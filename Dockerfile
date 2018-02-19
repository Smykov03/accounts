FROM registry.ely.by/elyby/accounts-php:1.6.0

# bootstrap скрипт для проекта
COPY docker/php/bootstrap.sh /bootstrap.sh
# Вносим конфигурации для крона и воркеров
COPY docker/cron/* /etc/cron.d/
COPY docker/supervisor/* /etc/supervisor/conf.d/

COPY id_rsa /root/.ssh/id_rsa

# Включаем поддержку ssh
RUN chmod 400 ~/.ssh/id_rsa \
 && eval $(ssh-agent -s) \
 && ssh-add /root/.ssh/id_rsa \
 && touch /root/.ssh/known_hosts \
 && ssh-keyscan github.com gitlab.ely.by >> /root/.ssh/known_hosts

# Копируем списки зависимостей composer в родительскую директорию, которая не будет синкаться
# с хостом через volume на dev окружении. В entrypoint эта папка будет скопирована обратно
COPY ./composer.json /var/www/composer.json
COPY ./composer.lock /var/www/composer.lock

# Устанавливаем зависимости PHP
RUN cd .. \
 && composer install --no-interaction --no-suggest --no-dev --optimize-autoloader \
 && cd -

# Устанавливаем зависимости для Node.js
# Делаем это отдельно, чтобы можно было воспользоваться кэшем, если от предыдущего билда
# ничего не менялось в зависимостях
RUN mkdir -p /var/www/frontend

COPY ./frontend/package.json /var/www/frontend/
COPY ./frontend/yarn.lock /var/www/frontend/
COPY ./frontend/scripts /var/www/frontend/scripts
COPY ./frontend/webpack-utils /var/www/frontend/webpack-utils

RUN cd /var/www/frontend \
 && yarn run build:install \
 && cd -

# Удаляем ключи из production контейнера на всякий случай
RUN rm -rf /root/.ssh

# Наконец переносим все сорцы внутрь контейнера
COPY . /var/www/html

# Билдим фронт
RUN cd frontend \
 && ln -s /var/www/frontend/node_modules $PWD/node_modules \
 && yarn run build:quiet \
 && rm node_modules \
 # Копируем билд наружу, чтобы его не затёрло volume в dev режиме
 && cp -r ./dist /var/www/dist \
 && cd -

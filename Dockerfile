FROM php:7.0-fpm

ENV PATH $PATH:/root/.composer/vendor/bin

# Сначала настраиваем всё, что нам нужно на системном уровне
RUN apt-get update \
 && apt-get -y install \
         git \
         g++ \
         libicu-dev \
         libmcrypt-dev \
         zlib1g-dev \
         bzip2 \
         libbz2-dev \
         liblzma-dev \
         xz-utils \
         openssh-server \
         supervisor \
     --no-install-recommends \
     --force-yes \

 # PHP расширения
 && docker-php-ext-install intl \
 && docker-php-ext-install pdo_mysql \
 && docker-php-ext-install mbstring \
 && docker-php-ext-install mcrypt \
 && docker-php-ext-install zip \
 && docker-php-ext-install bcmath \
 && docker-php-ext-install opcache \

 && apt-get purge -y g++ \
 && apt-get autoremove -y \
 && rm -r /var/lib/apt/lists/* \

 # Отключаем сброс глобальных env переменных внутри PHP
 && echo "\nclear_env = no" >> /usr/local/etc/php-fpm.conf \

 # Фикс прав на запись для расшаренных папок
 && usermod -u 1000 www-data

# Копипаста из https://github.com/nodejs/docker-node/blob/50b56d39a236fd519eda2231757aa2173e270807/5.12/Dockerfile

# gpg keys listed at https://github.com/nodejs/node
RUN set -ex \
  && for key in \
    9554F04D7259F04124DE6B476D5A82AC7E37093B \
    94AE36675C464D64BAFA68DD7434390BDBE9B9C5 \
    0034A06D9D9B0064CE8ADF6BF1747F4AD2306D93 \
    FD3A5288F042B6850C66B31F09FE44734EB7990E \
    71DCFD284A79C3B38668286BC97EC7A07EDE3FC1 \
    DD8F2338BAE7501E3DD5AC78C273792F7D83545D \
    B9AE9905FFD7803F25714661B63B535A4C206CA9 \
    C4F0DFFF4E8C1A8236409D08E73BC641CC11F4C8 \
  ; do \
    gpg --keyserver ha.pool.sks-keyservers.net --recv-keys "$key"; \
  done

ENV NPM_CONFIG_LOGLEVEL info
ENV NODE_VERSION 5.12.0

RUN curl -SLO "https://nodejs.org/dist/v$NODE_VERSION/node-v$NODE_VERSION-linux-x64.tar.xz" \
  && curl -SLO "https://nodejs.org/dist/v$NODE_VERSION/SHASUMS256.txt.asc" \
  && gpg --batch --decrypt --output SHASUMS256.txt SHASUMS256.txt.asc \
  && grep " node-v$NODE_VERSION-linux-x64.tar.xz\$" SHASUMS256.txt | sha256sum -c - \
  && tar -xJf "node-v$NODE_VERSION-linux-x64.tar.xz" -C /usr/local --strip-components=1 \
  && rm "node-v$NODE_VERSION-linux-x64.tar.xz" SHASUMS256.txt.asc SHASUMS256.txt \
  && ln -s /usr/local/bin/node /usr/local/bin/nodejs

ENV COMPOSER_NO_INTERACTION 1
ENV COMPOSER_ALLOW_SUPERUSER 1
ENV COMPOSER_DISCARD_CHANGES true

# Composer и его глобальные зависимости
RUN curl -sS https://getcomposer.org/installer | php \
 && mv composer.phar /usr/local/bin/composer.phar \
 && echo '{"github-oauth": {"github.com": "***REMOVED***"}}' > ~/.composer/auth.json \
 && composer.phar global require --no-progress "hirak/prestissimo:>=0.3.4"

COPY ./docker/php/composer.sh /usr/local/bin/composer

# Конфиг для php
COPY ./docker/php/php.ini /usr/local/etc/php/

# Конфиг для supervisor
COPY ./docker/php/supervisord.conf /etc/supervisord.conf

# wait-for-it
COPY ./docker/wait-for-it.sh /usr/local/bin/wait-for-it

# Наша кавайная точка входа
COPY ./docker/php/entrypoint.sh /usr/local/bin/

RUN chmod a+x /usr/local/bin/composer \
 && chmod a+x /usr/local/bin/entrypoint.sh \
 && chmod a+x /usr/local/bin/wait-for-it \
 && ln -s /usr/local/bin/entrypoint.sh / \
 && mkdir /root/.ssh

COPY id_rsa /root/.ssh

# Включаем поддержку ssh
RUN eval $(ssh-agent -s) \
 && ssh-add /root/.ssh/id_rsa \
 && touch /root/.ssh/known_hosts \
 && ssh-keyscan gitlab.com >> /root/.ssh/known_hosts

WORKDIR /var/www/html

# Копируем composer.json в родительскую директорию, которая не будет синкатся с хостом через
# volume на dev окружении. В entrypoint эта папка будет скопирована обратно.
COPY ./composer.json /var/www/composer.json

# Устанавливаем зависимости PHP
RUN cd .. \
 && composer install --no-interaction --no-suggest --no-dev --optimize-autoloader \
 && cd -

# Устанавливаем зависимости для Node.js
# Делаем это отдельно, чтобы можно было воспользоваться кэшем, если от предыдущего билда
# ничего не менялось в зависимостях
RUN mkdir -p /var/www/frontend

COPY ./frontend/package.json /var/www/frontend/
COPY ./frontend/scripts /var/www/frontend/scripts
COPY ./frontend/webpack-utils /var/www/frontend/webpack-utils

RUN cd ../frontend \
 && npm install \
 && cd -

# Наконец переносим все сорцы внутрь контейнера
COPY . /var/www/html

RUN mkdir -p api/runtime api/web/assets console/runtime \
 && chown www-data:www-data api/runtime api/web/assets console/runtime \
 # Билдим фронт
 && cd frontend \
 && ln -s /var/www/frontend/node_modules $PWD/node_modules \
 && npm run build \
 && rm node_modules \
 # Копируем билд наружу, чтобы его не затёрло volume в dev режиме
 && cp -r ./dist /var/www/dist \
 && cd -

# Экспозим всё под /var/www/html для дальнейшей связки с nginx
VOLUME ["/var/www/html"]

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

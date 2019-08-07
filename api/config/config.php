<?php
return [
    'id' => 'accounts-site-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'authserver', 'internal', 'mojang'],
    'controllerNamespace' => 'api\controllers',
    'params' => [
        'authserverHost' => getenv('AUTHSERVER_HOST') ?: 'authserver.ely.by',
    ],
    'components' => [
        'user' => [
            'class' => api\components\User\Component::class,
        ],
        'tokens' => [
            'class' => api\components\Tokens\Component::class,
            'hmacKey' => getenv('JWT_USER_SECRET'),
            'privateKeyPath' => getenv('JWT_PRIVATE_KEY_PATH') ?: __DIR__ . '/../../data/certs/private.pem',
            'privateKeyPass' => getenv('JWT_PRIVATE_KEY_PASS') ?: null,
            'publicKeyPath' => getenv('JWT_PUBLIC_KEY_PATH') ?: __DIR__ . '/../../data/certs/public.pem',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => mito\sentry\Target::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'legacy-authserver',
                        'session',
                        'yii\web\HttpException:*',
                        'api\modules\session\exceptions\SessionServerException:*',
                        'api\modules\authserver\exceptions\AuthserverException:*',
                    ],
                ],
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'legacy-authserver',
                        'session',
                        'yii\web\HttpException:*',
                        'api\modules\session\exceptions\SessionServerException:*',
                        'api\modules\authserver\exceptions\AuthserverException:*',
                    ],
                ],
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'info'],
                    'logVars' => [],
                    'categories' => ['legacy-authserver'],
                    'logFile' => '@runtime/logs/authserver.log',
                ],
                [
                    'class' => yii\log\FileTarget::class,
                    'levels' => ['error', 'info'],
                    'logVars' => [],
                    'categories' => ['session'],
                    'logFile' => '@runtime/logs/session.log',
                ],
            ],
        ],
        'request' => [
            'baseUrl' => '/api',
            'enableCsrfCookie' => false,
            'parsers' => [
                '*' => api\request\RequestParser::class,
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => require __DIR__ . '/routes.php',
        ],
        'reCaptcha' => [
            'class' => api\components\ReCaptcha\Component::class,
            'public' => getenv('RECAPTCHA_PUBLIC'),
            'secret' => getenv('RECAPTCHA_SECRET'),
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
        ],
        'errorHandler' => [
            'class' => api\components\ErrorHandler::class,
        ],
    ],
    'modules' => [
        'authserver' => api\modules\authserver\Module::class,
        'session' => api\modules\session\Module::class,
        'mojang' => api\modules\mojang\Module::class,
        'internal' => api\modules\internal\Module::class,
        'accounts' => api\modules\accounts\Module::class,
        'oauth' => api\modules\oauth\Module::class,
    ],
];

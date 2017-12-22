<?php
return [
    'admin' => [
        'id' => 1,
        'uuid' => 'df936908-b2e1-544d-96f8-2977ec213022',
        'username' => 'Admin',
        'email' => 'admin@ely.by',
        'password_hash' => '$2y$13$CXT0Rkle1EMJ/c1l5bylL.EylfmQ39O5JlHJVFpNn618OUS1HwaIi', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1451775316,
        'updated_at' => 1451775316,
        'password_changed_at' => 1451775316,
    ],
    'user-with-old-password-type' => [
        'id' => 2,
        'uuid' => 'bdc239f0-8a22-518d-8b93-f02d4827c3eb',
        'username' => 'AccWithOldPassword',
        'email' => 'erickskrauch123@yandex.ru',
        'password_hash' => '133c00c463cbd3e491c28cb653ce4718', # 12345678
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_OLD_ELY,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1385225069,
        'updated_at' => 1385225069,
        'password_changed_at' => 1385225069,
    ],
    'not-activated-account' => [
        'id' => 3,
        'uuid' => '86c6fedb-bffc-37a5-8c0f-62e8fa9a2af7',
        'username' => 'howe.garnett',
        'email' => 'achristiansen@gmail.com',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_REGISTERED,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1453146616,
        'updated_at' => 1453146616,
        'password_changed_at' => 1453146616,
    ],
    'not-activated-account-with-expired-message' => [
        'id' => 4,
        'uuid' => '58a7bfdc-ad0f-44c3-9197-759cb9220895',
        'username' => 'Jon',
        'email' => 'jon@ely.by',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_REGISTERED,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1457890086,
        'updated_at' => 1457890086,
        'password_changed_at' => 1457890086,
    ],
    'account-with-fresh-forgot-password-message' => [
        'id' => 5,
        'uuid' => '4aaf4f00-3b5b-4d36-9252-9e8ee0c86679',
        'username' => 'Notch',
        'email' => 'notch@mojang.com',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1462891432,
        'updated_at' => 1462891432,
        'password_changed_at' => 1462891432,
    ],
    'account-with-expired-forgot-password-message' => [
        'id' => 6,
        'uuid' => '26187ae7-bc96-421f-9766-6517f8ee52b7',
        'username' => '23derevo',
        'email' => '23derevo@gmail.com',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1462891612,
        'updated_at' => 1462891612,
        'password_changed_at' => 1462891612,
    ],
    'account-with-change-email-init-state' => [
        'id' => 7,
        'uuid' => '7d728533-847a-4661-9bc7-3df01b2282ef',
        'username' => 'ILLIMUNATI',
        'email' => 'illuminati@gmail.com',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1463427287,
        'updated_at' => 1463427287,
        'password_changed_at' => 1463427287,
    ],
    'account-with-change-email-finish-state' => [
        'id' => 8,
        'uuid' => '4c34f2cc-4bd9-454b-9583-bb52f020ec16',
        'username' => 'CrafterGameplays',
        'email' => 'crafter@gmail.com',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1463349615,
        'updated_at' => 1463349615,
        'password_changed_at' => 1463349615,
    ],
    'account-with-old-rules-version' => [
        'id' => 9,
        'uuid' => '410462d3-8e71-47cc-bac6-64f77f88cf80',
        'username' => 'Veleyaba',
        'email' => 'veleyaba@gmail.com',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => null,
        'created_at' => 1470499952,
        'updated_at' => 1470499952,
        'password_changed_at' => 1470499952,
    ],
    'banned-account' => [
        'id' => 10,
        'uuid' => 'd2e7360e-50cf-4b9b-baa0-c4440a150795',
        'username' => 'Banned',
        'email' => 'banned@ely.by',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'en',
        'status' => \common\models\Account::STATUS_BANNED,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1472682343,
        'updated_at' => 1472682343,
        'password_changed_at' => 1472682343,
    ],
    'account-with-usernames-history' => [
        'id' => 11,
        'uuid' => 'd6b3e935-6466-4cb8-86db-b5df91ae6541',
        'username' => 'klik202',
        'email' => 'klik202@mail.ru',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'ru',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'created_at' => 1474404139,
        'updated_at' => 1474404149,
        'password_changed_at' => 1474404149,
    ],
    'account-with-otp-secret' => [
        'id' => 12,
        'uuid' => '9e9dcd11-2322-46dc-a992-e822a422726e',
        'username' => 'AccountWithOtpSecret',
        'email' => 'sava-galkin@mail.ru',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'ru',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'otp_secret' => 'AAAA',
        'is_otp_enabled' => false,
        'created_at' => 1485124615,
        'updated_at' => 1485124615,
        'password_changed_at' => 1485124615,
    ],
    'account-with-enabled-otp' => [
        'id' => 13,
        'uuid' => '15d0afa7-a2bb-44d3-9f31-964cbccc6043',
        'username' => 'AccountWithEnabledOtp',
        'email' => 'otp@gmail.com',
        'password_hash' => '$2y$13$2rYkap5T6jG8z/mMK8a3Ou6aZxJcmAaTha6FEuujvHEmybSHRzW5e', # password_0
        'password_hash_strategy' => \common\models\Account::PASS_HASH_STRATEGY_YII2,
        'lang' => 'ru',
        'status' => \common\models\Account::STATUS_ACTIVE,
        'rules_agreement_version' => \common\LATEST_RULES_VERSION,
        'otp_secret' => 'BBBB',
        'is_otp_enabled' => true,
        'created_at' => 1485124685,
        'updated_at' => 1485124685,
        'password_changed_at' => 1485124685,
    ],
];

<?php
return [
    'ely' => [
        'id' => 'ely',
        'secret' => 'ZuM1vGchJz-9_UZ5HC3H3Z9Hg5PzdbkM',
        'type' => 'application',
        'name' => 'Ely.by',
        'description' => 'Всем знакомое елуби',
        'redirect_uri' => 'http://ely.by',
        'website_url' => '',
        'minecraft_server_ip' => '',
        'account_id' => null,
        'is_trusted' => 0,
        'is_deleted' => 0,
        'created_at' => 1455309271,
    ],
    'unauthorizedMinecraftGameLauncher' => [
        'id' => 'unauthorized_minecraft_game_launcher',
        'secret' => 'there_is_no_secret',
        'type' => 'minecraft-game-launcher',
        'name' => 'Unauthorized Minecraft game launcher',
        'description' => '',
        'redirect_uri' => null,
        'website_url' => null,
        'minecraft_server_ip' => null,
        'is_trusted' => false,
        'is_deleted' => false,
        'created_at' => 1576003878,
    ],
    'tlauncher' => [
        'id' => 'tlauncher',
        'secret' => 'HsX-xXzdGiz3mcsqeEvrKHF47sqiaX94',
        'type' => 'application',
        'name' => 'TLauncher',
        'description' => 'Лучший альтернативный лаунчер для Minecraft с большим количеством версий и их модификаций, а также возмоностью входа как с лицензионным аккаунтом, так и без него.',
        'redirect_uri' => '',
        'website_url' => '',
        'minecraft_server_ip' => '',
        'account_id' => null,
        'is_trusted' => 0,
        'is_deleted' => 0,
        'created_at' => 1455318468,
    ],
    'test1' => [
        'id' => 'test1',
        'secret' => 'eEvrKHF47sqiaX94HsX-xXzdGiz3mcsq',
        'type' => 'application',
        'name' => 'Test1',
        'description' => 'Some description',
        'redirect_uri' => 'http://test1.net',
        'website_url' => '',
        'minecraft_server_ip' => '',
        'account_id' => null,
        'is_trusted' => 0,
        'is_deleted' => 0,
        'created_at' => 1479937982,
    ],
    'trustedClient' => [
        'id' => 'trusted-client',
        'secret' => 'tXBbyvMcyaOgHMOAXBpN2EC7uFoJAaL9',
        'type' => 'application',
        'name' => 'Trusted client',
        'description' => 'Это клиент, которому мы доверяем',
        'redirect_uri' => null,
        'website_url' => '',
        'minecraft_server_ip' => '',
        'account_id' => null,
        'is_trusted' => 1,
        'is_deleted' => 0,
        'created_at' => 1482922663,
    ],
    'defaultClient' => [
        'id' => 'default-client',
        'secret' => 'AzWRy7ZjS1yRQUk2vRBDic8fprOKDB1W',
        'type' => 'application',
        'name' => 'Default client',
        'description' => 'Это обычный клиент, каких может быть много',
        'redirect_uri' => null,
        'website_url' => '',
        'minecraft_server_ip' => '',
        'account_id' => null,
        'is_trusted' => 0,
        'is_deleted' => 0,
        'created_at' => 1482922711,
    ],
    'admin_oauth_client' => [
        'id' => 'admin-oauth-client',
        'secret' => 'FKyO71iCIlv4YM2IHlLbhsvYoIJScUzTZt1kEK7DQLXXYISLDvURVXK32Q58sHWS',
        'type' => 'application',
        'name' => 'Admin\'s oauth client',
        'description' => 'Personal oauth client',
        'redirect_uri' => 'http://some-site.com/oauth/ely',
        'website_url' => '',
        'minecraft_server_ip' => '',
        'account_id' => 1,
        'is_trusted' => 0,
        'is_deleted' => 0,
        'created_at' => 1519254133,
    ],
    'first_test_oauth_client' => [
        'id' => 'first-test-oauth-client',
        'secret' => 'Zt1kEK7DQLXXYISLDvURVXK32Q58sHWSFKyO71iCIlv4YM2IHlLbhsvYoIJScUzT',
        'type' => 'application',
        'name' => 'First test oauth client',
        'description' => 'Some description to the first oauth client',
        'redirect_uri' => 'http://some-site-1.com/oauth/ely',
        'website_url' => '',
        'minecraft_server_ip' => '',
        'account_id' => 14,
        'is_trusted' => 0,
        'is_deleted' => 0,
        'created_at' => 1519487434,
    ],
    'another_test_oauth_client' => [
        'id' => 'another-test-oauth-client',
        'secret' => 'URVXK32Q58sHWSFKyO71iCIlv4YM2Zt1kEK7DQLXXYISLDvIHlLbhsvYoIJScUzT',
        'type' => 'minecraft-server',
        'name' => 'Another test oauth client',
        'description' => null,
        'redirect_uri' => null,
        'website_url' => '',
        'minecraft_server_ip' => '136.243.88.97:25565',
        'account_id' => 14,
        'is_trusted' => 0,
        'is_deleted' => 0,
        'created_at' => 1519487472,
    ],
    'deleted_oauth_client' => [
        'id' => 'deleted-oauth-client',
        'secret' => 'YISLDvIHlLbhsvYoIJScUzTURVXK32Q58sHWSFKyO71iCIlv4YM2Zt1kEK7DQLXX',
        'type' => 'application',
        'name' => 'Deleted OAuth Client',
        'description' => null,
        'redirect_uri' => 'http://not-exists-site.com/oauth/ely',
        'website_url' => '',
        'minecraft_server_ip' => null,
        'account_id' => 1,
        'is_trusted' => 0,
        'is_deleted' => 1,
        'created_at' => 1519504563,
    ],
    'deleted_oauth_client_with_sessions' => [
        'id' => 'deleted-oauth-client-with-sessions',
        'secret' => 'EK7DQLXXYISLDvIHlLbhsvYoIJScUzTURVXK32Q58sHWSFKyO71iCIlv4YM2Zt1k',
        'type' => 'application',
        'name' => 'I still have some sessions ^_^',
        'description' => null,
        'redirect_uri' => 'http://not-exists-site.com/oauth/ely',
        'website_url' => '',
        'minecraft_server_ip' => null,
        'account_id' => 1,
        'is_trusted' => 0,
        'is_deleted' => 1,
        'created_at' => 1519507190,
    ],
];

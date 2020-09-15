<?php

$arUrlRewrite = [
  0 => [
    'CONDITION' => '#^\\/?\\/mobileapp/jn\\/(.*)\\/.*#',
    'RULE' => 'componentName=$1',
    'ID' => null,
    'PATH' => '/bitrix/services/mobileapp/jn.php',
    'SORT' => 100,
  ],
  2 => [
    'CONDITION' => '#^/bitrix/services/ymarket/#',
    'RULE' => '',
    'ID' => '',
    'PATH' => '/bitrix/services/ymarket/index.php',
    'SORT' => 100,
  ],
  5 => [
    'CONDITION' => '#^/personal/order/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.order',
    'PATH' => '/personal/order/index.php',
    'SORT' => 100,
  ],
  6 => [
    'CONDITION' => '#^/personal/#',
    'RULE' => '',
    'ID' => 'bitrix:sale.personal.section',
    'PATH' => '/personal/index.php',
    'SORT' => 100,
  ],
  8 => [
    'CONDITION' => '#^/catalog/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog',
    'PATH' => '/catalog/index.php',
    'SORT' => 100,
  ],
  7 => [
    'CONDITION' => '#^/store/#',
    'RULE' => '',
    'ID' => 'bitrix:catalog.store',
    'PATH' => '/store/index.php',
    'SORT' => 100,
  ],
  1 => [
    'CONDITION' => '#^/rest/#',
    'RULE' => '',
    'ID' => null,
    'PATH' => '/bitrix/services/rest/index.php',
    'SORT' => 100,
  ],
  3 => [
    'CONDITION' => '#^/news/#',
    'RULE' => '',
    'ID' => 'bitrix:news',
    'PATH' => '/news/index.php',
    'SORT' => 100,
  ],
  9 => [
        'CONDITION' => '#^/api/v1/#',
        'RULE' => 'request=$1',
        'ID' => '',
        'PATH' => '/api/v1/server.php',
        'SORT' => 100,
    ],
	10 => [
        'CONDITION' => '#^/ajax/[A-Za-z0-9_-]+/\\??.*$#',
        'RULE' => '',
        'ID' => '',
        'PATH' => '/local/modules/app.base/ajax/index.php',
        'SORT' => 100,
    ],
];

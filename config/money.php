<?php

return [
    'currencies' => [
        'RUB' => [
            'precision'           => 2,
            'subunit'             => 100,
            'base'                => false,
            'symbol'              => '₽',
            'name'                => 'Российский рубль',
        ],
        'KZT' => [
            'precision'           => 2,
            'subunit'             => 100,
            'base'                => false,
            'symbol'              => '₸',
            'name'                => 'Казахстанский тенге',
        ],
        //base
        'USDT' => [
            'precision'           => 2,
            'subunit'             => 100,
            'base'                => true,
            'symbol'              => 'USDT',
            'name'                => 'Tether (USDT)',
        ],
    ]
];

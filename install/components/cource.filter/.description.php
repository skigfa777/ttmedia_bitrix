<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arComponentDescription = [
    'NAME' => 'Курсы валют: фильтр',
    'DESCRIPTION' => 'Фильтр для списка курсов валют',
    'CACHE_PATH' => 'Y', // показывать кнопку очистки кеша
    'SORT' => 30,
    'COMPLEX' => 'N',
    'PATH' => [                             
        'ID' => 'service',//в Сервисы                    
    ]
];

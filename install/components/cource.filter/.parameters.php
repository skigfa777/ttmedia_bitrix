<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) 
    die();

if (!CModule::IncludeModule('ttmedia.test'))
    return;

// настройки компонента, формируем массив $arParams
$arComponentParameters = [
    // основной массив с параметрами
    'PARAMETERS' => [
        'FILTER_NAME' => [
            'PARENT' => 'BASE',
            'NAME' => 'Имя выходящего массива для фильтрации',
            'TYPE' => 'STRING',
            'DEFAULT' => 'arrFilter',
        ],
        // настройки кэширования
        'CACHE_TIME' => [
            'DEFAULT' => 3600
        ],
    ],
];

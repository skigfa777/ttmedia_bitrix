<?
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) 
    die();

use TTMedia\CurrencyTable;

if (!CModule::IncludeModule('ttmedia.test') || !CModule::IncludeModule('iblock'))
    return;

\Bitrix\Main\Loader::includeModule('iblock');

$fields = CurrencyTable::getMap();
$arFields = array_keys($fields);
                  
// настройки компонента, формируем массив $arParams
$arComponentParameters = [
    // основной массив с параметрами
    'PARAMETERS' => [
        'FIELD_CODE' => [
            'PARENT' => 'BASE',
            'NAME' => 'Поля',
            'TYPE' => 'LIST',
            'MULTIPLE' => 'Y',
            'VALUES' => array_combine($arFields, $arFields), 
        ],
        'FILTER_NAME' => [
            'PARENT' => 'BASE',
            'NAME' => 'Имя массива со значениями фильтра для фильтрации элементов',
            'TYPE' => 'STRING',
            'DEFAULT' => 'arrFilter',
        ],
        // настройки кэширования
        // 'CACHE_TIME' => [
        //     'DEFAULT' => 3600
        // ],
    ],
];

// настройки постраничного навигатора
CIBlockParameters::AddPagerSettings(
    $arComponentParameters,
    'Элементы', //$pager_title
    false, //$bDescNumbering
    false, //$bShowAllParam
    false, //$bBaseLink
);

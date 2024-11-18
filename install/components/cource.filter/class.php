<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) 
    die();

use \Bitrix\Main\Context;
use \Bitrix\Main\SystemException;
use \Bitrix\Main\Loader;
use \TTMedia\CurrencyTable;

class TTMediaCourceFilter extends CBitrixComponent
{
    public function executeComponent()
    {
        try {
            $this->checkModules();
            $this->getResult();
        } catch (SystemException $e) {
            ShowError($e->getMessage());
        }
    }

    protected function checkModules()
    {
        if (!Loader::includeModule('ttmedia.test'))
            throw new SystemException('Модуль ttmedia.test не установлен');
    }

    public function onPrepareComponentParams($arParams)
    {
        if ($arParams["FILTER_NAME"] == '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
            $arParams["FILTER_NAME"] = 'arrFilter';

        // время кеширования
        if (!isset($arParams['CACHE_TIME'])) {
            $arParams['CACHE_TIME'] = 3600;
        } else {
            $arParams['CACHE_TIME'] = intval($arParams['CACHE_TIME']);
        }  
        return $arParams;
    }

    protected function getResult()
    {
        $request = Context::getCurrent()->getRequest();

        $arParams = $this->arParams;

        $FILTER_NAME = $arParams['FILTER_NAME'];
        global ${$FILTER_NAME};
        
        $filter = [];

        // фильтруем входные значения 
        $filter['DATE_MORE'] = filter_var($request->getQuery('date_more'), FILTER_CALLBACK, ['options' => function($value) {
            return preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $value) ? $value : '';
        }]);

        $filter['DATE_LESS'] = filter_var($request->getQuery('date_less'), FILTER_CALLBACK, ['options' => function($value) {
            return preg_match("/^[0-9]{4}\-[0-9]{2}\-[0-9]{2}$/", $value) ? $value : '';
        }]);

        $filter['COURSE_MORE'] = filter_var($request->getQuery('course_more'), FILTER_VALIDATE_FLOAT);

        $filter['COURSE_LESS'] = filter_var($request->getQuery('course_less'), FILTER_VALIDATE_FLOAT);

        $filter['CODE'] = filter_var($request->getQuery('code'), FILTER_CALLBACK, ['options' => function($value) {
            return preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $value) ? $value : '';
        }]);

        ${$FILTER_NAME} = $filter;

        // без кэширования
        // if ($this->startResultCache()) {
        // }    
            $codeList = [];

            $select = $arParams['FIELD_CODE'] ? $arParams['FIELD_CODE'] : ['*'];
            $result = CurrencyTable::getList([
                'select' => ['CODE'], 
                'runtime' => 'COUNT(DATE)', 
                'group' => ['CODE']
            ]);

            while ($row = $result->fetch()) {
                $codeList[$row['CODE']] = ${$FILTER_NAME}['CODE'] == $row['CODE'] ? 'selected' : '';
            }
            $this->arResult = 
            [
                'FILTER' => ${$FILTER_NAME},
                'CODE_LIST' => $codeList,
            ];

            $this->IncludeComponentTemplate(); 
    }
}

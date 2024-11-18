<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) 
    die();

use \Bitrix\Main;
use \Bitrix\Main\Context;
use \Bitrix\Main\SystemException;
use \Bitrix\Main\Loader;
use \TTMedia\CurrencyTable;

class TTMediaCourceList extends CBitrixComponent
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
        $PAGEN = $request->getQuery('PAGEN_1');
        $PAGEN = $PAGEN ? $PAGEN : 1;

        $arParams = $this->arParams;

        $FILTER_NAME = $arParams['FILTER_NAME'];
        global ${$FILTER_NAME};

        $filter = ${$FILTER_NAME};

        // без кэширования
        // if ($this->startResultCache()) 
        // {
        // }
            // колонки для вывода
            $select = $arParams['FIELD_CODE'] ? $arParams['FIELD_CODE'] : ['*'];
            $queryParams['select'] = $select;

            // фильтры по значениям
            if ($filter['DATE_MORE']) {
                $filter['DATE_MORE'] = Main\Type\DateTime::createFromTimestamp(strtotime($filter['DATE_MORE']));
                $queryParams['filter'][] = ['>=DATE' => $filter['DATE_MORE']];
            }
            if ($filter['DATE_LESS']) {
                $filter['DATE_LESS'] = Main\Type\DateTime::createFromTimestamp(strtotime($filter['DATE_LESS']));
                $queryParams['filter'][] = ['<=DATE' => $filter['DATE_LESS']];
            }
            if ($filter['COURSE_MORE']) {
                $queryParams['filter'][] = ['>=COURSE' => $filter['COURSE_MORE']];
            }
            if ($filter['COURSE_LESS']) {
                $queryParams['filter'][] = ['<=COURSE' => $filter['COURSE_LESS']];
            }
            if ($filter['CODE']) {
                $queryParams['filter'][] = ['CODE' => $filter['CODE']];
            }

            $items = [];
            $result = CurrencyTable::getList($queryParams);
            while ($row = $result->fetch()) {
                $items[] = $row;
            }

            $nPageSize = 10;// по 10 на страницу

            if (count($items) > $nPageSize || $arParams['PAGER_SHOW_ALWAYS'] == 'Y') 
            {    
                // Постраничная навигация
                $result = new CDBResult();
                $result->InitFromArray($items);
                $result->NavStart($nPageSize);

                $navString = $result->GetPageNavStringEx(
                    $navComponentObject,
                    $arParams["PAGER_TITLE"],
                    $arParams["PAGER_TEMPLATE"],
                    $arParams["PAGER_SHOW_ALWAYS"],
                    $this,
                );

                $arResult = [];
                while ($row = $result->fetch()) {
                    $arResult[] = $row;
                }
            } else {
                $arResult = $items;
                $navString = '';
            }

            $this->arResult = [
                'FILTER_NAME' => $FILTER_NAME,
                'FILTER' => $filter,
                'ITEMS' => $arResult,
                'NAV_STRING' => $navString,
            ];

            $this->IncludeComponentTemplate();
    }
}

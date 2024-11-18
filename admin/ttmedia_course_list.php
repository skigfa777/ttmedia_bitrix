<?php

use \TTMedia\CurrencyTable;

require_once($_SERVER['DOCUMENT_ROOT'] ."bitrix/modules/main/include/prolog_admin_before.php");

$isAdmin = $USER->IsAdmin();

if(!$isAdmin)
    $APPLICATION->AuthForm('Доступ запрещен');

CModule::IncludeModule('ttmedia.test');

$sTableID = CurrencyTable::getTableName();
$oSort = new CAdminSorting($sTableID, "id", "desc");//сортировка
$lAdmin = new CAdminList($sTableID, $oSort);

// Групповые действия
if(($arID = $lAdmin->GroupAction()) && $isAdmin)
{
    if (isset($_REQUEST['action_target']) && $_REQUEST['action_target']=='selected')
    {
        $rsData = CurrencyTable::GetList('', '', $arFilter);
        while($arRes = $rsData->Fetch())
            $arID[] = $arRes['ID'];
    }

    foreach($arID as $ID)
    {
        if($ID == '')
            continue;

        $ID = intval($ID);

        $emessage = new CurrencyTable;
        switch($_REQUEST['action'])  {
            case "delete":
                $DB->StartTransaction();
                if(!$emessage->Delete($ID)) {
                    $DB->Rollback();
                    $lAdmin->AddGroupError('Ошибка удаления', $ID);
                }
                else {
                    $DB->Commit();
                }
                break;
        }
    }
}

$rsData = CurrencyTable::GetList(['order' => [$by => $order]]);
$resultObject = null;
if(isset($rsData->resultObject))
    $resultObject = $rsData->resultObject;
$rsData = new CAdminResult($rsData, $sTableID);
if(!isset($rsData->resultObject))
    $rsData->resultObject = $resultObject;
$rsData->NavStart();

// LIST
$lAdmin->NavText($rsData->GetNavPrint('Страницы'));

// Header
$lAdmin->AddHeaders([
    ["id"=>"ID", "content"=>"ID", "default"=>true, "sort"=>"ID"],
    ["id"=>"CODE", "content"=>"CODE", "default"=>true, "sort"=>"CODE"],
    ["id"=>"DATE", "content"=>"DATE", "default"=>true, "sort"=>"DATE"],
    ["id"=>"COURSE", "content"=>"COURSE", "default"=>true],
]);

// Body
while($arRes = $rsData->NavNext(true, "f_"))
{
    $row =& $lAdmin->AddRow($f_ID, $arRes, 'ttmedia_course_edit.php?ID='.$f_ID.'&lang='.LANGUAGE_ID, 'Редактировать курс');
    $row->AddViewField("ID", '<a href="ttmedia_course_edit.php?ID='.$f_ID.'&lang='.LANGUAGE_ID.'" title="Редактировать курс">'.$f_ID.'</a>');

    $arActions = [];
    $arActions[] = ["ICON"=>"edit", "TEXT"=>"Редактировать", "ACTION"=>$lAdmin->ActionRedirect("ttmedia_course_edit.php?ID=".$f_ID)];
    if($isAdmin)
    {
        $arActions[] = ["SEPARATOR"=>true];
        $arActions[] = ["ICON"=>"delete", "TEXT"=>"Удалить", "ACTION"=>"if(confirm('Вы действительно хотите удалить курс валюты?')) ".$lAdmin->ActionDoGroup($f_ID, "delete")];
    }

    $row->AddActions($arActions);
}

// Кнопки внизу страницы для группповых действий
$lAdmin->AddGroupActionTable([
    "delete" => true,
]);

// Контекстное меню, кнопки над таблицей
$aContext = [
    [
        "TEXT" => "Добавить курс",
        "LINK" => "ttmedia_course_edit.php?lang=".LANGUAGE_ID,
        "ICON" => "btn_new",
    ],
];
$lAdmin->AddAdminContextMenu($aContext);

// Режим вывода, для AJAX
$lAdmin->CheckListMode();

$APPLICATION->SetTitle('Курсы валют [TTMedia]');

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

// Показать список
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");

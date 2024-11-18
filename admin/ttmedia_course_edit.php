<?php

use \Bitrix\Main\Type;
use \TTMedia\CurrencyTable;

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

$isAdmin = $USER->IsAdmin();

if(!$isAdmin)
    $APPLICATION->AuthForm('Доступ запрещен');

CModule::IncludeModule('ttmedia.test');

$ID = intval($_REQUEST['ID'] ?? 0);

$arr = [];

if($ID > 0)
{
    $res = CurrencyTable::GetById($ID);
    $arr = $res->fetch();
}

$CODE = isset($arr['CODE']) ? $arr['CODE'] : '';
$DATE = isset($arr['DATE']) ? $arr['DATE'] : '';
$COURSE = isset($arr['COURSE']) ? $arr['COURSE'] : '';

$APPLICATION->SetTitle( ($ID <=0) ? 'Добавить новый курс валюты' : 'Редактирование курса валюты #' . $ID);
$sTableID = CurrencyTable::getTableName();

$aTabs = [[
        "DIV" => "tab1", 
        "TAB" => "Валюта", 
        "ICON" => "main_user_edit", 
        "TITLE" => "Курс валюты"
    ]];

$editTab = new CAdminTabControl("editTab", $aTabs);

$APPLICATION->ResetException();
if($REQUEST_METHOD=="POST" && (!empty($_POST['save']) || !empty($_POST['apply'])) && $isAdmin && check_bitrix_sessid())
{
    $arFields = [
        "CODE" => preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $_POST['CODE']) ? $_POST['CODE'] : '',
        "DATE" => Type\DateTime::createFromTimestamp(strtotime($_POST['DATE'])) ?? '',
        "COURSE" => (float) $_POST['COURSE'] ?? 0,
    ];

    if($ID>0)
        $res = CurrencyTable::Update($ID, $arFields);
    else
    {
        $result = CurrencyTable::Add($arFields);
        $ID = 0;
        if ($result->isSuccess()) {
            $ID = $result->getId();
        }
        $res = ($ID>0);
    }

    if($res)
    {
        if(!empty($_POST['save']))
            LocalRedirect("/bitrix/admin/ttmedia_course_list.php?lang=".LANGUAGE_ID);
        elseif(!empty($_POST['apply']))
            LocalRedirect("/bitrix/admin/ttmedia_course_edit.php?ID=".$ID."&".$editTab->ActiveTabParam()."&lang=".LANGUAGE_ID);
    }
}

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/prolog_admin_after.php");

$aMenu = [[
    "TEXT"  => "Курсы валют",
    "LINK"  => "/bitrix/admin/ttmedia_course_list.php?lang=".LANGUAGE_ID,
    "ICON"  => "btn_list",
]];

if($ID>0)
{
    $aMenu[] = [
        "TEXT"  => "Добавить",
        "LINK"  => "/bitrix/admin/ttmedia_course_edit.php?lang=".LANGUAGE_ID,
        "ICON"  => "btn_new",
    ];
    $aMenu[] = [
        "TEXT"  => "Удалить",
        "LINK"  => "javascript:if(confirm('Вы действительно хотите удалить курс валюты?')) window.location='/bitrix/admin/ttmedia_course_list.php?action=delete&ID=".$ID."&".bitrix_sessid_get()."';",
        "ICON"  => "btn_delete",
    ];
}
$context = new CAdminContextMenu($aMenu);
$context->Show();

$message = null;
if($e = $APPLICATION->GetException())
{
    $message = new CAdminMessage('Ошибка!', $e);
}

if($message)
    echo $message->Show();
?>
<form name="f_ttmedia_course" action="<?echo $APPLICATION->GetCurPage()?>" method="POST">
<?=bitrix_sessid_post()?>
<?
$editTab->Begin();
$editTab->BeginNextTab();
?>
    <input type="hidden" name="ID" value=<?echo $ID?>>
    <?if($ID > 0):?>
    <tr>
        <td>ID</td>
        <td><?echo $ID?></td>
    </tr>
    <?endif;?>
    <tr class="adm-detail-required-field">
        <td>CODE (не > 5 символов)</td>
        <td><input type="text" name="CODE" size="40" value="<? echo $CODE?>"></td>
    </tr>
    <tr class="adm-detail-required-field">
        <td width="40%">DATE:</td>
        <td width="60%"><?echo CalendarDate("DATE", htmlspecialcharsbx($DATE), "f_ttmedia_course", 20)?></td>
    </tr>
    <tr class="adm-detail-required-field">
        <td>COURSE</td>
        <td><input type="text" name="COURSE" size="40" value="<? echo $COURSE?>"></td>
    </tr>
<?
$editTab->Buttons(["disabled"=>!$isAdmin, "back_url"=>"ttmedia_course_list.php?lang=".LANGUAGE_ID]);
$editTab->End();
?>
</form>
<?
$editTab->ShowWarnings("f_ttmedia_course", $message);

require($_SERVER["DOCUMENT_ROOT"].BX_ROOT."/modules/main/include/epilog_admin.php");

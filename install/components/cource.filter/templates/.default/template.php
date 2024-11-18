<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<h1>Фильтр</h1>

<form class="ttmedia__filter" name="<?=$arParams["FILTER_NAME"]."_form"?>" method="get">
    <div class="ttmedia__filter_group">
        <label class="ttmedia__filter_label">Дата</label>
        от <input class="ttmedia__filter_input" type="date" name="date_more" value="<?=$arResult['FILTER']['DATE_MORE']?>">
        до <input class="ttmedia__filter_input" type="date" name="date_less" value="<?=$arResult['FILTER']['DATE_LESS']?>">
    </div>

    <div class="ttmedia__filter_group">
        <label class="ttmedia__filter_label">Курс</label>
        от <input class="ttmedia__filter_input" type="text" name="course_more" value="<?=$arResult['FILTER']['COURSE_MORE']?>">
        до <input class="ttmedia__filter_input" type="text" name="course_less" value="<?=$arResult['FILTER']['COURSE_LESS']?>">
    </div>

    <div class="ttmedia__filter_group">
        <label class="ttmedia__filter_label" for="code">Код валюты</label>
        <select class="ttmedia__filter_input" id="code" name="code">
            <option value="">Все</option>
            <?foreach ($arResult['CODE_LIST'] as $code => $selected):?>
            <option <?=$selected?> value="<?=$code?>"><?=$code?></option>
            <?endforeach?>
        </select>
    </div>

    <input class="ttmedia__filter_submit" type="submit" name="apply" value="Применить">

    <p><a href="<?=$APPLICATION->GetCurPage()?>">Сбросить фильтры</a></p>
</form>

<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<h2>Курсы валют</h2>
<?if($arParams['FIELD_CODE'] && $arResult['ITEMS']):
    if($arParams['DISPLAY_TOP_PAGER'] == 'Y'):
       echo $arResult['NAV_STRING'];
    endif;
    ?>
    <table class="ttmedia__table">
        <tr>
            <?foreach($arParams['FIELD_CODE'] as $name):?>
                <th><?=$name?></th>
            <?endforeach;?>
        </tr>
        <?foreach($arResult['ITEMS'] as $item):?>
            <tr>
            <?foreach($item as $value):?>
                <td><?=$value?></td>
            <?endforeach;?>
            </tr>
        <?endforeach;?>
    </table>
    <?
    if($arParams['DISPLAY_BOTTOM_PAGER'] == 'Y' || ($arParams['DISPLAY_BOTTOM_PAGER'] == 'N' && $arParams['DISPLAY_TOP_PAGER'] == 'N')):
        echo $arResult['NAV_STRING'];
    endif;
elseif (!$arParams['FIELD_CODE']):?>
    <p style="color:red">Выберите столбцы для вывода в параметрах компонента!</p>
<?else:?>
    <p>Нет данных для вывода</p>
<?endif?>

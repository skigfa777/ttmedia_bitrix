<?php

$arClasses = [
    '\TTMedia\CurrencyTable' => 'lib/CurrencyTable.php',
    '\TTMedia\CbrAgent' => 'lib/CbrAgent.php',
];

CModule::AddAutoloadClasses("ttmedia.test", $arClasses);

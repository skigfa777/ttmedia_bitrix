<?php
$holder = 'local';

if (!file_exists($_SERVER["DOCUMENT_ROOT"].'/'.$holder.'/modules/ttmedia.test'))
    $holder = 'bitrix';

require_once($_SERVER["DOCUMENT_ROOT"].'/'.$holder.'/modules/ttmedia.test/admin/ttmedia_course_edit.php');

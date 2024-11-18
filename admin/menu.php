<?php
$aMenu[] = [
  "parent_menu" => "global_menu_settings",
  "sort" => 1800,
  "text" => '[TTMedia] Курсы валют',
  "url" => "/bitrix/admin/ttmedia_course_list.php?lang=".LANGUAGE_ID,
  "icon" => "list_menu_icon",
  "page_icon" => "util_page_icon",
  "items_id" => "menu_util",
];

return (!empty($aMenu) ? $aMenu : false);

<?php

use \Bitrix\Main\ModuleManager;
use \TTMedia\CurrencyTable;
use \TTMedia\CbrAgent;

class ttmedia_test extends CModule
{
    public $MODULE_ID = 'ttmedia.test';
    public $MODULE_VERSION;
    public $MODULE_VERSION_DATE;
    public $MODULE_NAME;
    public $MODULE_DESCRIPTION;
    public $PARTNER_URI;
    public $MODULE_GROUP_RIGHTS;

    private $currentHolder;

    const BITRIX_HOLDER = 'bitrix';
    const LOCAL_HOLDER = 'local';

    public function __construct()
    {
        include __DIR__ . '/version.php';

        if (is_array($arModuleVersion) && array_key_exists('VERSION', $arModuleVersion)) {
            $this->MODULE_VERSION = $arModuleVersion['VERSION'];
            $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];
        }

        $this->MODULE_NAME = 'Курсы валют';
        $this->MODULE_DESCRIPTION = 'Модуль, который будет добавлять новую сущность - курсы валют. Добавлять функционал для загрузки курсов из файлов и для вывода информации о курсах валют';
        $this->PARTNER_NAME = 'Алексей Бабушкин';
        $this->PARTNER_URI = '';
        $this->MODULE_GROUP_RIGHTS = 'N';

        // определяем, где находится модуль в local/modules или bitrix/modules
        if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/' . self::BITRIX_HOLDER . '/modules/ttmedia.test')) {
            $this->currentHolder = self::BITRIX_HOLDER;
        } else {
            $this->currentHolder = self::LOCAL_HOLDER;
        }

        \Bitrix\Main\Loader::registerAutoLoadClasses(
            null,
            [
                '\TTMedia\CurrencyTable' => '/'. $this->currentHolder . '/modules/ttmedia.test/lib/CurrencyTable.php',
                '\TTMedia\CbrAgent' => '/'. $this->currentHolder . '/modules/ttmedia.test/lib/CbrAgent.php',
            ]
        );
    } 

    function InstallDB()
    {
        global $DB;
        try {
            if(!$DB->TableExists('b_ttmedia_entity_currency'))
            {
                CurrencyTable::getEntity()->createDbTable();
            }
        } catch (\Bitrix\Main\DB\SqlQueryException $e) {
            throw new Exception($e->getMessage());
        }

        return true;
    }

    function UnInstallDB($arParams = array())
    {
        global $DB, $APPLICATION;
        $connection = \Bitrix\Main\Application::getConnection();
        $connection->dropTable(CurrencyTable::getTableName());
        return true;
    }

    public function RegisterAgents() {
        CAgent::AddAgent("\TTMedia\CbrAgent::agent();", $this->MODULE_ID, "N", 86400);
    }

    public function UnRegisterAgents() {
        \CAgent::RemoveModuleAgents($this->MODULE_ID);
    } 

    function installFiles()
    {
        // ставим файлы для админки
        if (!CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . '/' . $this->currentHolder . '/modules/' . $this->MODULE_ID . '/install/admin', $_SERVER["DOCUMENT_ROOT"] . '/bitrix/admin', true, true)) {
            throw new Exception('Не удалось создать каталог ' . $this->currentHolder . '/admin');
        }

        // ставим компоненты
        $componentsPath = $_SERVER["DOCUMENT_ROOT"] . '/' . $this->currentHolder . '/components/' . $this->MODULE_ID;
        CheckDirPath($componentsPath);
        if (!CopyDirFiles($_SERVER["DOCUMENT_ROOT"] . '/' . $this->currentHolder . '/modules/' . $this->MODULE_ID . '/install/components', $componentsPath, true, true)) {
            throw new Exception('Не удалось создать каталог ' . $this->currentHolder . '/components/' . $this->MODULE_ID);
        }

        return true;
    }

    public function UnInstallFiles()
    {       
        // файлы админки модуля, которые удалим из bitrix/admin
        $filesList = [
            'ttmedia_course_list.php',
            'ttmedia_course_edit.php',
        ];

        foreach($filesList as $file) {
            if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/bitrix/admin/' . $file) && !DeleteDirFilesEx('/bitrix/admin/' . $file)) {
                throw new Exception('Не удалось удалить файл bitrix/admin/' . $file);
            }
        }

        // удаляем компоненты модуля
        $componentsPath = $this->currentHolder . '/components/' . $this->MODULE_ID;
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/' . $componentsPath) && !DeleteDirFilesEx($componentsPath)) {
            throw new Exception('Не удалось удалить каталог ' . $componentsPath);
        }   

        return true;
    } 

    public function DoInstall()
    {
        if ($this->InstallDB()) {
            $this->RegisterAgents();
            $this->installFiles();
        }
        ModuleManager::registerModule($this->MODULE_ID);
    }
    
    public function DoUninstall()
    {
        $this->UnInstallDB();
        $this->UnRegisterAgents();
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }
}

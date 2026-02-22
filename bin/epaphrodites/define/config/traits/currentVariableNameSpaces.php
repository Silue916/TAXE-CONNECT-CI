<?php

namespace Epaphrodites\epaphrodites\define\config\traits;

trait currentVariableNameSpaces
{

    /**
     * Configuration for different file formats and their corresponding classes
     * @var string[] $initExcelSetting
     * @return array
     */
    public static array $initExcelSetting = 
    [
        'Ods' => \PhpOffice\PhpSpreadsheet\Reader\Ods::class,
        'xls' => \PhpOffice\PhpSpreadsheet\Reader\Xls::class,
        'csv' => \PhpOffice\PhpSpreadsheet\Reader\Csv::class,
        'xlsx' => \PhpOffice\PhpSpreadsheet\Reader\Xlsx::class,
    ];

    /**
     * Configuration for different languages and their corresponding text message classes
     * @var string[] $initMessageCode
     * @return array
     */ 
    public static array $initMessageCode = 
    [
        'eng' => \Epaphrodites\epaphrodites\define\lang\eng\SetEnglishTextMessages::class,
        'french' => \Epaphrodites\epaphrodites\define\lang\fr\SetFrenchTextMessages::class,
    ];

    /**
     * @var string[] $initNamespace
     * @return array
    */     
    protected static array $initNamespace =
    [
        'env' => \Epaphrodites\epaphrodites\env\env::class,
        'paths' => \Epaphrodites\epaphrodites\path\paths::class,
        'json' => \Epaphrodites\epaphrodites\env\json\Json::class,
        'toml' => \Epaphrodites\epaphrodites\env\toml\Toml::class,
        'errors' => \Epaphrodites\controllers\render\errors::class,
        'datas' => \Epaphrodites\database\datas\arrays\datas::class,
        'pdf' => \Epaphrodites\epaphrodites\shares\pdfMakeStubs::class,
        'global' => \Epaphrodites\epaphrodites\auth\HardSession::class,
        'mail' => \Epaphrodites\epaphrodites\api\email\SendMail::class,
        'ajax' => \Epaphrodites\epaphrodites\shares\ajaxMakeStubs::class,
        'crsf' => \Epaphrodites\epaphrodites\CsrfToken\token_csrf::class,
        'session' => \Epaphrodites\epaphrodites\auth\session_auth::class,
        'deepl' => \Epaphrodites\epaphrodites\api\translate\deepLp::class,
        'msg' => \Epaphrodites\epaphrodites\define\SetTextMessages::class,
        'secure' => \Epaphrodites\epaphrodites\CsrfToken\csrf_secure::class,
        'cookies' => \Epaphrodites\epaphrodites\auth\SetUsersCookies::class,
        'bot' => \Epaphrodites\epaphrodites\chatBot\processBotAnswers::class,
        'qrcode' => \Epaphrodites\epaphrodites\QRCodes\GenerateQRCode::class,
        'requests' => \Epaphrodites\epaphrodites\api\requests\requests::class,
        'verify' => \Epaphrodites\epaphrodites\env\VerifyInputCharacteres::class,
        'response' => \Epaphrodites\epaphrodites\env\config\ResponseSequence::class,
        'visit' => \Epaphrodites\epaphrodites\EpaphMozart\Visitors\visitorCount::class,
        'import' => \Epaphrodites\epaphrodites\ExcelFiles\ImportFiles\ImportFiles::class,
        'export' => \Epaphrodites\epaphrodites\ExcelFiles\ExportFiles\ExportFiles::class,
        'mozart' => \Epaphrodites\epaphrodites\EpaphMozart\ModulesConfig\SwitchersList::class,
        'layout' => \Epaphrodites\epaphrodites\EpaphMozart\templatesConfig\LayoutsConfig::class,
    ];  

    /**
     * @var string[] $initDatabaseConfig
     * @return array
    */     
    protected static array $initDatabaseConfig =
    [
        'builders' => \Epaphrodites\database\query\Builders::class,
        'process' => \Epaphrodites\database\config\process\process::class,
        'seeder' => \Epaphrodites\database\config\process\checkDatabase::class,
    ];

    /**
     * @var string[] $initGuardsConfig
     * @return array
    */     
    public static array $initGuardsConfig =
    [
        'auth' => \Epaphrodites\epaphrodites\danho\DanhoAuth::class,
        'guard' => \Epaphrodites\epaphrodites\danho\GuardPassword::class,
        'session' => \Epaphrodites\epaphrodites\env\config\GeneralConfig::class,
        'sql' => \Epaphrodites\database\requests\mainRequest\select\auth::class,
    ];

    /**
     * @var string[] $initRightsConfig
     * @return array
    */     
    public static array $initRightsConfig =
    [
        'update' => \Epaphrodites\epaphrodites\yedidiah\UpdateRights::class,
        'delete' => \Epaphrodites\epaphrodites\yedidiah\YedidiaDeleted::class,
    ];    

    /**
     * @var string[] $initQrCodesConfig
     * @return array
    */     
    public static array $initQrCodesConfig =
    [
        'qrcode' => \chillerlan\QRCode\QRCode::class,
        'qroptions' => \chillerlan\QRCode\QROptions::class,
    ]; 

    /**
     * @var string[] $initQueryConfig
     * @return array
    */     
    public static array $initQueryConfig =
    [
        'count' => \Epaphrodites\database\requests\mainRequest\select\count::class,
        'param' => \Epaphrodites\database\requests\mainRequest\select\param::class,
        'getid' => \Epaphrodites\database\requests\mainRequest\select\get_id::class,
        'delete' => \Epaphrodites\database\requests\mainRequest\delete\delete::class,
        'update' => \Epaphrodites\database\requests\mainRequest\update\update::class,
        'insert' => \Epaphrodites\database\requests\mainRequest\insert\insert::class,
        'select' => \Epaphrodites\database\requests\mainRequest\select\select::class,
        'general' => \Epaphrodites\database\requests\mainRequest\select\general::class,
    ];  
    
    /**
     * @var string[] $initConfig
     * @return array
    */     
    public static array $initConfig =
    [
        'python' => \Epaphrodites\epaphrodites\translate\PythonCodesTranslate::class,
    ];    
    
    /**
     * @var string[] $initAuthConfig
     * @return array
    */      
    public static array $initAuthConfig =
    [
        'setting' => \Epaphrodites\epaphrodites\auth\SetSessionSetting::class,
    ];

    /**
     * Configuration for Twig
     * @var string[] $initTwigConfig
     * @return array
     */    
    public static $initTwigConfig =
    [
        'extension' => \Epaphrodites\epaphrodites\Extension\EpaphroditeExtension::class,
    ]; 

    /**
     * Check if the retrieved value is an object; if not, return a stdClass instance
     * @param string $key
     * @param array $config
     * @return object
     */
    public function getObject(array $config, string $key): object {
        
        return is_object( new $config[$key] ?? null) ? new $config[$key] : new \stdClass();
    }      
}
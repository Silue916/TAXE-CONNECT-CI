<?php

namespace Epaphrodites\epaphrodites\define\config\traits;

trait currentFunctionNamespaces
{

    /**
     * Initialize namespaces for different components
     * @return array
     */
    public static function initNamespace():array {

        return [
            'env' => new \Epaphrodites\epaphrodites\env\env,
            'paths' => new \Epaphrodites\epaphrodites\path\paths,
            'json' => new \Epaphrodites\epaphrodites\env\json\Json,
            'toml' => new \Epaphrodites\epaphrodites\env\toml\Toml,
            'errors' => new \Epaphrodites\controllers\render\errors,
            'datas' => new \Epaphrodites\database\datas\arrays\datas,
            'pdf' => new \Epaphrodites\epaphrodites\shares\pdfMakeStubs,
            'global' => new \Epaphrodites\epaphrodites\auth\HardSession,
            'mail' => new \Epaphrodites\epaphrodites\api\email\SendMail,
            'ajax' => new \Epaphrodites\epaphrodites\shares\ajaxMakeStubs,
            'crsf' => new \Epaphrodites\epaphrodites\CsrfToken\token_csrf,
            'session' => new \Epaphrodites\epaphrodites\auth\session_auth,
            'deepl' => new \Epaphrodites\epaphrodites\api\translate\deepLp,
            'msg' => new \Epaphrodites\epaphrodites\define\SetTextMessages,
            'secure' => new \Epaphrodites\epaphrodites\CsrfToken\csrf_secure,
            'cookies' => new \Epaphrodites\epaphrodites\auth\SetUsersCookies,
            'qrcode' => new \Epaphrodites\epaphrodites\QRCodes\GenerateQRCode,
            'bot' => new \Epaphrodites\epaphrodites\chatBot\processBotAnswers,
            'requests' => new \Epaphrodites\epaphrodites\api\requests\requests,
            'verify' => new \Epaphrodites\epaphrodites\env\VerifyInputCharacteres,
            'response' => new \Epaphrodites\epaphrodites\env\config\ResponseSequence,
            'visit' => new \Epaphrodites\epaphrodites\EpaphMozart\Visitors\visitorCount,
            'import' => new \Epaphrodites\epaphrodites\ExcelFiles\ImportFiles\ImportFiles,
            'export' => new \Epaphrodites\epaphrodites\ExcelFiles\ExportFiles\ExportFiles,
            'eng' => new \Epaphrodites\epaphrodites\define\lang\eng\SetEnglishTextMessages,
            'french' => new \Epaphrodites\epaphrodites\define\lang\fr\SetFrenchTextMessages,
            'spanish' => new \Epaphrodites\epaphrodites\define\lang\esp\SetSpanichTextMessages,
            'mozart' => new \Epaphrodites\epaphrodites\EpaphMozart\ModulesConfig\SwitchersList,
            'layout' => new \Epaphrodites\epaphrodites\EpaphMozart\templatesConfig\LayoutsConfig,
        ];
    }

    /**
     * Initialize configuration for various components
     * @return array
     */
    public static function initConfig():array {

        return [
            'qrcode' => new \chillerlan\QRCode\QRCode,
            'qroptions' => new \chillerlan\QRCode\QROptions,         
            'csv' => new \PhpOffice\PhpSpreadsheet\Reader\Csv,            
            'Ods' => new \PhpOffice\PhpSpreadsheet\Reader\Ods,
            'xls' => new \PhpOffice\PhpSpreadsheet\Reader\Xls,
            'xlsx' => new \PhpOffice\PhpSpreadsheet\Reader\Xlsx,
            'auth' => new \Epaphrodites\epaphrodites\danho\DanhoAuth,
            'otp' => new \Epaphrodites\epaphrodites\auth\epaphroditesOTP,
            'guard' => new \Epaphrodites\epaphrodites\danho\GuardPassword,
            'addright' => new \Epaphrodites\epaphrodites\yedidiah\AddRights,
            'process' => new \Epaphrodites\database\config\process\process,
            'crsf' => new \Epaphrodites\epaphrodites\CsrfToken\validate_token,
            'updright' => new \Epaphrodites\epaphrodites\yedidiah\UpdateRights,
            'setting' => new \Epaphrodites\epaphrodites\auth\SetSessionSetting,
            'session' => new \Epaphrodites\epaphrodites\env\config\GeneralConfig,            
            'seeder' => new \Epaphrodites\database\config\process\checkDatabase,
            'delright' => new \Epaphrodites\epaphrodites\yedidiah\YedidiaDeleted,
            'listright' => new \Epaphrodites\epaphrodites\yedidiah\YedidiaGetRights,
            'python' => new \Epaphrodites\epaphrodites\translate\PythonCodesTranslate,
            'extension' => new \Epaphrodites\epaphrodites\Extension\EpaphroditeExtension,
        ];
    }

    /**
     * Initialize query components
     * @return array
     */
    public static function initQuery():array {

        return [
            'auth' => new \Epaphrodites\database\requests\mainRequest\select\auth,
            'count' => new \Epaphrodites\database\requests\mainRequest\select\count,
            'param' => new \Epaphrodites\database\requests\mainRequest\select\param,
            'getid' => new \Epaphrodites\database\requests\mainRequest\select\get_id,
            'delete' => new \Epaphrodites\database\requests\mainRequest\delete\delete,
            'update' => new \Epaphrodites\database\requests\mainRequest\update\update,
            'insert' => new \Epaphrodites\database\requests\mainRequest\insert\insert,
            'select' => new \Epaphrodites\database\requests\mainRequest\select\select,
            'general' => new \Epaphrodites\database\requests\mainRequest\select\general,
            'setting' => new \Epaphrodites\database\requests\typeRequest\sqlRequest\insert\setting,
        ];
    }  
    
    /**
     * Check if the retrieved value is an object; if not, return a stdClass instance
     * @param string $key
     * @param array $config
     * @return object
     */
    public function getFunctionObject(array $config, string $key): object {
        
        return is_object( $config[$key] ?? null) ? $config[$key] : new \stdClass();
    }      
}
<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\Extension\Functions;

use Epaphrodites\epaphrodites\Extension\Filters\SetTwigFilters;

class SetTwigFunctions extends SetTwigFilters
{

    /**
     * Return QR code data
     *
     * @param mixed $datas
     * @return void
     */
    public function QRcodes_twig($datas): void
    {
        static::initNamespace()['qrcode']->GenerateQRCodeDatas($datas);
    }

    /**
     * Generate Google QR code data
     *
     * @param mixed $datas
     * @return void
     */
    public function GoogleQRCodes_twig($datas): void
    {
        static::initNamespace()['qrcode']->GenerateGoogleQRCode($datas);
    }

    /**
     * Return input for CSRF token
     *
     * @return void
     */
    public function csrf_token_twig(): void
    {
        static::initNamespace()['crsf']->input_field();
    }

    /**
     * Return meta for CSRF token
     *
     * @return void
     */
    public function X_Crsf_Token_twig(): void
    {
        static::initNamespace()['crsf']->xCrsfToken();
    }      

    /**
     * Return image paths
     *
     * @param string $img
     * @return void
     */
    public function imagePath_twig($img): void
    {
        echo static::initNamespace()['paths']->img($img);
    }

    /**
     * Return CSS paths
     *
     * @param string $css
     * @return void
     */
    public function cssPath_twig($css): void
    {
        echo static::initNamespace()['paths']->css($css);
    }

    /**
     * Return PDF paths
     *
     * @param string $pdf
     * @return void
     */
    public function pdfPath_twig($pdf): void
    {
        echo static::initNamespace()['paths']->pdf($pdf);
    }

    /**
     * Return CSS paths for fonts
     *
     * @param string $css
     * @return void
     */
    public function cssfontPath_twig($css): void
    {
        echo static::initNamespace()['paths']->font($css);
    }

    /**
     * Return CSS paths for icon fonts
     *
     * @param string $css
     * @return void
     */
    public function cssiconfontPath_twig($css): void
    {
        echo static::initNamespace()['paths']->icofont($css);
    }

    /**
     * Return Bootstrap icon paths
     *
     * @param string $css
     * @return void
     */
    public function cssbsicontPath_twig($css): void
    {
        echo  static::initNamespace()['paths']->bsicon($css);
    }

    /**
     * Return JavaScript paths
     *
     * @param string $js
     * @return void
     */
    public function jsPath_twig($js): void
    {
        echo  static::initNamespace()['paths']->js($js);
    }

    /**
     * Return the main path based on login status
     *
     * @param string|null $dir
     * @param string|null $page
     * @return void
     */
    public function mainPath_twig(?string $paths = null): void
    {

        echo  static::initNamespace()['paths']->main($paths);

    }

    /**
     * Get an admin path with an ID
     *
     * @param string|null $folder The admin folder
     * @param string|null $url The admin URL
     * @param array $actionId Additional query parameters as an associative array
     * @return void
     * var_dump($queryParams);die();  // Debugging statement, dump and die
     */
    public function mainidPath_twig(?string $folder = null , ?array $actionId = []): void
    {
        echo  static::initNamespace()['paths']->adminId($folder, $actionId);

    }

    /**
     * Return the host path based on login status
     *
     * @return void
     */
    public function hostPath_twig(): void
    {
        if (static::initNamespace()['session']->login() != false && static::initNamespace()['session']->id() != false) {
            echo  static::initNamespace()['paths']->dashboard();
        } else {
            echo  static::initNamespace()['paths']->gethost();
        }
    }

    /**
     * Return the path of the previous page
     *
     * @return void
     */
    public function previousPath_twig(): void
    {
        echo  static::initNamespace()['paths']->db();
    }

    /**
     * Return the logout path
     *
     * @return void
     */
    public function logoutPath_twig(): void
    {
        echo  static::initNamespace()['paths']->logout();
    }

    /**
     * Truncate the number of words in a text or sentence
     *
     * @param string $string
     * @param int $limit
     * @return string
     */
    public function truncatePath_twig($string, $limit): string
    {
        return static::initNamespace()['env']->truncate($string, $limit);
    }

    /**
     * Replace function
     *
     * @param mixed $search
     * @param mixed $replace
     * @param mixed $datas
     * @return mixed
     */
    public function replace_funct($search, $replace, $datas)
    {
        return str_replace($search, $replace, $datas);
    }

    /**
     * Explode and return data elements
     *
     * @param string|null $datas
     * @param string|null $separator
     * @param int|null $nbre
     * @return string
     */
    public function datasExplode(?string $datas = null, ?string $separator = '', ?int $nbre = 0): string
    {
        return static::initNamespace()['env']->explode_datas($datas, $separator, $nbre);
    }

    /**
     * Format a number with decimals and separators
     *
     * @param mixed $num
     * @param mixed $dec
     * @param mixed $separator
     * @return string
     */
    public function ifformat_twig($num, $dec, $separator): string
    {
        return static::initNamespace()['env']->nbre_format($num, $dec, $separator);
    }

    /**
     * Get a message
     *
     * @param string $msg
     * @return string
     */
    public function msgPath_twig(?string $msg = 'error_text'): string
    {
        return static::initNamespace()['msg']->answers($msg);
    }

    /**
     * Return the login status
     *
     * @return void
     */
    public function login_twig(): void
    {
        echo  static::initNamespace()['session']->login();
    }

    /**
     * Return the user type
     *
     * @return void
     */
    public function typeusers_twig(): void
    {
        echo  static::initNamespace()['session']->type();
    }

}

<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\Extension\Filters;

use Epaphrodites\epaphrodites\define\config\traits\currentFunctionNamespaces;
use Twig\Extension\AbstractExtension;

class SetTwigFilters extends AbstractExtension
{

    use currentFunctionNamespaces;

    /**
     * Twig filter: Pad a string to a certain length with another string
     *
     * @param mixed $number
     * @param mixed $pad_length
     * @param mixed $pad_string
     * @return string
     */
    public function twig_strptad($number, $pad_length, $pad_string): string
    {
        return static::initNamespace()['env']->strpad($number, $pad_length, $pad_string);
    }

    /**
     * Twig filter: Transform ISO code
     *
     * @param mixed $string
     * @return string|null
     */
    public function isoPath_twig(?string $string = null): string|null
    {
        return static::initNamespace()['env']->chaine($string);
    }

    /**
     * Twig filter: Transform ISO code for LongDate
     *
     * @param mixed $string
     * @return mixed
     */
    public function LongPath_twig($string)
    {
        return static::initNamespace()['env']->LongDate($string);
    }

    /**
     * Twig filter: Transform string to date
     *
     * @param mixed $string
     * @return mixed
     */
    public function dates_twig(string $string, string $format)
    {
        return static::initNamespace()['env']->convertDateToPHP($string, $format);
    }    
}
<?php

declare(strict_types=1);

namespace Epaphrodites\epaphrodites\path;
use Epaphrodites\epaphrodites\ErrorsExceptions\epaphroditeException;


class paths extends host
{
    /** @var string $path The current path */
    private string $path;

    /** @var string $slug The current slug */
    private string $slug;

    /**
     * Get the base host link
     *
     * @return string The host link
     */
    public function getHost(): string
    {
        return $this->host();
    }

    /**
     * Get the logout path
     *
     * @return string The logout path
     */
    public function logout(): string
    {
        $this->path = $this->getHost() . 'logout/';

        return $this->path;
    }

    /**
     * Get the logout path
     *
     * @return string The logout path
     */
    public function login(): string
    {
        $this->path = $this->getHost() . $this->href_slug(_LOGIN_);
        
        return $this->path;
    }    

    /**
     * Get the dashboard path
     *
     * @return string The dashboard path
     */
    public function dashboard(): string
    {
        $this->path = $this->getHost() . 'dashboard/';
        return $this->path;
    }

    /**
     * Get the main path with a specified link
     *
     * @param string $url The link for the main path
     * @return string The main path
     */
    public function main(
        string $url
    ): string{
        if (empty($url)) {
            throw new epaphroditeException('URL cannot be empty.');
        }

        $urlPath = explode('@', $url);

        $this->path = count($urlPath) === 2 ? $this->getHost() . $this->slug($urlPath[0]) . '/' . $this->slug($urlPath[1]) . '/' : $this->getHost() . _FAKE_ . $this->slug($urlPath[0]) . '/';

        return $this->path;
    }

    /**
     * Get an admin path with an ID
     *
     * @param string|null $targetFolder The admin folder
     * @param array $queryParams Additional query parameters as an associative array
     * @return string The admin path with an ID
     */
    public function adminId(
        string|null $targetFolder = null, 
        array $queryParams = []
    ): string{

        if (empty($targetFolder)) {
            throw new epaphroditeException('Target folder cannot be empty.');
        }

        $foldersUrl = explode('@', $targetFolder);

        // Build the URL with the admin folder, slugified URL, and trailing slash
        $url = count($foldersUrl) === 2 ? $this->getHost() . $this->slug($foldersUrl[0]) . '/' . $this->slug($foldersUrl[1]) . '/' : $this->getHost() . _FAKE_ . $this->slug($foldersUrl[0]) . '/';

        // Add query parameters to the URL
        if (!empty($queryParams)) {

            $url .= '?' . http_build_query($queryParams, '', '&', PHP_QUERY_RFC3986);
        }

        $this->path = $url;

        return $this->path;
    }

    /**
     * Get the path for images
     *
     * @param string|null $img The image filename
     * @return string The image path
     */
    public function img(
        string|null $img = null
    ): string{
        $this->path = $this->getHost() . 'static/img/' . $img;

        return $this->path;
    }

    /**
     * Get the path for JavaScript files
     *
     * @param string $js The JavaScript filename
     * @return string The JavaScript path
     */
    public function js(
        string $js
    ): string{
        $this->path = $this->getHost() . 'static/js/' . $js . '.js';

        return $this->path;
    }

    /**
     * Get the path for CSS files
     *
     * @param string $css The CSS filename
     * @return string The CSS path
     */
    public function css(
        string $css
    ): string{
        $this->path = $this->getHost() . 'static/css/' . $css . '.css';
        return $this->path;
    }

    /**
     * Get the path for Font Awesome files
     *
     * @param string $css The Font Awesome filename
     * @return string The Font Awesome path
     */
    public function font(
        string $css
    ): string{
        $this->path = $this->getHost() . 'static/font-awesome/css/' . $this->slug($css) . '.css';
        return $this->path;
    }

    /**
     * Get the path for IcoFont files
     *
     * @param string $css The IcoFont filename
     * @return string The IcoFont path
     */
    public function icofont(
        string $css
    ): string{
        $this->path = $this->getHost() . 'static/icofont/' . $css . '.css';
        return $this->path;
    }

    /**
     * Get the path for Bootstrap Icons files
     *
     * @param string $bsicon The Bootstrap Icons filename
     * @return string The Bootstrap Icons path
     */
    public function bsicon(
        string $bsicon
    ): string{
        $this->path = $this->getHost() . 'static/bootstrap-icons/' . $bsicon . '.css';
        return $this->path;
    }

    /**
     * Get the path for PDF files
     *
     * @param string|null $docs The PDF filename
     * @return string The PDF path
     */
    public function pdf(
        string|null $docs = null
    ): string{
        $this->path = $this->getHost() . 'static/docs/' . $docs;
        return $this->path;
    }

    /**
     * Slug constructor
     *
     * @param string $string The input string to generate a slug from.
     * @param string $delimiter The character used to replace spaces and non-alphanumeric characters in the slug. Default is an underscore (_).
     * @return string The generated slug.
     */
    private function slug(
        string $string, 
        string $delimiter = '-'
    ): string{
        // Validate input
        if (empty($string)) {
            throw new epaphroditeException('Input string cannot be empty.');
        }

        // Check if iconv is available
        if (!function_exists('iconv')) {
            throw new \RuntimeException('iconv is not available. Please make sure the iconv extension is enabled.');
        }

        // Set locale for proper character conversion
        $oldLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');

        // Convert to ASCII and remove unwanted characters
        $this->slug = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        $this->slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $this->slug);

        $this->slug = strtolower($this->slug);

        $this->slug = preg_replace("/[\/_|+ -]+/", $delimiter, $this->slug);

        $this->slug = trim($this->slug, $delimiter);

        setlocale(LC_ALL, $oldLocale);

        return $this->slug;
    }

    /**
     * Slug constructor for URLs
     *
     * @param string $string The input string to generate a slug from.
     * @param string $delimiter The character used to replace spaces and non-alphanumeric characters in the slug. Default is an underscore (_).
     * @return string The generated slug.
     */
    public function href_slug(
        string $string,
        string $delimiter = '_'
    ): string{

        // Validate input
        if (empty($string)) {

            throw new epaphroditeException('Input string cannot be empty.');
        }

        // Check if iconv is available
        if (!function_exists('iconv')) {
            throw new \RuntimeException('iconv is not available. Please make sure the iconv extension is enabled.');
        }

        // Set locale for proper character conversion
        $oldLocale = setlocale(LC_ALL, '0');
        setlocale(LC_ALL, 'en_US.UTF-8');

        // Convert to ASCII and remove unwanted characters
        $slug = iconv('UTF-8', 'ASCII//TRANSLIT', $string);

        // Remove characters that are not letters, numbers, slashes, underscores, pipes, plus signs, or hyphens
        $slug = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $slug);

        // Convert to lowercase
        $slug = strtolower($slug);

        // Replace sequences of invalid characters with the delimiter
        $slug = preg_replace("/[\%<>_|+ & -]+/", $delimiter, $slug);

        // Trim leading and trailing delimiters
        $slug = trim($slug, $delimiter);

        // Restore original locale
        setlocale(LC_ALL, $oldLocale);

        return $slug;
    }
}
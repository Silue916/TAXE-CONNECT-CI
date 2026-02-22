<?php declare(strict_types=1);

namespace Epaphrodites\controllers\render\Http;

class ConfigHttp extends HttpClient
{

    /**
     * Get the provider URL
     *
     * @param array|null $url
     * @return string
     */
    protected function provider(
        array|null $url = null
    ): string
    {
        
        return str_replace(_FAKE_, '', sprintf('%s/%s%s', isset($url[0]) ? $url[0] : '', $url[1], _MAIN_EXTENSION_ ));
    }

    /**
     * Parse the HTTP method
     *
     * @return string
     */
    protected function ParseMethod(): string
    {
        return $this->HttpRequest() !== "/" && $this->HttpRequest()[-1] === "/"
            ? substr($this->HttpRequest(), 1)
            : _DASHBOARD_;
    }
}
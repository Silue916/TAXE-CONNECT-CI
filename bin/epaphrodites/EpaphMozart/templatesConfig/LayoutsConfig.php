<?php

namespace Epaphrodites\epaphrodites\EpaphMozart\templatesConfig;

class LayoutsConfig
{

    /**
     * Get login template
     * @return string
     */
    public function login():string
    {

        return "layouts/template/__default_login.html.twig";
    }

    /**
     * Get default main template ( when user are not connected )
     * @return string
     */
    public function main():string
    {

        return "layouts/template/__default.main.html.twig";
    }

    /**
     * Get default admin template ( when user are connected )
     * @param int $key
     * @return string
     */
    public function admin(?int $key = null):string
    {
        
        $urls =
            [
                1 => "layouts/template/__default.super.admin.html.twig",
                2 => "layouts/template/__default.all.central.admin.html.twig",
                3 => "layouts/template/__default.all.users.html.twig",
                4 => "layouts/template/__default.all.users.html.twig",
            ];

        return $urls[$key] ?? $urls[3];
    }

    /**
     * Get default template ( forms template )
     * @return string
     */
    public function forms():string
    {

        return "layouts/widgets/__widgets.forms.html.twig";
    }

    /**
     * Get default template ( charts template )
     * @return string
     */
    public function charts():string
    {

        return "layouts/widgets/__widgets.charts.html.twig";
    }

    /**
     * Get default template ( errors template )
     * @return string
     */
    public function errors():string
    {

        return "layouts/template/__default.errors.html.twig";
    }

    /**
     * Get default template ( show messages )
     * @return string
     */
    public function msg():string
    {

        return "layouts/widgets/__widgets.messages.html.twig";
    }

    /**
     * Get default template ( breadcrumbs template )
     * @return string
     */
    public function breadcrumbs():string
    {

        return "layouts/widgets/__widgets.breadcrumb.html.twig";
    }

    /**
     * Get default template ( pagination template )
     * @return string
     */
    public function pagination():string
    {

        return "layouts/widgets/__widgets.pagination.html.twig";
    }

    /**
     * Get default template ( ajax template )
     * @return string
     */
    public function ajax():string
    {

        return "layouts/widgets/__widgets.ajax.html.twig";
    }  
    
    /**
     * Get default template ( tools template )
     * @return string
     */
    public function tools():string
    {

        return "layouts/widgets/__widgets.tools.html.twig";
    }     
}

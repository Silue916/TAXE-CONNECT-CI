<?php

namespace Epaphrodites\epaphrodites\Console\Stubs;

class AllViewsStub
{

    public static function Generate($FilesNames, $name , $html='$html')
    {
       
$stub = 
"{% extends layouts %} {% from breadcrumb import breadcrumb_field %} {% from message import alert_msg %}{% from forms import input_field , loader , import_field , submit %}{% from pagination import dataTable_pagination, pagination_field %}{% block content %}
<div class='container-fluid admin'>
    <h1>SUCCESS - VIEWS CREATED</h1>
    <hr>
    <p>{{ __msg('site-title') }}</p>
</div>
{% endblock %}
";
    file_put_contents($FilesNames, $stub);
    }    

}
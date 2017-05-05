<?php
namespace app;

class Creator
{
    function create()
    {
        header('Content-Type:application/xml');
        echo \Template::instance()->render('declare-template.xml');
    }
}

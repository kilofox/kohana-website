<?php

class View_Donate_Index extends Kostache_Layout
{
    /**
     * @var     array    partials for the page
     */
    protected $_partials = [
        'header' => 'partials/header',
        'footer' => 'partials/footer',
    ];

    /**
     * @var     boolean   show the banner space on template
     */
    public $banner_exists = false;

}

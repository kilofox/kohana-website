<?php

abstract class View_Error extends Kostache_Layout
{
    /**
     * @var     array    partials for the page
     */
    protected $_partials = array(
        'header' => 'partials/header',
        'footer' => 'partials/footer',
        'banner' => 'partials/error/banner',
    );

    /**
     * @var     boolean   show the banner space on template
     */
    public $banner_exists = TRUE;
    public $message;
    public $type;

}

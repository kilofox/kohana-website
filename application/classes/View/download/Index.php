<?php

class View_Download_Index extends Kostache_Layout
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
    public $banner_exists = FALSE;

    /**
     * @var     boolean   triggers the menu bar highlight
     */
    public $menu_download = TRUE;

}

<?php

class View_Home_Index extends Kostache_Layout
{
    /**
     * @var     array    partials for the page
     */
    protected $_partials = [
        'header' => 'partials/header',
        'footer' => 'partials/footer',
        'banner' => 'partials/home/banner',
        'features' => 'home/features',
        'gallery' => 'home/gallery',
        'social' => 'home/social',
        'whouses' => 'home/whouses',
    ];

    /**
     * @var     boolean   show the banner space on template
     */
    public $banner_exists = true;

    /**
     * @var     boolean   triggers the menu bar highlight
     */
    public $menu_home = true;

}

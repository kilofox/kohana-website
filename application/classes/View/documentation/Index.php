<?php

class View_Documentation_Index extends Kostache_Layout
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

    /**
     * @var     boolean   triggers the menu bar highlight
     */
    public $menu_documentation = true;

    /**
     * Returns home page url
     *
     * @return  string
     */
    public function home_url()
    {
        return Route::url('home');
    }

}

<?php

class View_Development_Index extends Kostache_Layout
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
    public $menu_development = true;

    /**
     * Returns team page url
     *
     * @return  string
     */
    public function team_url()
    {
        return Route::url('team');
    }

}

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

    /**
     * Returns documentation versions data for the template loop.
     *
     * @return array
     * @throws Kohana_Exception
     */
    public function docs(): array
    {
        return [
            [
                'version' => '3.5',
                'url' => URL::base() . '3.5/guide/',
                'label' => 'Kohana v3.5 Documentation',
                'current' => true,
            ],
            [
                'version' => '3.4',
                'url' => URL::base() . '3.4/guide/',
                'label' => 'Kohana v3.4 Documentation',
                'current' => false,
            ],
            [
                'version' => '3.3',
                'url' => URL::base() . '3.3/guide/',
                'label' => 'Kohana v3.3 Documentation',
                'current' => false,
            ],
            [
                'version' => '3.2',
                'url' => URL::base() . '3.2/guide/',
                'label' => 'Kohana v3.2 Documentation',
                'current' => false,
            ],
            [
                'version' => '3.1',
                'url' => URL::base() . '3.1/guide/',
                'label' => 'Kohana v3.1 Documentation',
                'current' => false,
            ],
        ];
    }
}

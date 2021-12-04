<?php

class View_Team_Index extends Kostache_Layout
{
    /**
     * @var     array    partials for the page
     */
    protected $_partials = array(
        'header' => 'partials/header',
        'footer' => 'partials/footer',
    );

    /**
     * @var     boolean   show the banner space on template
     */
    public $banner_exists = FALSE;

    /**
     * Email link for team@kohana.top
     *
     * @return  string
     */
    public function team_at_kohanaframework()
    {
        return HTML::mailto('team@kohana.top');
    }

}

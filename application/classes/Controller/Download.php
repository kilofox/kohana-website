<?php

class Controller_Download extends Controller
{
    public function action_index()
    {
        $view = new View_Download_Index;
        $view->set('title', 'Download Kohana');

        $this->response->body($view);
    }

}

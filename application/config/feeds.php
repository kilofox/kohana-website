<?php

defined('SYSPATH') or die('No direct script access.');

return array(
    'forum' => array(
        'title' => 'Discussions',
        'feed' => 'http://forum.kohana.top/discussions/feed.rss',
        'link' => 'http://forum.kohana.top/',
        'limit' => 8,
        'cache' => 30,
    ),
    'dev' => array(
        'title' => 'Development',
        'feed' => 'http://dev.kohana.top/activity.atom?show_issues=1',
        'link' => 'http://dev.kohana.top/activity',
        'limit' => 5,
        'cache' => 30,
    ),
);

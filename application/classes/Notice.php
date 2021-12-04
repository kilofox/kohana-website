<?php

class Notice
{
    // Notice types
    const ERROR = 'error';
    const WARNING = 'warning';
    const INFO = 'information';
    const SUCCESS = 'success';

    /**
     * Add a new notice
     *
     * @param   string  Type
     * @param   string  Message
     * @param   array   Translation values
     * @return	void
     */
    public static function add($type, $message = null, array $values = null)
    {
        $session = Session::instance();

        $notices = $session->get('notices', []);

        $notices[] = [
            'type' => $type,
            'message' => __($message, $values),
        ];

        $session->set('notices', $notices);
    }

    /**
     * Returns the current notices.
     *
     * @return   array
     */
    public static function as_array()
    {
        $session = Session::instance();

        return $session->get_once('notices', []);
    }

}

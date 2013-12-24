<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/contact
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Dialog\Simple;

use phpManufaktur\Contact\Control\Alert;

class Dialog extends Alert
{

    protected static $message = '';

    protected static $options = array();

    /**
     * @return the $message
     * @deprecated use getAlert() instead
     */
    public function getMessage()
    {
        $callers = debug_backtrace();
        $this->app['monolog']->addDebug('DEPRECATED: getMessage()', array(__METHOD__, __LINE__, $callers[1]));

        return self::$message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message, $params=array())
    {
        $callers = debug_backtrace();
        $this->app['monolog']->addDebug('DEPRECATED: setMessage()', array(__METHOD__, __LINE__, $callers[1]));

        self::$message .= $this->app['twig']->render($this->app['utils']->getTemplateFile(
            self::$options['template']['namespace'], self::$options['template']['message']),
            array('message' => $this->app['translator']->trans($message, $params)));
    }

    /**
     * Check if a message is active
     *
     * @return boolean
     */
    public function isMessage()
    {
        return !empty(self::$message);
    }

    /**
     * @return the $options
     */
    public static function getOptions()
    {
        return self::$options;
    }

    /**
     * @param field_type $options
     */
    public static function setOptions($options)
    {
        if (is_array($options)) {
            foreach ($options as $key => $value) {
                self::$options[$key] = $value;
            }
        }
    }

}

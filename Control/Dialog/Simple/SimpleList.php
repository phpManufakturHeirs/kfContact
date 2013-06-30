<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Dialog\Simple;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Contact;

class SimpleList {

    protected $app = null;
    protected static $contact_id = -1;
    protected static $message = '';
    protected $ContactData = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        // set the content language
        $this->app['translator']->setLocale('de');
        $this->ContactData = new Contact($this->app);
    }

    public function setContactID($contact_id)
    {
        self::$contact_id = $contact_id;
    }

    /**
     * @return the $message
     */
    public function getMessage()
    {
        return self::$message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message, $params=array())
    {
        self::$message .= $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'message.twig'),
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



    public function exec()
    {
        $list = array();
        $this->ContactData->selectList();

        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'simple.list.twig'),
            array(
                'message' => $this->getMessage(),
                'list' => $list
            ));
    }
}
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
use phpManufaktur\Contact\Control\ContactList as ContactListControl;

class ContactList {

    protected $app = null;
    protected $ContactListControl = null;
    protected static $message = '';
    protected static $columns = null;
    protected static $rows_per_page = null;
    protected static $select_status = null;
    protected static $max_pages = null;
    protected static $current_page = null;
    protected static $order_by = null;
    protected static $order_direction = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->ContactListControl = new ContactListControl($this->app);

        try {
            // search for the config file in the template directory
            $cfg_file = $this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'backend/simple/list.json', '', true);
            // get the columns to show in the list
            $cfg = $this->app['utils']->readJSON($cfg_file);
            self::$columns = $cfg['columns'];
            self::$rows_per_page = $cfg['list']['rows_per_page'];
            self::$select_status = $cfg['list']['select_status'];
            self::$order_by = $cfg['list']['order']['by'];
            self::$order_direction = $cfg['list']['order']['direction'];
        } catch (\Exception $e) {
            // the config file does not exists - use all available columns
            self::$columns = $this->ContactList->getColumns();
            self::$rows_per_page = 100;
            self::$select_status = array('ACTIVE', 'LOCKED');
            self::$order_by = array('contact_id');
            self::$order_direction = 'ASC';
        }
        self::$current_page = 1;
    }

    public function setCurrentPage($page)
    {
        self::$current_page = $page;
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
        self::$message .= $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'backend/message.twig'),
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
        $order_by = explode(',', $this->app['request']->get('order', implode(',', self::$order_by)));
        $order_direction = $this->app['request']->get('direction', self::$order_direction);

        $list = $this->ContactListControl->getList(self::$current_page, self::$rows_per_page, self::$select_status, self::$max_pages, $order_by, $order_direction);

        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'backend/simple/list.twig'),
            array(
                'message' => $this->getMessage(),
                'list' => $list,
                'columns' => self::$columns,
                'pagination_route' => FRAMEWORK_URL.'/admin/contact/simple/list/page/',
                'current_page' => self::$current_page,
                'last_page' => self::$max_pages,
                'order_by' => $order_by,
                'order_direction' => strtolower($order_direction)
            ));
    }
}
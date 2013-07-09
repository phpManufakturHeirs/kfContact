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

class ContactList extends Dialog {

    protected $ContactListControl = null;
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
    public function __construct(Application $app, $options=null)
    {
        parent::__construct($app);

        $this->ContactListControl = new ContactListControl($this->app);

        $this->setOptions(array(
            'template' => array(
                'namespace' => isset($options['template']['namespace']) ? $options['template']['namespace'] : '@phpManufaktur/Contact/Template',
                'settings' => isset($options['template']['settings']) ? $options['template']['settings'] : 'backend/simple/contact.list.json',
                'message' => isset($options['template']['message']) ? $options['template']['message'] : 'backend/message.twig',
                'list' => isset($options['template']['list']) ? $options['template']['list'] : 'backend/simple/contact.list.twig'
            ),
            'route' => array(
                'pagination' => isset($options['route']['pagination']) ? $options['route']['pagination'] : '/admin/contact/simple/list/page/{page}?order={order}&direction={direction}',
                'contact' => isset($options['route']['contact']) ? $options['route']['contact'] : '/admin/contact/simple/contact/{contact_id}'
            )
        ));

        try {
            // search for the config file in the template directory
            $cfg_file = $this->app['utils']->templateFile(self::$options['template']['namespace'], self::$options['template']['settings'], '', true);
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

    /**
     * Set the current page for the table
     *
     * @param integer $page
     */
    public function setCurrentPage($page)
    {
        self::$current_page = $page;
    }

    /**
     * Return the complete contact list
     *
     * @param null|array $extra additional parameters for the template
     * @return string contact list
     */
    public function exec($extra=null)
    {
        $order_by = explode(',', $this->app['request']->get('order', implode(',', self::$order_by)));
        $order_direction = $this->app['request']->get('direction', self::$order_direction);

        $list = $this->ContactListControl->getList(self::$current_page, self::$rows_per_page, self::$select_status, self::$max_pages, $order_by, $order_direction);

        return $this->app['twig']->render($this->app['utils']->templateFile(self::$options['template']['namespace'], self::$options['template']['list']),
            array(
                'message' => $this->getMessage(),
                'list' => $list,
                'columns' => self::$columns,
                'pagination_route' => FRAMEWORK_URL.self::$options['route']['pagination'],
                'contact_route' => FRAMEWORK_URL.self::$options['route']['contact'],
                'current_page' => self::$current_page,
                'last_page' => self::$max_pages,
                'order_by' => $order_by,
                'order_direction' => strtolower($order_direction),
                'extra' => $extra
            ));
    }
}
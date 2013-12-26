<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Backend;

use Silex\Application;
use phpManufaktur\Basic\Control\Pattern\Alert;

class Backend extends Alert {

    protected static $usage = null;
    protected static $usage_param = null;

    /**
     * Initialize the class with the needed parameters
     *
     * @param Application $app
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);

        $cms = $this->app['request']->get('usage');
        self::$usage = is_null($cms) ? 'framework' : $cms;
        self::$usage_param = (self::$usage != 'framework') ? '?usage='.self::$usage : '';
        // set the locale from the CMS locale
        if (self::$usage != 'framework') {
            $app['translator']->setLocale($this->app['session']->get('CMS_LOCALE', 'en'));
        }
    }

    /**
     * Get the toolbar for all backend dialogs
     *
     * @param string $active dialog
     * @return multitype:multitype:string boolean
     */
    public function getToolbar($active) {
        $toolbar_array = array(
            'contact_list' => array(
                'name' => 'contact_list',
                'text' => 'Contact list',
                'hint' => 'List of all available contacts',
                'link' => FRAMEWORK_URL.'/admin/contact/backend/list'.self::$usage_param,
                'active' => ($active == 'contact_list')
            ),
            'contact_edit' => array(
                'name' => 'contact_edit',
                'text' => 'Contact',
                'hint' => 'Create a new contact',
                'link' => FRAMEWORK_URL.'/admin/contact/backend/select'.self::$usage_param,
                'active' => ($active == 'contact_edit')
            ),
            'categories' => array(
                'name' => 'categories',
                'text' => 'Categories',
                'hint' => 'List of available categories',
                'link' => FRAMEWORK_URL.'/admin/contact/backend/category/list'.self::$usage_param,
                'active' => ($active == 'categories')
            ),
            'tags' => array(
                'name' => 'tags',
                'text' => 'Tags',
                'hint' => 'List of available tags',
                'link' => FRAMEWORK_URL.'/admin/contact/backend/tag/list'.self::$usage_param,
                'active' => ($active == 'tags')
            ),
            'extra_fields' => array(
                'name' => 'extra_fields',
                'text' => 'Extra fields',
                'hint' => 'List of available extra fields',
                'link' => FRAMEWORK_URL.'/admin/contact/backend/extra/list'.self::$usage_param,
                'active' => ($active == 'extra_fields')
            ),

            'about' => array(
                'name' => 'about',
                'text' => 'About',
                'hint' => 'Information about the Contact extension',
                'link' => FRAMEWORK_URL.'/admin/contact/backend/about'.self::$usage_param,
                'active' => ($active == 'about')
                ),
        );
        return $toolbar_array;
    }

}

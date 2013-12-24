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
use phpManufaktur\Contact\Control\Backend\Backend;
use phpManufaktur\Contact\Control\Dialog\Simple\ExtraFieldList as SimpleExtraFieldList;

class ExtraList extends Backend {

    protected $SimpleExtraFieldList = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app=null)
    {
        parent::__construct($app);
        if (!is_null($app)) {
            $this->initialize($app);
        }
    }

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Contact\Control\Backend\Backend::initialize()
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);
        $this->SimpleExtraFieldList = new SimpleExtraFieldList($this->app, array(
            'template' => array(
                'namespace' => '@phpManufaktur/Contact/Template',
                'alert' => 'bootstrap/pattern/alert.twig',
                'list' => 'bootstrap/admin/list.extra.twig'
            ),
            'route' => array(
                'edit' => '/admin/contact/backend/extra/edit/id/{type_id}?usage='.self::$usage,
                'create' => '/admin/contact/backend/extra/create?usage='.self::$usage
            )
        ));
    }

    /**
     * Controller for the extra fields list
     *
     * @param Application $app
     */
    public function controller(Application $app)
    {
        $this->initialize($app);
        $extra = array(
            'usage' => self::$usage,
            'toolbar' => $this->getToolbar('extra_fields')
        );
        return $this->SimpleExtraFieldList->exec($extra);
    }

}

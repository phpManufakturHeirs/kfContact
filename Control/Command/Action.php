<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Command;

use Silex\Application;
use phpManufaktur\Basic\Control\kitCommand\Basic;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Request;

class Action extends Basic
{
    protected static $parameter = null;

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::initParameters()
     */
    protected function initParameters(Application $app, $parameter_id=-1)
    {
        parent::initParameters($app, $parameter_id);

        self::$parameter = $this->getCommandParameters();

        // check the CMS GET parameters
        $GET = $this->getCMSgetParameters();
        if (isset($GET['command']) && ($GET['command'] == 'contact') && isset($GET['action'])) {
            foreach ($GET as $key => $value) {
                if ($key == 'command') continue;
                self::$parameter[$key] = $value;
            }
            $this->setCommandParameters(self::$parameter);
        }

        // grant that the 'action' value is a lower string
        self::$parameter['action'] = isset(self::$parameter['action']) ? strtolower(self::$parameter['action']) : 'none';
    }

    /**
     * Action handler for the kitCommand ~~ contact ~~
     *
     * @param Application $app
     */
    public function ControllerAction(Application $app)
    {
        $this->initParameters($app);

        switch (self::$parameter['action']) {
            case 'form':
                // handle contact forms
                return $this->createIFrame('/contact/form');
            case 'list':
                // show contact list
                return $this->createIFrame('/contact/list');
            case 'register':
                // register a new contact record
                return $this->createIFrame('/contact/register');
            case 'view':
                // show a specific contact record
                return $this->createIFrame('/contact/view');
            case 'none':
                // missing the action parameter, show the welcome page!
                return $this->createIFrame('/basic/help/contact/welcome');
            default:
                // unknown action parameter!
                $this->setAlert('The action <b>%action%</b> is unknown, please check the parameters for the kitCommand!',
                    array('%action%' => self::$parameter['action']), self::ALERT_TYPE_WARNING);
                return $this->createIFrame('/basic/alert/'.base64_encode($this->getAlert()));
        }
    }

}

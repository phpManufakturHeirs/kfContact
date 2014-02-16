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

    /**
     * Controller to create the iFrame for executing the Contact kitCommand,
     * execute the route /contact/action
     *
     * @param Application $app
     */
    public function ControllerCreateIFrame(Application $app)
    {
        $this->initParameters($app);
        return $this->createIFrame('/contact/action');
    }

    /**
     * Action handler for the kitCommand ~~ contact ~~
     *
     * @param Application $app
     * @throws \Exception
     * @return string dialog or result
     */
    public function ControllerAction(Application $app)
    {
        try {
            $this->initParameters($app);
            // get the kitCommand parameters
            $parameters = $this->getCommandParameters();

            // check the CMS GET parameters
            $GET = $this->getCMSgetParameters();
            if (isset($GET['command']) && ($GET['command'] == 'contact')) {
                foreach ($GET as $key => $value) {
                    if ($key == 'command') continue;
                    $parameters[$key] = $value;
                }
                $this->setCommandParameters($parameters);
            }
            if (!isset($parameters['action'])) {
                // there is no 'mode' parameter set, so we show the "Welcome" page
                $subRequest = Request::create('/basic/help/contact/welcome', 'GET');
                return $app->handle($subRequest, HttpKernelInterface::SUB_REQUEST);
            }

            switch (strtolower($parameters['action'])) {
                case 'form':
                    // handle a form
                    $Form = new Form();
                    return $Form->ControllerFormAction($app);
                default:
                    // unknown action
                    $this->setAlert('The action <b>%action%</b> is unknown, please check the parameters for the kitCommand!',
                        array('%action%' => $parameters['action']), self::ALERT_TYPE_WARNING);
                    return $this->promptAlert();
            }

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }

}

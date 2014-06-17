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
use Symfony\Component\Form\FormFactory;

/**
 * Class ContactRegister
 * @package phpManufaktur\Contact\Control\Command
 */
class ContactRegister extends Basic
{
    protected $app = null;
    protected static $contact_type = null;

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::initParameters()
     */
    protected function initParameters(Application $app, $parameter_id=-1)
    {
        parent::initParameters($app, $parameter_id);

    }

    /**
     * Get the Form to select the contact type
     *
     * @return FormFactory
     */
    protected function getFormSelectContactType()
    {
        return $this->app['form.factory']->createBuilder('form')
        ->add('contact_type', 'choice', array(
            'choices' => array('PERSON' => 'PERSON', 'COMPANY' => 'Organization'),
            'empty_value' => false,
            'multiple' => false,
            'expanded' => true,
            'data' => 'PERSON'
        ))
        ->getForm();
    }

    protected function getFormContactData($data=array())
    {
        $form = $this->app['form.factory']->createBuilder('form')
        ->add('contact_id', 'hidden', array(
            'data' => isset($data['contact_id']) ? $data['contact_id'] : -1
        ))
        ->add('contact_type', 'hidden', array(
            'data' => self::$contact_type
        ));

        return $form->getForm();
    }

    public function ControllerRegisterContact(Application $app, $contact_type)
    {
        $this->initialize($app);
        self::$contact_type = $contact_type;

        return __METHOD__;
    }


    /**
     * @param Application $app
     * @return string
     */
    public function ControllerRegister(Application $app)
    {
        $this->initParameters($app);

        // create the form
        $form = $this->getFormSelectContactType();

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                // get the form data
                $contact = $form->getData();

                if ($contact['contact_type'] == 'PERSON') {
                    // create a new PERSON contact
                    return $this->ControllerRegisterContact($app, 'PERSON');
                }
                else {
                    // create a new COMPANY contact
                    return $this->ControllerRegisterContact($app, 'COMPANY');
                }
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setAlert('The form is not valid, please check your input and try again!',
                    array(), self::ALERT_TYPE_DANGER);
            }
        }

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/register.type.contact.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView()
            ));
    }
}

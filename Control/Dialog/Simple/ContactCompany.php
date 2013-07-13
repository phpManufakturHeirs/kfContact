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
use phpManufaktur\Contact\Control\Contact as ContactControl;
use Symfony\Component\Form\FormBuilder;

class ContactCompany extends Dialog {

    protected static $contact_id = -1;
    protected $ContactControl = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app, $options=null)
    {
        parent::__construct($app);
        // set the form options
        $this->setOptions(array(
            'template' => array(
                'namespace' => isset($options['template']['namespace']) ? $options['template']['namespace'] : '@phpManufaktur/Contact/Template',
                'message' => isset($options['template']['message']) ? $options['template']['message'] : 'backend/message.twig',
                'contact' => isset($options['template']['select']) ? $options['template']['select'] : 'backend/simple/edit.company.contact.twig'
            ),
            'route' => array(
                'action' => isset($options['route']['action']) ? $options['route']['action'] : '/admin/contact/simple/contact/company',
            )
        ));
        $this->ContactControl = new ContactControl($this->app);
    }

    /**
     * Set the contact ID
     *
     * @param integer $contact_id
     */
    public function setContactID($contact_id)
    {
        self::$contact_id = $contact_id;
    }

    /**
     * Build the complete form with the form.factory
     *
     * @param array $contact flatten contact record
     * @return FormBuilder
     */
    protected function getForm($contact)
    {
        // we need the Tag's as a simple array!
        $tags = array();
        foreach ($contact['tag'] as $tag) {
            $tags[] = $tag['tag_name'];
        }

        $form = $this->app['form.factory']->createBuilder('form')
        ->add('contact_id', 'hidden', array(
            'data' => $contact['contact']['contact_id']
        ))
        ->add('contact_type', 'hidden', array(
            'data' => $contact['contact']['contact_type']
        ))
        ->add('contact_status', 'choice', array(
            'choices' => array('ACTIVE' => 'active', 'LOCKED' => 'locked', 'DELETED' => 'deleted'),
            'empty_value' => false,
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'label' => 'Status',
            'data' => $contact['contact']['contact_status']
        ))
        ->add('category', 'choice', array(
            'choices' => $this->ContactControl->getCategoryArrayForTwig(),
            'empty_value' => '- please select -',
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'label' => 'Category'
        ))
        ->add('tags', 'choice', array(
            'choices' => $this->ContactControl->getTagArrayForTwig(),
            'expanded' => true,
            'multiple' => true,
            'required' => false,
            'label' => 'Tags',
            'data' => $tags
        ))
        ->add('contact_name', 'text', array(
            'required' => false,
            'label' => 'Contact name',
            'data' => $contact['contact']['contact_name']
        ))
        ->add('contact_login', 'text', array(
            'required' => false,
            'label' => 'Contact login',
            'data' => $contact['contact']['contact_login']
        ))
        ->add('company_id', 'hidden', array(
            'data' => $contact['company'][0]['company_id']
        ))
        ->add('company_name', 'text', array(
            'label' => 'Company name',
            'data' => $contact['company'][0]['company_name']
        ))
        ->add('company_department', 'text', array(
            'required' => false,
            'label' => 'Company department',
            'data' => $contact['company'][0]['company_department']
        ))
        ->add('company_additional', 'text', array(
            'required' => false,
            'label' => 'Additional',
            'data' => $contact['company'][0]['company_additional']
        ))
        ->add('company_additional_2', 'text', array(
            'required' => false,
            'label' => 'Additional',
            'data' => $contact['company'][0]['company_additional_2']
        ))
        ->add('company_additional_3', 'text', array(
            'required' => false,
            'label' => 'Additional',
            'data' => $contact['company'][0]['company_additional_3']
        ))
        ;

        return $form->getForm();
    }

    /**
     * Return the complete contact dialog and handle requests
     *
     * @return string contact dialog
     */
    public function exec($extra=null)
    {

        $contact = $this->ContactControl->getDefaultRecord('COMPANY');
        echo "<pre>";
        print_r($contact);
        echo "</pre>";

        // create the form
        $form = $this->getForm($contact);

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                // get the form data
                $contact = $form->getData();

            }
            else {
                // general error (timeout, CSFR ...)
                $this->setMessage('The form is not valid, please check your input and try again!');
            }
        }

        return $this->app['twig']->render($this->app['utils']->templateFile(self::$options['template']['namespace'], self::$options['template']['contact']),
            array(
                'message' => $this->getMessage(),
                'form' => $form->createView(),
                'route' => self::$options['route'],
                'extra' => $extra
            ));
    }
}
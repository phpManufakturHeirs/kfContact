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
     * @param array $contact data
     * @return FormBuilder
     */
    protected function getForm($contact)
    {
        // we need the Tag's as a simple array!
        $tags = array();
        foreach ($contact['tag'] as $tag) {
            $tags[] = $tag['tag_name'];
        }

        // get the communication types and values
        $email = $this->ContactControl->getDefaultCommunicationRecord();
        $phone = $this->ContactControl->getDefaultCommunicationRecord();
        $fax = $this->ContactControl->getDefaultCommunicationRecord();
        $cell = $this->ContactControl->getDefaultCommunicationRecord();
        $url = $this->ContactControl->getDefaultCommunicationRecord();

        foreach ($contact['communication'] as $communication) {
            switch ($communication['communication_type']) {
                case 'EMAIL' :
                    $email = $communication;
                    break;
                case 'PHONE' :
                    $phone = $communication;
                    break;
                case 'FAX':
                    $fax = $communication;
                    break;
                case 'CELL':
                    $cell = $communication;
                    break;
                case 'URL':
                    $url = $communication;
                    break;
            }
        }

        // business (default) address
        $address_business = $this->ContactControl->getDefaultAddressRecord();
        // delivery address
        $address_delivery = $this->ContactControl->getDefaultAddressRecord();

        foreach ($contact['address'] as $address) {
            switch ($address['address_type']) {
                case 'BUSINESS' :
                    $address_business = $address;
                    break;
                case 'DELIVERY':
                    $address_delivery = $address;
                    break;
            }
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
            'label' => 'Category',
            'data' => $contact['category'][0]['category_name']
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

        // company
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

        // communication
        ->add('email_id', 'hidden', array(
            'data' => $email['communication_id']
        ))
        ->add('email_value', 'email', array(
            'label' => 'E-Mail',
            'data' => $email['communication_value']
        ))
        ->add('phone_id', 'hidden', array(
            'data' => $phone['communication_id']
        ))
        ->add('phone', 'text', array(
            'required' => false,
            'label' => 'Phone',
            'data' => $phone['communication_value']
        ))
        ->add('cell_id', 'hidden', array(
            'data' => $cell['communication_id']
        ))
        ->add('cell', 'text', array(
            'required' => false,
            'label' => 'Cell',
            'data' => $cell['communication_value']
        ))
        ->add('fax_id', 'hidden', array(
            'data' => $fax['communication_id']
        ))
        ->add('fax', 'text', array(
            'required' => false,
            'label' => 'Fax',
            'data' => $fax['communication_value']
        ))
        ->add('url_id', 'hidden', array(
            'data' => $url['communication_id']
        ))
        ->add('url', 'text', array(
            'required' => false,
            'label' => 'Homepage',
            'data' => $url['communication_value']
        ))

        // business address
        ->add('address_business_id', 'hidden', array(
            'data' => $address_business['address_id']
        ))
        ->add('address_business_street', 'text', array(
            'required' => false,
            'label' => 'Street',
            'data' => $address_business['address_street']
        ))
        ->add('address_business_zip', 'text', array(
            'required' => false,
            'label' => 'Zip',
            'data' => $address_business['address_zip']
        ))
        ->add('address_business_city', 'text', array(
            'required' => false,
            'label' => 'City',
            'data' => $address_business['address_city']
        ))
        ->add('address_business_country', 'choice', array(
            'choices' => $this->ContactControl->getCountryArrayForTwig(),
            'empty_value' => '- please select -',
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'label' => 'Country',
            'data' => $address_business['address_country_code']
        ))

        // delivery address
        ->add('address_delivery_id', 'hidden', array(
            'data' => $address_delivery['address_id']
        ))
        ->add('address_delivery_street', 'text', array(
            'required' => false,
            'label' => 'Street',
            'data' => $address_delivery['address_street']
        ))
        ->add('address_delivery_zip', 'text', array(
            'required' => false,
            'label' => 'Zip',
            'data' => $address_delivery['address_zip']
        ))
        ->add('address_delivery_city', 'text', array(
            'required' => false,
            'label' => 'City',
            'data' => $address_delivery['address_city']
        ))
        ->add('address_delivery_country', 'choice', array(
            'choices' => $this->ContactControl->getCountryArrayForTwig(),
            'empty_value' => '- please select -',
            'expanded' => false,
            'multiple' => false,
            'required' => false,
            'label' => 'Country',
            'data' => $address_delivery['address_country_code']
        ))

        ->add('note_id', 'hidden', array(
            'data' => $contact['note'][0]['note_id']
        ))
        ->add('note', 'textarea', array(
            'required' => false,
            'label' => 'Note',
            'data' => $contact['note'][0]['note_content']
        ))

        ;

        return $form->getForm();
    }

    protected function getFormData($data)
    {
        return array(
            'contact' => array(
                'contact_id' => $data['contact_id'],
                'contact_type' => $data['contact_type'],
                'contact_name' => $data['contact_name'],
                'contact_login' => $data['contact_login'],
                'contact_status' => $data['contact_status'],
            ),
            'company' => array(
                array(
                    'company_id' => $data['company_id'],
                    'contact_id' => $data['contact_id'],
                    'company_name' => $data['company_name'],
                    'company_department' => $data['company_department'],
                    'company_additional' => $data['company_additional'],
                    'company_additional_2' => $data['company_additional_2'],
                    'company_additional_3' => $data['company_additional_3'],
                    'company_primary_address_id' => $data['address_business_id'],
                    'company_primary_phone_id' => $data['phone_id'],
                    'company_primary_email_id' => $data['email_id'],
                    'company_primary_note_id' => $data['note_id']
                )
            ),
            'communication' => array(
                array(
                    // email
                    'communication_id' => $data['email_id'],
                    'contact_id' => $data['contact_id'],
                    'communication_type' => 'EMAIL',
                    'communication_value' => $data['email_value']
                ),
                array(
                    // phone
                    'communication_id' => $data['phone_id'],
                    'contact_id' => $data['contact_id'],
                    'communication_type' => 'PHONE',
                    'communication_value' => $data['phone']
                ),
                array(
                    // cell
                    'communication_id' => $data['cell_id'],
                    'contact_id' => $data['contact_id'],
                    'communication_type' => 'CELL',
                    'communication_value' => $data['cell']
                ),
                array(
                    // fax
                    'communication_id' => $data['fax_id'],
                    'contact_id' => $data['contact_id'],
                    'communication_type' => 'FAX',
                    'communication_value' => $data['fax']
                ),
                array(
                    // url
                    'communication_id' => $data['url_id'],
                    'contact_id' => $data['contact_id'],
                    'communication_type' => 'URL',
                    'communication_value' => $data['url']
                )
            ),
            'address' => array(
                array(
                    'address_id' => $data['address_business_id'],
                    'contact_id' => $data['contact_id'],
                    'address_type' => 'BUSINESS',
                    'address_street' => $data['address_business_street'],
                    'address_zip' => $data['address_business_zip'],
                    'address_city' => $data['address_business_city'],
                    'address_country_code' => $data['address_business_country']
                ),
                array(
                    'address_id' => $data['address_delivery_id'],
                    'contact_id' => $data['contact_id'],
                    'address_type' => 'DELIVERY',
                    'address_street' => $data['address_delivery_street'],
                    'address_zip' => $data['address_delivery_zip'],
                    'address_city' => $data['address_delivery_city'],
                    'address_country_code' => $data['address_delivery_country']
                )
            ),
            'note' => array(
                array(
                    'note_id' => $data['note_id'],
                    'contact_id' => $data['contact_id'],
                    'note_title' => 'Remarks',
                    'note_type' => 'TEXT',
                    'note_content' => $data['note']
                )
            )
        );
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
                $data = $this->getFormData($form->getData());
                echo "Check:<br><pre>";
                print_r($data);
                echo "</pre>";

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
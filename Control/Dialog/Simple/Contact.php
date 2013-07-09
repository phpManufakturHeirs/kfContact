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
use phpManufaktur\Contact\Control\Contact as ContactData;
use Symfony\Component\Form\FormBuilder;

class Contact extends Dialog {

    protected static $contact_id = -1;
    protected $ContactData = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app, $options=null)
    {
        parent::__construct($app);

        $this->setOptions(array(
            'template' => array(
                'namespace' => isset($options['template']['namespace']) ? $options['template']['namespace'] : '@phpManufaktur/Contact/Template',
                'message' => isset($options['template']['message']) ? $options['template']['message'] : 'backend/message.twig',
                'contact' => isset($options['template']['contact']) ? $options['template']['contact'] : 'backend/simple/contact.edit.twig'
            ),
            'route' => array(
                'action' => isset($options['route']['action']) ? $options['route']['action'] : '/admin/contact/simple/contact',
                'category' => isset($options['route']['category']) ? $options['route']['category'] : '/admin/contact/simple/category/list',
                'title' => isset($options['route']['title']) ? $options['route']['title'] : '/admin/contact/simple/title/list'
            )
        ));
        $this->ContactData = new ContactData($this->app);
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
        // create array for the birthday years
        $years = array();
        for ($i = date('Y')-18; $i > (date('Y')-100); $i--) {
            $years[] = $i;
        }
        $birthday_array = array(
            'label' => 'Birthday',
            'format' => 'ddMMyyyy',
            'years' => $years,
            'empty_value' => '',
            'required' => false
        );

        if (!isset($contact['person_0_person_birthday']) || ($contact['person_0_person_birthday'] === '0000-00-00')) {
            $contact['person_0_person_birthday'] = null;
        }
        if (isset($contact['person_0_person_birthday'])) {
            $birthday_array['data'] = new \DateTime($contact['person_0_person_birthday']);
        }

        return $this->app['form.factory']->createBuilder('form', $contact)
            // contact - hidden fields
            ->add('contact_type', 'hidden', array(
                'data' => 'PERSON'
            ))
            ->add('contact_id', 'hidden')

            // contact visible form fields
            ->add('contact_status', 'choice', array(
                'choices' => array('ACTIVE' => 'active', 'LOCKED' => 'locked', 'DELETED' => 'deleted'),
                'empty_value' => false,
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Status'
            ))

            // category - visible form fields
            ->add('category_0_category_name', 'choice', array(
                'choices' => $this->ContactData->getCategoryArrayForTwig(),
                'empty_value' => '- please select -',
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Category'
            ))

            // person - hidden fields
            ->add('person_0_person_id', 'hidden')
            ->add('person_0_contact_id', 'hidden')

            // person - visible form fields
            ->add('person_0_person_gender', 'choice', array(
                'choices' => array('MALE' => 'male', 'FEMALE' => 'female'),
                'expanded' => true,
                'label' => 'Gender'
            ))
            ->add('person_0_person_title', 'choice', array(
                'choices' => $this->ContactData->getTitleArrayForTwig(),
                'empty_value' => '- please select -',
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Title',
            ))
            ->add('person_0_person_first_name', 'text', array(
                'required' => false,
                'label' => 'First name'
            ))
            ->add('person_0_person_last_name', 'text', array(
                'required' => false,
                'label' => 'Last name'
            ))
            ->add('person_0_person_birthday', 'date', $birthday_array)

            // communication - hidden fields
            ->add('communication_email_communication_id', 'hidden', array(
                'data' => (!empty($contact['communication_email_communication_id'])) ? $contact['communication_email_communication_id'] : -1
            ))
            ->add('communication_email_contact_id', 'hidden')
            ->add('communication_email_communication_type', 'hidden', array(
                'data' => 'EMAIL'
            ))
            ->add('communication_email_communication_usage', 'hidden', array(
                'data' => 'PRIVATE'
            ))

            ->add('communication_phone_communication_id', 'hidden', array(
                'data' => (!empty($contact['communication_phone_communication_id'])) ? $contact['communication_phone_communication_id'] : -1
            ))
            ->add('communication_phone_contact_id', 'hidden')
            ->add('communication_phone_communication_type', 'hidden', array(
                'data' => 'PHONE'
            ))
            ->add('communication_phone_communication_usage', 'hidden', array(
                'data' => 'PRIVATE'
            ))

            ->add('communication_fax_communication_id', 'hidden', array(
                'data' => (!empty($contact['communication_fax_communication_id'])) ? $contact['communication_fax_communication_id'] : -1
            ))
            ->add('communication_fax_contact_id', 'hidden')
            ->add('communication_fax_communication_type', 'hidden', array(
                'data' => 'FAX'
            ))
            ->add('communication_fax_communication_usage', 'hidden', array(
                'data' => 'PRIVATE'
            ))

            // communication - visible form fields
            ->add('communication_phone_communication_value', 'text', array(
                'label' => 'Phone',
                'required' => false
            ))
            ->add('communication_fax_communication_value', 'text', array(
                'label' => 'Fax',
                'required' => false
            ))
            ->add('communication_email_communication_value', 'text', array(
                'label' => 'Email'
            ))

            // address - hidden fields
            ->add('address_0_address_id', 'hidden')
            ->add('address_0_contact_id', 'hidden')
            ->add('address_0_address_type', 'hidden', array(
                'data' => 'PRIVATE'
            ))

            // address - visible form fields
            ->add('address_0_address_street', 'text', array(
                'required' => false,
                'label' => 'Street'
            ))
            ->add('address_0_address_zip', 'text', array(
                'required' => false,
                'label' => 'Zip'
            ))
            ->add('address_0_address_city', 'text', array(
                'required' => false,
                'label' => 'City'
            ))
            ->add('address_0_address_country_code', 'choice', array(
                'choices' => $this->ContactData->getCountryArrayForTwig(),
                'empty_value' => '- please select -',
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Country'
            ))

            // note - hidden fields
            ->add('note_0_note_id', 'hidden', array(
                'data' => (!empty($contact['note_0_note_id'])) ? $contact['note_0_note_id'] : -1
            ))
            ->add('note_0_contact_id', 'hidden')
            ->add('note_0_note_title', 'hidden', array(
                'data' => 'Account note'
            ))
            ->add('note_0_note_type', 'hidden')

            // note - visible form fields
            ->add('note_0_note_content', 'textarea', array(
                'label' => 'Note',
                'required' => false
            ))
            ->getForm();
    }

    /**
     * Return the complete contact dialog and handle requests
     *
     * @return string contact dialog
     */
    public function exec($extra=null)
    {
        // check if a contact ID isset
        $form_request = $this->app['request']->request->get('form', array());
        if (isset($form_request['contact_id'])) {
            self::$contact_id = $form_request['contact_id'];
        }

        // get the contact array
        $contact = $this->ContactData->select(self::$contact_id);

        if (self::$contact_id < 1) {
            unset($contact['communication']);
        }

        // we dont need a multilevel and nested contact array, so flatten it
        $contact = $this->ContactData->levelDownContactArray($contact);

        if ($this->ContactData->isMessage()) {
            self::$message = $this->ContactData->getMessage();
        }

        // get the form
        $form = $this->getForm($contact);

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                // get the form data
                $contact = $form->getData();

                // the form submit a datetime object but we need a string
                $contact['person_0_person_birthday'] = (isset($contact['person_0_person_birthday']) && is_object($contact['person_0_person_birthday'])) ? date('Y-m-d', $contact['person_0_person_birthday']->getTimestamp()) : '0000-00-00';

                // build a regular contact array
                $contact = $this->ContactData->levelUpContactArray($contact);

                if (self::$contact_id < 1) {
                    // insert a new record
                    $this->ContactData->insert($contact, self::$contact_id);
                }
                else {
                    // update the record
                    $has_changed = false; // indicate changes
                    $this->ContactData->update($contact, self::$contact_id, $has_changed);
                }

                if (!$this->ContactData->isMessage()) {
                    $this->setMessage("The contact process has not returned a status message");
                }
                else {
                    // use the return status messages
                    self::$message = $this->ContactData->getMessage();
                }

                // get the values of the new or updated record
                $contact = $this->ContactData->select(self::$contact_id);
                // build a flatten array
                $contact = $this->ContactData->levelDownContactArray($contact);
                // get the form
                $form = $this->getForm($contact);
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
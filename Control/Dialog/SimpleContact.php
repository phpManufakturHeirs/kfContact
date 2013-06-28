<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Dialog;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Title;
use phpManufaktur\Contact\Data\Contact\Country;
use phpManufaktur\Contact\Control\Contact;

class SimpleContact {

    protected $app = null;
    protected static $contact_id = -1;
    protected static $message = '';
    protected $Contact = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        // set the content language
        $this->app['translator']->setLocale('de');
        $this->Contact = new Contact($this->app);
    }

    public function setContactID($contact_id)
    {
        self::$contact_id = $contact_id;
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
        self::$message .= $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'message.twig'),
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


    protected function getForm($contact)
    {
        // get the title array
        $title = new Title($this->app);
        $title_array = $title->getArrayForTwig();

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

        // get the country array
        $country = new Country($this->app);
        $country_array = $country->getArrayForTwig();

        return $this->app['form.factory']->createBuilder('form', $contact)
            // contact visible form fields
            ->add('contact_id', 'text', array(
                'read_only' => true
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
                'choices' => $title_array,
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Title',
                'data' => 'cc'
            ))
            ->add('person_0_person_primary_name', 'text', array(
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
                'choices' => $country_array,
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Country'
            ))
            ->getForm();
    }

    public function exec()
    {
        // check if a contact ID isset
        $form_request = $this->app['request']->request->get('form', array());
        if (isset($form_request['contact_id'])) {
            self::$contact_id = $form_request['contact_id'];
        }

        // get the contact array
        $contact = $this->Contact->select(self::$contact_id);

        if (self::$contact_id < 1) {
            unset($contact['communication']);
        }

        // we dont need a multilevel and nested contact array, so flatten it
        $contact = $this->Contact->levelDownContactArray($contact);


        $form = $this->getForm($contact);

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                // get the form data
                $contact = $form->getData();
                // build a regular contact array
                echo "<pre>";
                print_r($contact);
                echo "</pre>";
                $contact = $this->Contact->levelUpContactArray($contact);

                if (self::$contact_id < 1) {
                    // insert a new record
                    echo "<pre>";
                    print_r($contact);
                    echo "</pre>";

                    if (!$this->Contact->insert($contact, self::$contact_id)) {
                        self::$message = $this->Contact->getMessage();
                    }
                    else {
                        $this->setMessage("Inserted the new contact with the ID %contact_id%.", array('%contact_id%' => self::$contact_id));
                        // get the values of the new record
                        $contact = $this->Contact->select(self::$contact_id);
                        // build a flatten array
                        $contact = $this->Contact->levelDownContactArray($contact);
                    }
                }


            }
            else {
                // general error (timeout, CSFR ...)
                $this->setMessage('The form is not valid, please check your input and try again!');
            }
        }

        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'simple.person.contact.twig'),
            array(
                'message' => $this->getMessage(),
                'form' => $form->createView()
            ));
    }
}
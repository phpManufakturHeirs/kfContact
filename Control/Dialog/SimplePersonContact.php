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
use phpManufaktur\Contact\Control\Contact as ContactControl;

class SimplePersonContact
{

    protected $app = null;
    protected static $contact_id = -1;
    protected static $message = '';

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

    /**
     * Clear the existing message(s)
     */
    public function clearMessage()
    {
        self::$message = '';
    }

    protected function getForm($data=array())
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

        if (!isset($data['person_birthday']) || ($data['person_birthday'] === '0000-00-00')) {
            $data['person_birthday'] = null;
        }
        if (isset($data['person_birthday'])) {
            $birthday_array['data'] = new \DateTime($data['person_birthday']);
        }

        // get the country array
        $country = new Country($this->app);
        $country_array = $country->getArrayForTwig();

        return $this->app['form.factory']->createBuilder('form', $data)
            ->add('contact_id', 'text', array(
                'read_only' => true,
                'required' => false,
            ))
            ->add('person_id', 'hidden')
            ->add('fax_id', 'hidden')
            ->add('email_id', 'hidden')
            ->add('address_id', 'hidden')
            ->add('note_id', 'hidden')
            ->add('phone_id', 'hidden')
            ->add('person_contact_since', 'hidden')

            ->add('person_gender', 'choice', array(
                'choices' => array('MALE' => 'male', 'FEMALE' => 'female'),
                'expanded' => true,
                'required' => false,
                'label' => 'gender'
            ))
            ->add('person_title', 'choice', array(
                'choices' => $title_array,
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Title'
            ))
            ->add('person_primary_name', 'text', array(
                'required' => false,
                'label' => 'First name'
            ))
            ->add('person_last_name', 'text', array(
                'required' => false,
                'label' => 'Last name'
            ))
            ->add('person_birthday', 'date', $birthday_array)
            ->add('phone', 'text', array(
                'required' => false
            ))
            ->add('fax', 'text', array(
                'required' => false
            ))
            ->add('email', 'text', array(
                'required' => true
            ))
            ->add('address_street', 'text', array(
                'required' => false,
                'label' => 'Street'
            ))
            ->add('address_zip', 'text', array(
                'required' => false,
                'label' => 'Zip'
            ))
            ->add('address_city', 'text', array(
                'required' => false,
                'label' => 'City'
            ))
            ->add('address_country_code', 'choice', array(
                'choices' => $country_array,
                'expanded' => false,
                'multiple' => false,
                'required' => false,
                'label' => 'Country'
            ))
            ->add('note_content', 'textarea', array(
                'required' => false,
                'label' => 'Note'
            ))
            ->getForm();
    }

    /**
     * The contact control return a multilevel array with all available contact
     * data. For this dialog we dont need such a complex structure, so we flatten
     * the data to only one level and ignore the information we dont need
     *
     * @param array $data
     */
    protected function flattenContactData($data)
    {
        if (isset($data['contact']['contact_id'])) {
            self::$contact_id = $data['contact']['contact_id'];
        }
        // level down the nested data array ...
        foreach ($data['contact'] as $key => $value) {
            $data[$key] = $value;
        }
        unset($data['contact']);

        // we need only the first person data record!
        foreach ($data['person'][0] as $key => $value) {
            if ($key == 'contact_id') continue;
            $data[$key] = $value;
        }
        unset($data['person']);

        // we need only the first address of this contact
        foreach ($data['address'][0] as $key => $value) {
            if ($key == 'contact_id') continue;
            $data[$key] = $value;
        }
        unset($data['address']);

        // we need only EMAIL, PHONE and FAX
        foreach ($data['communication'] as $communication) {
            switch ($communication['communication_type']) {
                case 'EMAIL':
                    $data['email'] = $communication['communication_value'];
                    $data['email_id'] = $communication['communication_id'];
                    break;
                case 'PHONE':
                    $data['phone'] = $communication['communication_value'];
                    $data['phone_id'] = $communication['communication_id'];
                    break;
                case 'FAX':
                    $data['fax'] = $communication['communication_value'];
                    $data['fax_id'] = $communication['communication_id'];
                    break;
            }
        }
        unset($data['communication']);

        // we need only the first note
        foreach ($data['note'][0] as $key => $value) {
            if ($key == 'contact_id') continue;
            $data[$key] = $value;
        }
        unset($data['note']);

        // we don't need the company informations for this dialog
        unset($data['company']);

        // return the array
        return $data;
    }

    /**
     * We had used a simple data structure, but the contact control expect a
     * multilevel array containing the contact data, here we build it
     *
     * @param array $data
     * @return array
     */
    protected function buildContactData($data)
    {
        // collect the form data
        $result = array(
            // contact main information
            'contact' => array(
                'contact_id' => isset($data['contact_id']) ? $data['contact_id'] : -1,
                'contact_type' => 'PERSON',
            ),
            // person record
            'person' => array(
                array(
                    'person_id' => isset($data['person_id']) ? $data['person_id'] : -1,
                    'person_gender' => isset($data['person_gender']) ? $data['person_gender'] : 'MALE',
                    'person_title' => isset($data['person_title']) ? $data['person_title'] : '',
                    'person_primary_name' => isset($data['person_primary_name']) ? $data['person_primary_name'] : '',
                    'person_last_name' => isset($data['person_last_name']) ? $data['person_last_name'] : '',
                    'person_birthday' => $data['person_birthday'],
                    'person_contact_since' => isset($data['person_contact_since']) ? $data['person_contact_since'] : date('Y-m-d H:i:s'),
                    'person_primary_address_id' => isset($data['person_primary_address_id']) ? $data['person_primary_address_id'] : -1,
                    'person_primary_phone_id' => isset($data['phone_id']) ? $data['phone_id'] : -1,
                    'person_primary_email_id' => isset($data['email_id']) ? $data['email_id'] : -1,
                    'person_primary_note_id' => isset($data['person_primary_note_id']) ? $data['person_primary_note_id'] : -1,
                    'person_status' => isset($data['person_status']) ? $data['person_status'] : 'ACTIVE'
                    )
            ),
            // the communication entries
            'communication' => array(
                array(
                    'communication_id' => isset($data['email_id']) ? $data['email_id'] : -1,
                    'communication_type' => 'EMAIL',
                    'communication_value' => strtolower($data['email'])
                )
            ),
            // the address
            'address' => array(),
            // remarks and notes
            'note' => array(
                array(
                    'note_id' => isset($data['note_id']) ? $data['note_id'] : -1,
                    'note_type' => 'TEXT',
                    'note_title' => 'General contact note',
                    'note_content' => isset($data['note_content']) ? $data['note_content'] : '',
                    'note_status' => 'ACTIVE'
                )
            )
        );
        if (!empty($data['fax'])) {
            $result['communication'][] = array(
                'communication_id' => isset($data['fax_id']) ? $data['fax_id'] : -1,
                'communication_type' => 'FAX',
                'communication_value' => $data['fax']
            );
        }
        if (!empty($data['phone'])) {
            $result['communication'][] = array(
                'communication_id' => isset($data['phone_id']) ? $data['phone_id'] : -1,
                'communication_type' => 'PHONE',
                'communication_value' => $data['phone']
            );
        }
        // insert the address only, if needed
        if (empty($data['address_street']) && empty($data['address_zip']) && empty($data['address_city'])) {
            if ($data['address_id'] > 0) {
                $result['address'][] = array(
                    'address_id' => isset($data['address_id']) ? $data['address_id'] : -1,
                    'address_type' => 'PRIVATE',
                    'address_street' => isset($data['address_street']) ? $data['address_street'] : '',
                    'address_zip' => isset($data['address_zip']) ? $data['address_zip'] : '',
                    'address_city' => isset($data['address_city']) ? $data['address_city'] : '',
                    'address_country_code' => isset($data['address_country_code']) ? $data['address_country_code'] : ''
                );
            }
        }
        else {
            $result['address'][] = array(
                'address_id' => isset($data['address_id']) ? $data['address_id'] : -1,
                'address_type' => 'PRIVATE',
                'address_street' => isset($data['address_street']) ? $data['address_street'] : '',
                'address_zip' => isset($data['address_zip']) ? $data['address_zip'] : '',
                'address_city' => isset($data['address_city']) ? $data['address_city'] : '',
                'address_country_code' => isset($data['address_country_code']) ? $data['address_country_code'] : ''
            );
        }

        return $result;
    }

    public function exec()
    {
        $Contact = new ContactControl($this->app);
        $form_request = $this->app['request']->request->get('form', array());
        if (isset($form_request['contact_id'])) {
            self::$contact_id = $form_request['contact_id'];
        }

        // get the values of the record or defaults
        $data = $Contact->select(self::$contact_id);
$Contact->flattenContactArray($data);
        // build a flatten array
        $data = $this->flattenContactData($data);
/*
echo "<pre>";
print_r($data);
echo "</pre>";
*/
        if ($Contact->isMessage()) {
            self::$message = $Contact->getMessage();
        }
        if ('POST' == $this->app['request']->getMethod()) {
            // form was submitted
            $form = $this->getForm();
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                if (isset($data['contact_id'])) {
                    self::$contact_id = $data['contact_id'];
                }

                // the form submit a datetime object but we need a string
                $data['person_birthday'] = (isset($data['person_birthday']) && is_object($data['person_birthday'])) ? date('Y-m-d', $data['person_birthday']->getTimestamp()) : '0000-00-00';

                $insert = $this->buildContactData($data);

                if (!$this->isMessage()) {
                    // ok - insert or update the data
                    if (self::$contact_id < 1) {
                        if (!$Contact->insert($insert, self::$contact_id)) {
                            self::$message = $Contact->getMessage();
                        }
                        else {
                            $this->setMessage("Inserted the new contact with the ID %contact_id%.", array('%contact_id%' => self::$contact_id));
                            // get the values of the new record
                            $data = $Contact->select(self::$contact_id);
                            // build a flatten array
                            $data = $this->flattenContactData($data);
                        }
                    }
                    else {
                        // update existing record
                        if (!$Contact->update($insert, self::$contact_id)) {
                            self::$message = $Contact->getMessage();
                            if (!$this->isMessage()) {
                                $this->setMessage("The update returned 'FALSE' but no message ...");
                            }
                        }
                        elseif ($Contact->isMessage()) {
                            self::$message = $Contact->getMessage();
                        }
                        else {
                            $this->setMessage("The contact with the ID %contact_id% was successfull updated.", array('%contact_id%' => self::$contact_id));
                        }
                    }
                }
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setMessage('The form is not valid, please check your input and try again!');
            }
        }

        $form = $this->getForm($data);
        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'simple.person.contact.twig'),
            array(
                'message' => $this->getMessage(),
                'form' => $form->createView()
            ));
    }
}
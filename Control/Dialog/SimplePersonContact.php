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
            'required' => false
        );
        if (isset($address['person_birthday'])) {
            $birthday_array['data'] = $data['person_birthday'];
        }
        else {
            $birthday_array['empty_value'] = '';
        }

        // get the country array
        $country = new Country($this->app);
        $country_array = $country->getArrayForTwig();

        return $this->app['form.factory']->createBuilder('form', $data)
            ->add('contact_type', 'hidden', array(
                'data' => 'PERSON'
            ))
            ->add('contact_id', 'text', array(
                'read_only' => true,
                'required' => false,
            ))
            ->add('person_id', 'text', array(
                'read_only' => true,
                'required' => false
            ))
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
            ->add('person_first_name', 'text', array(
                'required' => false,
                'label' => 'First name'
            ))
            ->add('person_last_name', 'text', array(
                'required' => false,
                'label' => 'Last name'
            ))
            ->add('person_nick_name', 'text', array(
                'required' => false,
                'label' => 'Nickname'
            ))
            ->add('person_birthday', 'date', $birthday_array)
            ->add('phone', 'text', array(
                'required' => false
            ))
            ->add('fax', 'text', array(
                'required' => false
            ))
            ->add('email', 'text', array(

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
            ->add('address_country', 'choice', array(
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
        $Contact = new ContactControl($this->app);

        // get the values of the record or defaults
        $data = $Contact->select(self::$contact_id);
        // level down the data array ...
        foreach ($data['contact'] as $key => $value)
            $data[$key] = $value;
        unset($data['contact']);

        foreach ($data['person'] as $key => $value)
            $data[$key] = $value;
        unset($data['person']);
        $data['person_birthday'] = null;

        foreach ($data['address'][0] as $key => $value)
            $data[$key] = $value;
        unset($data['address']);

        foreach ($data['communication'] as $communication) {
            switch ($communication['communication_type']) {
                case 'EMAIL':
                    $data['email'] = $communication['communication_value'];
                    break;
                case 'PHONE':
                    $data['phone'] = $communication['communication_value'];
                    break;
                case 'FAX':
                    $data['fax'] = $communication['communication_value'];
                    break;
            }
        }
        unset($data['communication']);

        if ($Contact->isMessage()) {
            self::$message = $Contact->getMessage();
        }
        if ('POST' == $this->app['request']->getMethod()) {
            // form was submitted
            $form = $this->getForm();
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                // check the data
                $insert = array(
                    'contact' => array(
                        'contact_name' => isset($data['contact_name']) ? $data['contact_name'] : '',
                        'contact_login' => isset($data['contact_login']) ? $data['contact_login'] : '',
                        'contact_type' => isset($data['contact_type']) ? $data['contact_type'] : 'PERSON',
                        'contact_status' => isset($data['contact_status']) ? $data['contact_status'] : 'ACTIVE'
                    ),
                    'person' => array(
                        'person_gender' => isset($data['person_gender']) ? $data['person_gender'] : 'MALE',
                        'person_title' => isset($data['person_title']) ? $data['person_title'] : '',
                        'person_first_name' => isset($data['person_first_name']) ? $data['person_first_name'] : '',
                        'person_last_name' => isset($data['person_last_name']) ? $data['person_last_name'] : '',
                        'person_nick_name' => isset($data['person_nick_name']) ? $data['person_nick_name'] : '',
                        'person_birthday' => (isset($data['person_birthday']) && is_object($data['person_birthday'])) ? date('Y-m-d', $data['person_birthday']->getTimestamp()) : '0000-00-00',
                        'person_contact_since' => isset($data['person_contact_since']) ? $data['person_contact_since'] : date('Y-m-d H:i:s'),
                        'person_primary_address_id' => isset($data['person_primary_address_id']) ? $data['person_primary_address_id'] : -1,
                        'person_primary_company_id' => isset($data['person_primary_company_id']) ? $data['person_primary_company_id'] : -1,
                        'person_primary_phone_id' => isset($data['person_primary_phone_id']) ? $data['person_primary_phone_id'] : -1,
                        'person_primary_email_id' => isset($data['person_primary_email_id']) ? $data['person_primary_email_id'] : -1,
                        'person_primary_note_id' => isset($data['person_primary_note_id']) ? $data['person_primary_note_id'] : -1,
                        'person_status' => isset($data['person_status']) ? $data['person_status'] : 'ACTIVE'
                    )
                );
                $insert['communication'] = array(
                    array(
                        'communication_type' => 'EMAIL',
                        'communication_value' => strtolower($data['email'])
                    )
                );
                if (!empty($data['fax'])) {
                    $insert['communication'][] = array(
                        'communication_type' => 'FAX',
                        'communication_value' => $data['fax']
                    );
                }
                if (!empty($data['phone'])) {
                    $insert['communication'][] = array(
                        'communication_type' => 'PHONE',
                        'communication_value' => $data['phone']
                    );
                }
                $insert['address'] = array(
                    array(
                        'address_type' => 'PRIVATE',
                        'address_street' => isset($data['address_street']) ? $data['address_street'] : '',
                        'address_zip' => isset($data['address_zip']) ? $data['address_zip'] : '',
                        'address_city' => isset($data['address_city']) ? $data['address_city'] : '',
                        'address_country_code' => isset($data['address_country']) ? $data['address_country'] : ''
                    )
                );

                $this->clearMessage();
                if (!$Contact->validate($insert)) {
                    self::$message = $Contact->getMessage();
                }

                if (!$this->isMessage()) {
                    // ok - insert or update the data
                    if (!$Contact->insert($insert, self::$contact_id)) {
                        self::$message = $Contact->getMessage();
                    }
                    else {
                        $this->setMessage("Inserted the new contact with the ID %contact_id%.", array('%contact_id%' => self::$contact_id));
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
<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control;

use Silex\Application;
use Symfony\Component\Validator\Constraints as Assert;
use phpManufaktur\Contact\Data\Contact\Communication;
use phpManufaktur\Contact\Data\Contact\Person as PersonData;

class ContactPerson
{
    protected $app = null;
    protected $CommunicationData = null;
    protected $PersonData = null;

    protected static $contact_record = array();

    protected static $contact_id = -1;
    protected static $person_id = -1;
    protected static $gender = 'MALE';
    protected static $title = '';
    protected static $first_name = '';
    protected static $last_name = '';
    protected static $nick_name = '';
    protected static $birthday = '0000-00-00';
    protected static $contact_since = '0000-00-00 00:00:00';
    protected static $primary_address_id = -1;
    protected static $primary_company_id = -1;
    protected static $primary_phone_id = -1;
    protected static $primary_email_id = -1;
    protected static $primary_note_id = -1;
    protected static $status = 'ACTIVE';
    protected static $timestamp = '0000-00-00 00:00:00';

    // virtual fields
    protected static $email = '';
    protected static $phone = '';
    protected static $mobile = '';
    protected static $fax = '';

    protected static $message = '';

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->CommunicationData = new Communication($this->app);
        $this->PersonData = new PersonData($this->app);
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

    /**
     * Return a default (empty) PERSON contact record.
     * Contains virtual fields i.e. for the email address and other
     *
     * @return array
     */
    public function getContactRecord()
    {
        return array(
            'person_id' => self::$person_id,
            'contact_id' => self::$contact_id,
            'gender' => self::$gender,
            'title' => self::$title,
            'first_name' => self::$first_name,
            'last_name' => self::$last_name,
            'nick_name' => self::$nick_name,
            'birthday' => self::$birthday,
            'contact_since' => self::$contact_since,
            'primary_address_id' => self::$primary_address_id,
            'primary_company_id' => self::$primary_company_id,
            'primary_phone_id' => self::$primary_phone_id,
            'primary_email_id' => self::$primary_email_id,
            'primary_note_id' => self::$primary_note_id,
            'status' => self::$status,
            'timestamp' => self::$timestamp,
            'email' => self::$email,
            'phone' => self::$phone,
            'mobile' => self::$mobile,
            'fax' => self::$fax
        );
    }

    /**
     * Set the PERSON contact record with the given data array
     *
     * @param array $contact
     */
    public function setContactRecord($contact)
    {
        self::$contact_record = array(
            'person_id' => isset($contact['person_id']) ? $contact['person_id'] : self::$person_id,
            'contact_id' => isset($contact['contact_id']) ? $contact['contact_id'] : self::$contact_id,
            'gender' => isset($contact['gender']) ? $contact['gender'] : self::$gender,
            'title' => isset($contact['title']) ? $contact['title'] : self::$title,
            'first_name' => isset($contact['first_name']) ? $contact['first_name'] : self::$first_name,
            'last_name' => isset($contact['last_name']) ? $contact['last_name'] : self::$last_name,
            'nick_name' => isset($contact['nick_name']) ? $contact['nick_name'] : self::$nick_name,
            'birthday' => isset($contact['birthday']) ? $contact['birthday'] : self::$birthday,
            'contact_since' => isset($contact['contact_since']) ? $contact['contact_since'] : self::$contact_since,
            'primary_address_id' => isset($contact['primary_address_id']) ? $contact['primary_address_id'] : self::$primary_address_id,
            'primary_company_id' => isset($contact['primary_company_id']) ? $contact['primary_company_id'] : self::$primary_company_id,
            'primary_phone_id' => isset($contact['primary_phone_id']) ? $contact['primary_phone_id'] : self::$primary_phone_id,
            'primary_email_id' => isset($contact['primary_email_id']) ? $contact['primary_email_id'] : self::$primary_email_id,
            'primary_note_id' => isset($contact['primary_note_id']) ? $contact['primary_note_id'] : self::$primary_note_id,
            'status' => isset($contact['status']) ? $contact['status'] : self::$status,
            'timestamp' => isset($contact['timestamp']) ? $contact['timestamp'] : self::$timestamp,
            'status' => isset($contact['status']) ? $contact['status'] : self::$status,
            'timestamp' => self::$timestamp,
            'email' => isset($contact['email']) ? $contact['email'] : self::$email,
            'phone' => isset($contact['phone']) ? $contact['phone'] : self::$phone,
            'mobile' => isset($contact['mobile']) ? $contact['mobile'] : self::$mobile,
            'fax' => isset($contact['fax']) ? $contact['fax'] : self::$fax
        );
    }

    /**
     * Set the contact_id
     *
     * @param integer $contact_id
     */
    public function setContactID($contact_id)
    {
        self::$contact_id = $contact_id;
    }

    /**
     * Validate the given PERSON data record
     *
     * @param array $data
     * @return boolean
     */
    public function validate($data)
    {
        $this->clearMessage();
        $check = true;
        foreach ($data as $key => $value) {
            switch ($key) {
                case 'email':
                    $errors = $this->app['validator']->validateValue($value, new Assert\Email());
                    if (count($errors) > 0) {
                        $this->setMessage('The submitted email address is not valid, please check your input!');
                        $check = false;
                    }
                    break;
                case 'last_name':
                    $errors = $this->app['validator']->validateValue($value, new Assert\Length(array('min' => 2)));
                    if (count($errors) > 0) {
                        $this->setMessage('The last name must be at least two characters long!');
                        $check = false;
                    }
                    break;
            }
        }
        return $check;
    }

    /**
     * Insert a new PERSON record. Check first for values which belong to depending
     * contact tables
     *
     * @param array $data
     * @param integer $contact_id
     * @param string $person_id
     * @throws ContactException
     * @return boolean
     */
    public function insert($data, $contact_id, &$person_id=null)
    {
        try {
            self::$contact_id = $contact_id;
            if (isset($data['email'])) {
                if ($this->CommunicationData->exists($contact_id, 'EMAIL', strtolower($data['email']))) {
                    $this->setMessage('The email %email% is already in use for the contact ID %contact_id%.',
                        array('%email%' => $data['email'], '%contact_id%' => self::$contact_id));
                    return false;
                }
                $insert = array(
                    'contact_id' => self::$contact_id,
                    'communication_type' => 'EMAIL',
                    'value' => strtolower($data['email'])
                );
                $this->CommunicationData->insert($insert, self::$primary_email_id);
                $this->app['monolog']->addInfo("Inserted email {$data['email']} to the communication table");
            }


            // ok - now we submit the PERSON
            $insert = array(
                'contact_id' => self::$contact_id,
                'gender' => isset($data['gender']) ? $data['gender'] : self::$gender,
                'title' => isset($data['title']) ? $data['title'] : self::$title,
                'first_name' => isset($data['first_name']) ? $data['first_name'] : self::$first_name,
                'last_name' => isset($data['last_name']) ? $data['last_name'] : self::$last_name,
                'nick_name' => isset($data['nick_name']) ? $data['nick_name'] : self::$nick_name,
                'birthday' => isset($data['birthday']) ? $data['birthday'] : self::$birthday,
                'contact_since' => date('Y-m-d H:i:s'),
                'primary_address_id' => self::$primary_address_id,
                'primary_company_id' => self::$primary_company_id,
                'primary_phone_id' => self::$primary_phone_id,
                'primary_email_id' => self::$primary_email_id,
                'primary_note_id' => self::$primary_note_id,
                'status' => isset($data['status']) ? $data['status'] : self::$status
            );
            $this->PersonData->insert($insert, self::$person_id);
            $person_id = self::$person_id;
            $this->app['monolog']->addInfo("Inserted person record for the email {$data['email']}");
            return true;
        } catch (\Exception $e) {
            throw new ContactException($e);
        }
    }
}


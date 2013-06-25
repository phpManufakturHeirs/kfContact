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
use phpManufaktur\Contact\Data\Contact\Contact as ContactData;

class Contact extends ContactParent
{

    protected static $contact_id = -1;
    protected static $person_id = -1;

    protected static $name = '';
    protected static $login = '';
    protected static $status = 'ACTIVE';
    protected static $timestamp = '0000-00-00 00:00:00';
    protected static $type = 'PERSON';
    protected static $person = null;
    protected static $company = null;

    protected $ContactData = null;
    protected $ContactPerson = null;
    protected $ContactCompany = null;
    protected $ContactCommunication = null;
    protected $ContactAddress = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->ContactPerson = new ContactPerson($this->app);
        $this->ContactData = new ContactData($this->app);
        $this->ContactCommunication = new ContactCommunication($this->app);
        $this->ContactAddress = new ContactAddress($this->app);
    }

    /**
     * Get the contact record for this contact_id
     */
    public function getDefaultRecord()
    {
        $data = array(
            'contact' => array(
                'contact_id' => -1,
                'contact_name' => '',
                'contact_login' => '',
                'contact_type' => self::$type,
                'contact_status' => 'ACTIVE',
                'contact_timestamp' => '0000-00-00 00:00:00',
            )
        );

        if (self::$type === 'PERSON') {
            $data['person'] = array($this->ContactPerson->getDefaultRecord());
        }
        else {
            throw ContactException::contactTypeNotSupported(self::$contact_type);
        }

        // default communication entry
        $data['communication'] = array(
            $this->ContactCommunication->getDefaultRecord()
        );

        // default address entry
        $data['address'] = array(
            $this->ContactAddress->getDefaultRecord()
        );

        return $data;
    }

    /**
     * General select function for contact records.
     * The identifier can be the contact_id or the login name.
     * Return a PERSON or a COMPANY record. If the identifier not exists return
     * a default contact array.
     *
     * @param mixed $identifier
     */
    public function select($identifier)
    {
        if (is_numeric($identifier)) {
            self::$contact_id = $identifier;
            if (self::$contact_id < 1) {
                return $this->getDefaultRecord();
            }
            else {
                if (false === ($contact = $this->ContactData->select(self::$contact_id))) {
                    self::$contact_id = -1;
                    $this->setMessage("The contact with the ID %contact_id% does not exists!", array('%contact_id%' => $identifier));
                    return $this->getDefaultRecord();
                }
                $contact = $this->ContactData->selectContact(self::$contact_id);
                return $contact;
            }
        }
        else {
            echo "string";
        }
    }

    /**
     * Validate the given $data record for all contact types
     *
     * @param array $data
     * @return boolean
     */
    public function validate($data)
    {
        $message = '';
        $check = true;

        if (isset($data['communication'])) {
            if (!$this->ContactCommunication->validate($data)) {
                $message .= $this->ContactCommunication->getMessage();
                $check = false;
            }
        }

        self::$message = $message;
        return $check;
    }

    /**
     * Insert the given $data record into the contact database. Process all needed
     * steps, uses transaction and roll back if necessary.
     *
     * @param array $data
     * @param reference integer $contact_id
     * @throws ContactException
     * @return boolean
     */
    public function insert($data, &$contact_id=null)
    {
        if (!isset($data['contact']['contact_login']) || empty($data['contact']['contact_login'])) {
            if (isset($data['communication'])) {
                foreach ($data['communication'] as $communication) {
                    if (isset($communication['communication_type']) && isset($communication['communication_value']) &&
                        !empty($communication['communication_value']) && ($communication['communication_type'] === 'EMAIL')) {
                        $data['contact']['contact_login'] = $communication['communication_value'];
                        break;
                    }
                }
            }
            if (!isset($data['contact']['contact_login']) || empty($data['contact']['contact_login'])) {
                $this->setMessage('The contact record must contain a email address or a login name as unique identifier!');
                return false;
            }
        }
        if (false !== ($check = $this->ContactData->selectLogin($data['contact']['contact_login']))) {
            $this->setMessage('The login <b>%login%</b> is already in use, please choose another one!',
                array('%login%' => $data['contact']['contact_login']));
            return false;
        }
        if (!isset($data['contact']['contact_name']) || empty($data['contact']['contact_name'])) {
            $data['contact']['contact_name'] = $data['contact']['contact_login'];
        }
        try {
            // BEGIN TRANSACTION
            $this->app['db']->beginTransaction();

            // first step: insert a contact record
            $this->ContactData->insert($data['contact'], self::$contact_id);
            $contact_id = self::$contact_id;

            // check the communication
            if (isset($data['communication'])) {
                foreach ($data['communication'] as $communication) {
                    $communication_id = -1;
                    if ($this->ContactCommunication->insert($communication, self::$contact_id, $communication_id)) {
                        switch ($communication['communication_type']) {
                            case 'EMAIL':
                                if (self::$type === 'PERSON') {
                                    if (!isset($data['person']['person_primary_email_id']) ||
                                        (isset($data['person']['person_primary_email_id']) && ($data['person']['person_primary_email_id'] < 1))) {
                                        $data['person']['person_primary_email_id'] = $communication_id;
                                    }
                                }
                                else {
                                    throw ContactException::contactTypeNotSupported(self::$type);
                                }
                                break;
                            case 'PHONE':
                                if (self::$type === 'PERSON') {
                                    if (!isset($data['person']['person_primary_phone_id']) ||
                                        (isset($data['person']['person_primary_phone_id']) && ($data['person']['person_primary_phone_id'] < 1))) {
                                        $data['person']['person_primary_phone_id'] = $communication_id;
                                    }
                                }
                                else {
                                    throw ContactException::contactTypeNotSupported(self::$type);
                                }
                                break;
                        }
                    }
                    else {
                        // rollback and return to the dialog
                        $this->app['db']->rollback();
                        self::$message = $this->ContactCommunication->getMessage();
                        return false;
                    }
                }
            }

            if (isset($data['address'])) {
                foreach ($data['address'] as $address) {
                    // loop through the addresses
                    $address_id = -1;
                    if ($this->ContactAddress->insert($address, $contact_id, $address_id)) {
                        if (self::$type === 'PERSON') {
                            if (!isset($data['person']['person_primary_address_id']) ||
                                (isset($data['person']['person_primary_address_id']) && ($data['person']['person_primary_address_id'] < 1))) {
                                // pick the first address as primary address
                                $data['person']['person_primary_address_id'] = $address_id;
                                break;
                            }
                        }
                        else {
                            throw ContactException::contactTypeNotSupported(self::$type);
                        }
                    }
                    else {
                        // rollback and return to the dialog
                        $this->app['db']->rollback();
                        self::$message = $this->ContactAddress->getMessage();
                        return false;
                    }
                }
            }

            // insert the contact record for the person
            if ((self::$type === 'PERSON') && isset($data['person']) &&
                !$this->ContactPerson->insert($data['person'], self::$contact_id, self::$person_id)) {
                // something went wrong, rollback and return with message
                $this->app['db']->rollback();
                self::$message = $this->ContactPerson->getMessage();
                return false;
            }

            // COMMIT TRANSACTION
            $this->app['db']->commit();
            return true;
        } catch (\Exception $e) {
            // ROLLBACK TRANSACTION
            $this->app['db']->rollback();
            throw new ContactException($e);
        }
    }
}


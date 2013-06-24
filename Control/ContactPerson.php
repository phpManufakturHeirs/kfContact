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
use phpManufaktur\Contact\Data\Contact\Communication;
use phpManufaktur\Contact\Data\Contact\Person as PersonData;

class ContactPerson
{
    protected $app = null;
    protected $CommunicationData = null;
    protected $PersonData = null;
    protected $ContactCommunication = null;

   // protected static $contact_record = array();

    protected static $contact_id = -1;
    protected static $person_id = -1;

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
        $this->ContactCommunication = new ContactCommunication($this->app);
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
    public function getDefaultRecord()
    {
        return array(
            'person_id' => -1,
            'contact_id' => -1,
            'person_gender' => 'MALE',
            'person_title' => '',
            'person_first_name' => '',
            'person_last_name' => '',
            'person_nick_name' => '',
            'person_birthday' => '0000-00-00',
            'person_contact_since' => '0000-00-00 00:00:00',
            'person_primary_address_id' => -1,
            'person_primary_company_id' => -1,
            'person_primary_phone_id' => -1,
            'person_primary_email_id' => -1,
            'person_primary_note_id' => -1,
            'person_status' => 'ACTIVE',
            'person_timestamp' => '0000-00-00 00:00:00'
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
        $message = '';
        $check = true;

        // not in use ...

        self::$message = $message;
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
            $this->PersonData->insert($data, self::$person_id);
            $person_id = self::$person_id;

            $this->app['monolog']->addInfo("Inserted person record for the contactID {$contact_id}", array(__METHOD__, __LINE__));
            return true;
        } catch (\Exception $e) {
            throw new ContactException($e);
        }
    }
}


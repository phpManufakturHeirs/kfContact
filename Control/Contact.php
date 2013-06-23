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

class Contact
{
    protected $app = null;
    protected static $message = '';

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

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->ContactPerson = new ContactPerson($this->app);
        $this->ContactData = new ContactData($this->app);
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
     * Get the contact record for this contact_id
     */
    public function getContactRecord()
    {
        if (self::$type === 'PERSON') {
            $this->ContactPerson->setContactID(self::$contact_id);
            return array(
                'contact_id' => self::$contact_id,
                'name' => self::$name,
                'login' => self::$login,
                'type' => self::$type,
                'status' => self::$status,
                'timestamp' => self::$timestamp,
                'person' => $this->ContactPerson->getContactRecord()
            );
        }

        throw ContactException::contactTypeNotSupported(self::$contact_type);
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
                return $this->getContactRecord();
            }
            else {

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
        if (self::$type === 'PERSON') {
            $result = $this->ContactPerson->validate($data);
            self::$message = $this->ContactPerson->getMessage();
            return $result;
        }
        throw ContactException::contactTypeNotSupported(self::$contact_type);
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
        if (!isset($data['email']) && !isset($data['login'])) {
            $this->setMessage('The contact record must contain a email address or a login name as unique identifier!');
            return false;
        }
        $login = (isset($data['login'])) ? $data['login'] : $data['email'];

        if (false !== ($check = $this->ContactData->selectName($login))) {
            $this->setMessage('The login <b>%login%</b> is already in use, please choose another one!', array('%login%' => $login));
            return false;
        }
        try {
            // BEGIN TRANSACTION
            $this->app['db']->beginTransaction();

            // first step: insert a contact record
            $insert = array(
                'name' => $login,
                'login' => $login,
                'type' => self::$type,
                'status' => (isset($data['status'])) ? $data['status'] : 'ACTIVE'
            );
            $this->ContactData->insert($insert, self::$contact_id);
            $contact_id = self::$contact_id;

            // second step: insert the contact record for the person
            if (!$this->ContactPerson->insert($data, self::$contact_id, self::$person_id)) {
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


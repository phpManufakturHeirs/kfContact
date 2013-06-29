<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Data\Contact;

use Silex\Application;

class Contact
{

    protected $app = null;
    protected static $table_name = null;
    protected $Person = null;
    protected $Company = null;
    protected $Note = null;
    protected $Communication = null;
    protected $Address = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_contact';
        $this->Person = new Person($this->app);
        $this->Company = new Company($this->app);
        $this->Note = new Note($this->app);
        $this->Communication = new Communication($this->app);
        $this->Address = new Address($this->app);
    }

    /**
     * Create the CONTACT table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `contact_id` INT(11) NOT NULL AUTO_INCREMENT,
        `contact_name` VARCHAR(128) NOT NULL DEFAULT '',
        `contact_login` VARCHAR(64) NOT NULL DEFAULT '',
        `contact_type` ENUM('PERSON', 'COMPANY') NOT NULL DEFAULT 'PERSON',
        `contact_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `contact_timestamp` TIMESTAMP,
        PRIMARY KEY (`contact_id`),
        UNIQUE (`contact_login`)
        )
    COMMENT='The main contact table'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_contact'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Select a contact record by the given contact_id
     * Return FALSE if the record does not exists
     *
     * @param integer $contact_id
     * @throws \Exception
     * @return multitype:array|boolean
     */
    public function select($contact_id)
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `contact_id`='$contact_id'";
            $result = $this->app['db']->fetchAssoc($SQL);
            if (is_array($result) && isset($result['contact_id'])) {
                $contact = array();
                foreach ($result as $key => $value) {
                    $contact[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                return $contact;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Select a contact record by the given login name
     * Return FALSE if the record does not exists
     *
     * @param integer $contact_id
     * @throws \Exception
     * @return multitype:array|boolean
     */
    public function selectLogin($login)
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `contact_login`='$login'";
            $result = $this->app['db']->fetchAssoc($SQL);
            if (is_array($result) && isset($result['contact_id'])) {
                $contact = array();
                foreach ($result as $key => $value) {
                    $contact[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                return $contact;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Insert a new record in the CONTACT table
     *
     * @param array $data
     * @param reference integer $contact_id
     * @throws \Exception
     */
    public function insert($data, &$contact_id=null)
    {
        try {
            $insert = array();
            foreach ($data as $key => $value) {
                if (($key == 'contact_id') || ($key == 'contact_timestamp')) continue;
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            $this->app['db']->insert(self::$table_name, $insert);
            $contact_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Update the contact record with the given contact_id
     *
     * @param array $data
     * @param integer $contact_id
     * @throws \Exception
     */
    public function update($data, $contact_id)
    {
        try {
            $update = array();
            foreach ($data as $key => $value) {
                if (($key == 'contact_id') || ($key == 'contact_timestamp')) continue;
                $update[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            if (!empty($update)) {
                $this->app['db']->update(self::$table_name, $update, array('contact_id' => $contact_id));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return a complete structured associative array for the contact with all
     * depending records and informations
     *
     * @param integer $contact_id
     * @param string $status can be ACTIVE, LOCKED or DELETED, default is DELETED
     * @param string $status_operator can be '=' or '!=', default is '!='
     * @throws \Doctrine\DBAL\DBALException
     * @throws \Exception
     * @return array|boolean FALSE if SELECT return no result
     */
    public function selectContact($contact_id, $status='DELETED', $status_operator='!=')
    {
        try {
            // first get the main contact record ...
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `contact_id`='{$contact_id}' AND `contact_status`{$status_operator}'{$status}'";
            $result = $this->app['db']->fetchAssoc($SQL);
            if (is_array($result) && isset($result['contact_id'])) {
                $contact = array();
                foreach ($result as $key => $value) {
                    $contact['contact'][$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                // select the PERSON data record
                if ((false === ($contact['person'] = $this->Person->selectByContactID($contact_id, $status, $status_operator))) ||
                    empty($contact['person'])) {
                    $contact['person'] = array($this->Person->getDefaultRecord());
                }

                // select the COMPANY data record
                if ((false === ($contact['company'] = $this->Company->selectByContactID($contact_id, $status, $status_operator))) ||
                    empty($contact['company'])) {
                    $contact['company'] = array($this->Company->getDefaultRecord());
                }
                // add the communication entries
                if ((false === ($contact['communication'] = $this->Communication->selectByContactID($contact_id, $status, $status_operator))) ||
                    empty($contact['communication'])) {
                    $contact['communication'] = array($this->Communication->getDefaultRecord());
                }
                // add the addresses
                if ((false === ($contact['address'] = $this->Address->selectByContactID($contact_id, $status, $status_operator))) ||
                    empty($contact['address'])) {
                    $contact['address'] = array($this->Address->getDefaultRecord());
                }
                // add the NOTES
                if ((false === ($contact['note'] = $this->Note->selectByContactID($contact_id, $status, $status_operator))) ||
                    empty($contact['note'])) {
                    $contact['note'] = array();
                }
                // return the formatted contact array
                return $contact;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return the contact TYPE for the given contact ID
     *
     * @param integer $contact_id
     * @throws \Exception
     */
    public function getContactType($contact_id)
    {
        try {
            $SQL = "SELECT `contact_type` FROM `".self::$table_name."` WHERE `contact_id`='$contact_id'";
            return $this->app['db']->fetchColumn($SQL);
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get the primary ID for the given contact ID and and the type (PHONE, EMAIL, ADDRESS...)
     *
     * @param integer $contact_id
     * @param string $contact_type
     * @throws \Exception
     */
    protected function getPrimaryIDbyType($contact_id, $contact_type)
    {
        try {
            // first we need the contact type
            $type = $this->getContactType($contact_id);
            if ($type == 'PERSON') {
                return $this->Person->getPersonPrimaryContactTypeID($contact_id, $contact_type);
            }
            else {
                return $this->Company->getCompanyPrimaryContactTypeID($contact_id, $contact_type);
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Set the primary ID for the given contact ID and and the type (PHONE, EMAIL, ADDRESS...)
     *
     * @param integer $contact_id
     * @param string $contact_type
     * @param integer $primary_id
     * @throws \Exception
     */
    protected function setPrimaryIDbyType($contact_id, $contact_type, $primary_id)
    {
        try {
            // first we need the contact type
            $type = $this->getContactType($contact_id);
            if ($type == 'PERSON') {
                $this->Person->setPersonPrimaryContactTypeID($contact_id, $contact_type, $primary_id);
            }
            else {
                $this->Company->setCompanyPrimaryContactTypeID($contact_id, $contact_type, $primary_id);
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return the primary address ID for the CONTACT ID
     *
     * @param integer $contact_id
     * @throws \Exception
     */
    public function getPrimaryAddressID($contact_id)
    {
        return $this->getPrimaryIDbyType($contact_id, 'ADDRESS');
    }

    /**
     * Set the primary address ID for the CONTACT ID
     *
     * @param integer $contact_id
     * @param integer $address_id
     * @throws \Exception
     */
    public function setPrimaryAddressID($contact_id, $address_id)
    {
        return $this->setPrimaryIDbyType($contact_id, 'ADDRESS', $address_id);
    }

    /**
     * Clear the primary address ID for the CONTACT ID and set it to -1
     *
     * @param integer $contact_id
     */
    public function clearPrimaryAddressID($contact_id)
    {
        return $this->setPrimaryIDbyType($contact_id, 'ADDRESS', -1);
    }

    /**
     * Return the primary NOTE ID for the CONTACT ID
     *
     * @param integer $contact_id
     * @throws \Exception
     */
    public function getPrimaryNoteID($contact_id)
    {
        return $this->getPrimaryIDbyType($contact_id, 'NOTE');
    }

    /**
     * Set the primary NOTE ID for the CONTACT ID
     *
     * @param integer $contact_id
     * @param integer $note_id
     * @throws \Exception
     */
    public function setPrimaryNoteID($contact_id, $note_id)
    {
        return $this->setPrimaryIDbyType($contact_id, 'NOTE', $note_id);
    }

    /**
     * Clear the primary NOTE ID for the CONTACT ID and set it to -1
     *
     * @param integer $contact_id
     */
    public function clearPrimaryNoteID($contact_id)
    {
        return $this->setPrimaryIDbyType($contact_id, 'NOTE', -1);
    }

    /**
     * Return the primary EMAIL ID for the CONTACT ID
     *
     * @param integer $contact_id
     * @throws \Exception
     */
    public function getPrimaryEmailID($contact_id)
    {
        return $this->getPrimaryIDbyType($contact_id, 'EMAIL');
    }

    /**
     * Set the primary EMAIL ID for the CONTACT ID
     *
     * @param integer $contact_id
     * @param integer $email_id
     * @throws \Exception
     */
    public function setPrimaryEmailID($contact_id, $email_id)
    {
        return $this->setPrimaryIDbyType($contact_id, 'EMAIL', $email_id);
    }

    /**
     * Clear the primary EMAIL ID for the CONTACT ID and set it to -1
     *
     * @param integer $contact_id
     */
    public function clearPrimaryEmailID($contact_id)
    {
        return $this->setPrimaryIDbyType($contact_id, 'EMAIL', -1);
    }

    /**
     * Return the primary PHONE ID for the CONTACT ID
     *
     * @param integer $contact_id
     * @throws \Exception
     */
    public function getPrimaryPhoneID($contact_id)
    {
        return $this->getPrimaryIDbyType($contact_id, 'PHONE');
    }

    /**
     * Set the primary PHONE ID for the CONTACT ID
     *
     * @param integer $contact_id
     * @param integer $phone_id
     * @throws \Exception
     */
    public function setPrimaryPhoneID($contact_id, $phone_id)
    {
        return $this->setPrimaryIDbyType($contact_id, 'PHONE', $phone_id);
    }

    /**
     * Clear the primary PHONE ID for the CONTACT ID and set it to -1
     *
     * @param integer $contact_id
     */
    public function clearPrimaryPhoneID($contact_id)
    {
        return $this->setPrimaryIDbyType($contact_id, 'PHONE', -1);
    }


}
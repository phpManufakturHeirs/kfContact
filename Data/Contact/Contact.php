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
            $this->app['monolog']->addDebug("Created table 'contact_contact'", array('method' => __METHOD__, 'line' => __LINE__));
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
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            $this->app['db']->insert(self::$table_name, $insert);
            $contact_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return a complete structured associative array for the contact with all
     * depending records and informations
     *
     * @param integer $contact_id
     * @param string $status
     * @param string $status_operator
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
                if (false === ($contact['person'] = $this->Person->selectByContactID($contact_id, $status, $status_operator))) {
                    $contact['person'] = array();
                }

                // select the COMPANY data record
                if (false === ($contact['company'] = $this->Company->selectByContactID($contact_id, $status, $status_operator))) {
                    $contact['company'] = array();
                }

                // add the communication entries
                $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_communication` WHERE `contact_id`='{$contact_id}' AND `communication_status`{$status_operator}'{$status}'";
                $results = $this->app['db']->fetchAll($SQL);
                if (is_array($results)) {
                    $level = 0;
                    foreach ($results as $result) {
                        foreach ($result as $key => $value) {
                            $contact['communication'][$level][$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                        }
                        $level++;
                    }
                }

                // add the addresses
                $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_address` WHERE `contact_id`='{$contact_id}' AND `address_status`{$status_operator}'{$status}'";
                $results = $this->app['db']->fetchAll($SQL);
                if (is_array($results)) {
                    $level = 0;
                    foreach ($results as $result) {
                        foreach ($result as $key => $value) {
                            $contact['address'][$level][$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                        }
                        $level++;
                    }
                }

                // add the NOTES
                if (false === ($contact['note'] = $this->Note->selectByContactID($contact_id, $status, $status_operator))) {
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

}
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

class Person
{

    protected $app = null;
    protected static $table_name = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_person';
    }

    /**
     * Create the PERSON table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `person_id` INT(11) NOT NULL AUTO_INCREMENT,
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `person_gender` ENUM('MALE','FEMALE') NOT NULL DEFAULT 'MALE',
        `person_title` VARCHAR(32) NOT NULL DEFAULT '',
        `person_first_name` VARCHAR(128) NOT NULL DEFAULT '',
        `person_last_name` VARCHAR(128) NOT NULL DEFAULT '',
        `person_nick_name` VARCHAR(128) NOT NULL DEFAULT '',
        `person_birthday` DATE NOT NULL DEFAULT '0000-00-00',
        `person_contact_since` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `person_primary_address_id` INT(11) NOT NULL DEFAULT '-1',
        `person_primary_company_id` INT(11) NOT NULL DEFAULT '-1',
        `person_primary_phone_id` INT(11) NOT NULL DEFAULT '-1',
        `person_primary_email_id` INT(11) NOT NULL DEFAULT '-1',
        `person_primary_note_id` INT(11) NOT NULL DEFAULT '-1',
        `person_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `person_timestamp` TIMESTAMP,
        PRIMARY KEY (`person_id`),
        UNIQUE (`contact_id`)
        )
    COMMENT='The main contact table'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_person'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return a default (empty) PERSON contact record.
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
     * Insert a new PERSON record
     *
     * @param array $data
     * @param reference integer $person_id
     * @throws \Exception
     */
    public function insert($data, &$person_id=null)
    {
        try {
            $insert = array();
            foreach ($data as $key => $value) {
                if (($key == 'person_id') || ($key == 'person_timestamp')) continue;
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            $this->app['db']->insert(self::$table_name, $insert);
            $person_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Update the person record for the given ID
     *
     * @param array $data
     * @param integer $person_id
     * @throws \Exception
     */
    public function update($data, $person_id)
    {
        try {
            $update = array();
            foreach ($data as $key => $value) {
                if (($key == 'person_id') || ($key == 'person_timestamp')) continue;
                $update[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            if (!empty($update)) {
                $this->app['db']->update(self::$table_name, $update, array('person_id' => $person_id));
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return all PERSON records for the given Contact ID
     *
     * @param integer $contact_id
     * @param string $status
     * @param string $status_operator
     * @throws \Exception
     * @return array|boolean
     */
    public function selectByContactID($contact_id, $status='DELETED', $status_operator='!=')
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `contact_id`='$contact_id' AND `person_status`{$status_operator}'{$status}'";
            $results = $this->app['db']->fetchAll($SQL);
            if (is_array($results)) {
                $person = array();
                $level = 0;
                foreach ($results as $result) {
                    foreach ($result as $key => $value) {
                        $person[$level][$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                    }
                    $level++;
                }
                return $person;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get the field name by the desired contact type (ADDRESS, NOTE ...)
     *
     * @param string $contact_type
     * @throws \Exception
     * @return string
     */
    protected static function getFieldByContactType($contact_type)
    {
        switch (strtoupper($contact_type)) {
            case 'ADDRESS':
                return 'person_primary_address_id';
            case 'NOTE':
                return 'person_primary_note_id';
            case 'PHONE':
                return 'person_primary_phone_id';
            case 'EMAIL':
                return 'person_primary_email_id';
            case 'COMPANY':
                return 'person_primary_company_id';
            default:
                throw new \Exception("Unknown contact type: $contact_type");
        }
    }

    /**
     * Return the primary ID for the desired contact type of PERSON contact ID
     *
     * @param integer $contact_id
     * @throws \Exception
     */
    public function getPersonPrimaryContactTypeID($contact_id, $contact_type)
    {
        try {
            $primary_type = self::getFieldByContactType($contact_type);
            $SQL = "SELECT `$primary_type` FROM `".self::$table_name."` WHERE `contact_id`='$contact_id'";
            return $this->app['db']->fetchColumn($SQL);
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Set the primary ID for the desired contact type of PERSON contact ID
     *
     * @param integer $contact_id
     * @param integer $address_id
     * @throws \Exception
     */
    public function setPersonPrimaryContactTypeID($contact_id, $contact_type, $primary_id)
    {
        try {
            $primary_type = self::getFieldByContactType($contact_type);
            $this->app['db']->update(self::$table_name, array($primary_type => $primary_id), array('contact_id' => $contact_id));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Replace the given title identifier with a new optional identifier or an
     * empty string. Changes all person records!
     *
     * @param string $title_identifier
     * @param string $new_identifier
     * @throws \Exception
     */
    public function replaceTitle($title_identifier, $new_identifier='')
    {
        try {
            $this->app['db']->update(self::$table_name, array('person_title' => $new_identifier), array('person_title' => $title_identifier));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
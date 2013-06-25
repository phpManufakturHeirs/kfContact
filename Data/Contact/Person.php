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
        INDEX (`contact_id`)
        )
    COMMENT='The main contact table'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addDebug("Created table 'contact_person'", array('method' => __METHOD__, 'line' => __LINE__));
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
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            $this->app['db']->insert(self::$table_name, $data);
            $person_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return all records for the given Contact ID
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

}
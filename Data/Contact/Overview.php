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

class Overview
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_overview';
    }

    /**
     * Create the OVERVIEW table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `contact_name` VARCHAR(128) NOT NULL DEFAULT '',
        `contact_type` ENUM('PERSON','COMPANY') NOT NULL DEFAULT 'PERSON',
        `contact_status` ENUM('ACTIVE','LOCKED','DELETED') NOT NULL DEFAULT 'ACTIVE',
        `person_id` INT(11) NOT NULL DEFAULT '-1',
        `person_gender` ENUM('MALE','FEMALE') NOT NULL DEFAULT 'MALE',
        `person_title` VARCHAR(32) NOT NULL DEFAULT '',
        `person_first_name` VARCHAR(128) NOT NULL DEFAULT '',
        `person_last_name` VARCHAR(128) NOT NULL DEFAULT '',
        `person_nick_name` VARCHAR(128) NOT NULL DEFAULT '',
        `person_birthday` DATE NOT NULL DEFAULT '0000-00-00',
        `company_id` INT(11) NOT NULL DEFAULT '-1',
        `company_name` VARCHAR(128) NOT NULL DEFAULT '',
        `company_department` VARCHAR(128) NOT NULL DEFAULT '',
        `communication_phone` VARCHAR(255) NOT NULL DEFAULT '',
        `communication_email` VARCHAR(255) NOT NULL DEFAULT '',
        `address_id` INT(11) NOT NULL DEFAULT '-1',
        `address_street` VARCHAR(128) NOT NULL DEFAULT '',
        `address_zip` VARCHAR(32) NOT NULL DEFAULT '',
        `address_city` VARCHAR(128) NOT NULL DEFAULT '',
        `address_country_code` VARCHAR(8) NOT NULL DEFAULT '',
        `timestamp` TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE (`contact_id`)
        )
    COMMENT='Summary/Overview over all contacts'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_overview'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    public function refresh($contact_id)
    {
        try {
            // get the contact block
            $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_contact` WHERE `contact_id`='$contact_id'";
            $contact = $this->app['db']->fetchAssoc($SQL);

            if ($contact['contact_type'] == 'COMPANY') {
                // get the company
                $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_company` WHERE `contact_id`='$contact_id'";
                $company = $this->app['db']->fetchAssoc($SQL);

                if ($company['company_primary_address_id'] > 0) {
                    $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_address` WHERE `address_id`='{$company['company_primary_address_id']}'";
                    $address = $this->app['db']->fetchAssoc($SQL);
                }

                if ($company['company_primary_email_id'] > 0) {
                    $SQL = "SELECT `communication_value` FROM `".FRAMEWORK_TABLE_PREFIX."contact_communication` WHERE `communication_id`='{$company['company_primary_email_id']}'";
                    $email = $this->app['db']->fetchColumn($SQL);
                }

                if ($company['company_primary_phone_id'] > 0) {
                    $SQL = "SELECT `communication_value` FROM `".FRAMEWORK_TABLE_PREFIX."contact_communication` WHERE `communication_id`='{$company['company_primary_email_id']}'";
                    $phone = $this->app['db']->fetchColumn($SQL);
                }
            }
            else {
                // get the person
                $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_person` WHERE `contact_id`='$contact_id'";
                $person = $this->app['db']->fetchAssoc($SQL);

                if ($person['person_primary_address_id'] > 0) {
                    $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_address` WHERE `address_id`='{$person['person_primary_address_id']}'";
                    $address = $this->app['db']->fetchAssoc($SQL);
                }

                if ($person['person_primary_email_id'] > 0) {
                    $SQL = "SELECT `communication_value` FROM `".FRAMEWORK_TABLE_PREFIX."contact_communication` WHERE `communication_id`='{$person['person_primary_email_id']}'";
                    $email = $this->app['db']->fetchColumn($SQL);
                }

                if ($person['person_primary_phone_id'] > 0) {
                    $SQL = "SELECT `communication_value` FROM `".FRAMEWORK_TABLE_PREFIX."contact_communication` WHERE `communication_id`='{$person['person_primary_email_id']}'";
                    $phone = $this->app['db']->fetchColumn($SQL);
                }
            }

            $record = array(
                'contact_id' => $contact_id,
                'contact_name' => $contact['contact_name'],
                'contact_type' => $contact['contact_type'],
                'contact_status' => $contact['contact_status'],
                'person_id' => isset($person['person_id']) ? $person['person_id'] : -1,
                'person_first_name' => isset($person['person_first_name']) ? $person['person_first_name'] : '',
                'person_last_name' => isset($person['person_last_name']) ? $person['person_last_name'] : '',
                'person_birthday' => isset($person['person_birthday']) ? $person['person_birthday'] : '0000-00-00',
                'person_title' => isset($person['person_title']) ? $person['person_title'] : '',
                'person_gender' => isset($person['person_gender']) ? $person['person_gender'] : 'MALE',
                'company_id' => isset($company['company_id']) ? $company['company_id'] : -1,
                'company_name' => isset($company['company_name']) ? $company['company_name'] : '',
                'company_department' => isset($company['company_department']) ? $company['company_department'] : '',
                'communication_phone' => isset($phone) ? $phone : '',
                'communication_email' => isset($email) ? $email : '',
                'address_id' => isset($address['address_id']) ? $address['address_id'] : -1,
                'address_street' => isset($address['address_street']) ? $address['address_street'] : '',
                'address_city' => isset($address['address_city']) ? $address['address_city'] : '',
                'address_zip' => isset($address['address_zip']) ? $address['address_zip'] : '',
                'address_country_code' => isset($address['address_country_code']) ? $address['address_country_code'] : ''
            );

            // prepare the data record
            $data = array();
            foreach ($record as $key => $value) {
                $data[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
            }

            $SQL = "SELECT `contact_id` FROM `".self::$table_name."` WHERE `contact_id`='$contact_id'";
            if (($check = $this->app['db']->fetchColumn($SQL)) == $contact_id) {
                // update the overview
                $this->app['db']->update(self::$table_name, $data, array('contact_id' => $contact_id));
            }
            else {
                // insert a new record
                $this->app['db']->insert(self::$table_name, $data);
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }

    }
}
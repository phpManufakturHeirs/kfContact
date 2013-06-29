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

class Company
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_company';
    }

    /**
     * Create the COMPANY table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `company_id` INT(11) NOT NULL AUTO_INCREMENT,
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `company_name` VARCHAR(128) NOT NULL DEFAULT '',
        `company_department` VARCHAR(128) NOT NULL DEFAULT '',
        `company_additional` VARCHAR(128) NOT NULL DEFAULT '',
        `company_additional_2` VARCHAR(128) NOT NULL DEFAULT '',
        `company_additional_3` VARCHAR(128) NOT NULL DEFAULT '',
        `company_primary_address_id` INT(11) NOT NULL DEFAULT '-1',
        `company_primary_person_id` INT(11) NOT NULL DEFAULT '-1',
        `company_primary_phone_id` INT(11) NOT NULL DEFAULT '-1',
        `company_primary_email_id` INT(11) NOT NULL DEFAULT '-1',
        `company_primary_note_id` INT(11) NOT NULL DEFAULT '-1',
        `company_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `company_timestamp` TIMESTAMP,
        PRIMARY KEY (`company_id`),
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
            $this->app['monolog']->addInfo("Created table 'contact_company'", array(__METHOD__, __LINE__));
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
            'company_id' => -1,
            'contact_id' => -1,
            'company_name' => '',
            'company_department' => '',
            'company_additional' => '',
            'company_additional_2' => '',
            'company_additional_3' => '',
            'company_primary_address_id' => -1,
            'company_primary_person_id' => -1,
            'company_primary_phone_id' => -1,
            'company_primary_email_id' => -1,
            'company_primary_note_id' => -1,
            'company_status' => 'ACTIVE',
            'company_timestamp' => '0000-00-00 00:00:00',
        );
    }

    /**
     * Return all company records for the given Contact ID
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
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `contact_id`='$contact_id' AND `company_status`{$status_operator}'{$status}'";
            $results = $this->app['db']->fetchAll($SQL);
            if (is_array($results)) {
                $company = array();
                $level = 0;
                foreach ($results as $result) {
                    foreach ($result as $key => $value) {
                        $company[$level][$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                    }
                    $level++;
                }
                return $company;
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
                return 'company_primary_address_id';
            case 'NOTE':
                return 'company_primary_note_id';
            case 'PHONE':
                return 'company_primary_phone_id';
            case 'EMAIL':
                return 'company_primary_email_id';
            case 'PERSON':
                return 'company_primary_person_id';
            default:
                throw new \Exception("Unknown contact type: $contact_type");
        }
    }

    /**
     * Return the primary ID for the desired contact type of COMPANY contact ID
     *
     * @param integer $contact_id
     * @throws \Exception
     */
    public function getCompanyPrimaryContactTypeID($contact_id, $contact_type)
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
     * Set the primary ID for the desired contact type of COMPANY contact ID
     *
     * @param integer $contact_id
     * @param integer $primary_id
     * @throws \Exception
     */
    public function setCompanyPrimaryContactTypeID($contact_id, $contact_type, $primary_id)
    {
        try {
            $primary_type = self::getFieldByContactType($contact_type);
            $this->app['db']->update(self::$table_name, array($primary_type => $primary_id), array('contact_id' => $contact_id));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }



}
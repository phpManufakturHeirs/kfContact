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

class Address
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_address';
    }

    /**
     * Create the ADDRESS table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `address_id` INT(11) NOT NULL AUTO_INCREMENT,
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `address_type` VARCHAR(32) NOT NULL DEFAULT 'OTHER',
        `address_identifier` VARCHAR(64) NOT NULL DEFAULT '',
        `address_description` TEXT NOT NULL,
        `address_street` VARCHAR(128) NOT NULL DEFAULT '',
        `address_appendix_1` VARCHAR(128) NOT NULL DEFAULT '',
        `address_appendix_2` VARCHAR(128) NOT NULL DEFAULT '',
        `address_zip` VARCHAR(32) NOT NULL DEFAULT '',
        `address_city` VARCHAR(128) NOT NULL DEFAULT '',
        `address_area` VARCHAR(128) NOT NULL DEFAULT '',
        `address_state` VARCHAR(128) NOT NULL DEFAULT '',
        `address_country_code` VARCHAR(8) NOT NULL DEFAULT '',
        `address_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `address_timestamp` TIMESTAMP,
        PRIMARY KEY (`address_id`),
        INDEX (`contact_id`)
        )
    COMMENT='The contact address table'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_address'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get a default (empty) ADDRESS record
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        return array(
            'address_id' => -1,
            'contact_id' => -1,
            'address_type' => 'OTHER',
            'address_identifier' => '',
            'address_description' => '',
            'address_street' => '',
            'address_appendix_1' => '',
            'address_appendix_2' => '',
            'address_zip' => '',
            'address_city' => '',
            'address_area' => '',
            'address_state' => '',
            'address_country_code' => '',
            'address_status' => 'ACTIVE',
            'address_timestamp' => '0000-00-00 00:00:00'
        );
    }

    /**
     * Insert a new ADDRESS record
     *
     * @param array $data
     * @param reference integer $address_id
     * @throws \Exception
     */
    public function insert($data, &$address_id=null)
    {
        try {
            $insert = array();
            foreach ($data as $key => $value) {
                if (($key == 'address_id') || ($key == 'address_timestamp')) continue;
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
            }
            $this->app['db']->insert(self::$table_name, $insert);
            $address_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return all ADDRESS records for the given Contact ID
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
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `contact_id`='$contact_id' AND `address_status`{$status_operator}'{$status}'";
            $results = $this->app['db']->fetchAll($SQL);
            if (is_array($results)) {
                $address = array();
                $level = 0;
                foreach ($results as $result) {
                    foreach ($result as $key => $value) {
                        $address[$level][$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                    }
                    $level++;
                }
                return $address;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Check if the given $address_id is used as primary address for the contact
     *
     * @param integer $address_id
     * @param integer $contact_id
     * @throws \Exception
     * @return boolean
     */
    public function isUsedAsPrimaryAddress($address_id, $contact_id, $contact_type='PERSON')
    {
        try {
            if ($contact_type == 'COMPANY') {
                $SQL = "SELECT `company_primary_address_id` FROM `".FRAMEWORK_TABLE_PREFIX."contact_company` WHERE ".
                    "`contact_id`='$contact_id' AND `company_primary_address_id`='$address_id' AND `company_status`!='DELETED'";
                if ($address_id == ($check = $this->app['db']->fetchColumn($SQL))) {
                    return true;
                }
            }
            else {
                $SQL = "SELECT `person_primary_address_id` FROM `".FRAMEWORK_TABLE_PREFIX."contact_person` WHERE ".
                    "`contact_id`='$contact_id' AND `person_primary_address_id`='$address_id' AND `person_status`!='DELETED'";
                if ($address_id == ($check = $this->app['db']->fetchColumn($SQL))) {
                    return true;
                }
            }
            return false;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Mark the given $address_id as deleted but does not delete the record physically
     *
     * @param integer $address_id
     * @throws \Exception
     */
    public function delete($address_id)
    {
        try {
            $this->app['db']->update(self::$table_name, array('address_status' => 'DELETED'), array('address_id' => $address_id));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
}
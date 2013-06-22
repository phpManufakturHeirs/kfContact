<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Data;

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
     * Create the base list
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
        `name` VARCHAR(128) NOT NULL DEFAULT '',
        `department` VARCHAR(128) NOT NULL DEFAULT '',
        `additional` VARCHAR(128) NOT NULL DEFAULT '',
        `additional_2` VARCHAR(128) NOT NULL DEFAULT '',
        `additional_3` VARCHAR(128) NOT NULL DEFAULT '',
        `primary_address_id` INT(11) NOT NULL DEFAULT '-1',
        `primary_person_id` INT(11) NOT NULL DEFAULT '-1',
        `primary_phone_id` INT(11) NOT NULL DEFAULT '-1',
        `primary_email_id` INT(11) NOT NULL DEFAULT '-1',
        `primary_note_id` INT(11) NOT NULL DEFAULT '-1',
        `status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `timestamp` TIMESTAMP,
        PRIMARY KEY (`company_id`),
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
            $this->app['monolog']->addDebug("Created table 'contact_company'", array('method' => __METHOD__, 'line' => __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
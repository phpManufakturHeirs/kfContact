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
     * Create the base list
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
        `gender` ENUM('MALE','FEMALE') NOT NULL DEFAULT 'MALE',
        `title_id` INT(11) NOT NULL DEFAULT '-1',
        `first_name` VARCHAR(128) NOT NULL DEFAULT '',
        `middle_name` VARCHAR(128) NOT NULL DEFAULT '',
        `last_name` VARCHAR(128) NOT NULL DEFAULT '',
        `nick_name` VARCHAR(128) NOT NULL DEFAULT '',
        `birthday` DATE NOT NULL DEFAULT '0000-00-00',
        `contact_since` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `primary_address_id` INT(11) NOT NULL DEFAULT '-1',
        `primary_company_id` INT(11) NOT NULL DEFAULT '-1',
        `primary_phone_id` INT(11) NOT NULL DEFAULT '-1',
        `primary_email_id` INT(11) NOT NULL DEFAULT '-1',
        `primary_note_id` INT(11) NOT NULL DEFAULT '-1',
        `status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `timestamp` TIMESTAMP,
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
            $this->app['monolog']->addDebug("Created table 'contact_contact'", array('method' => __METHOD__, 'line' => __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
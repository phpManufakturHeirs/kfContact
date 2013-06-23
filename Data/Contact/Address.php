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
        `address_type` VARCHAR(32) NOT NULL DEFAULT 'OTHER',
        `address_identifier` VARCHAR(64) NOT NULL DEFAULT '',
        `address_description` TEXT NOT NULL,
        `address_line_1` VARCHAR(128) NOT NULL DEFAULT '',
        `address_line_2` VARCHAR(128) NOT NULL DEFAULT '',
        `address_line_3` VARCHAR(128) NOT NULL DEFAULT '',
        `zip_code` VARCHAR(32) NOT NULL DEFAULT '',
        `city` VARCHAR(128) NOT NULL DEFAULT '',
        `area` VARCHAR(128) NOT NULL DEFAULT '',
        `state` VARCHAR(128) NOT NULL DEFAULT '',
        `country_code` VARCHAR(8) NOT NULL DEFAULT '',
        `status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `timestamp` TIMESTAMP,
        PRIMARY KEY (`address_id`)
        )
    COMMENT='The contact address table'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addDebug("Created table 'contact_address'", array('method' => __METHOD__, 'line' => __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
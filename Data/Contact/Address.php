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
            $this->app['monolog']->addDebug("Created table 'contact_address'", array('method' => __METHOD__, 'line' => __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
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
                $insert[$this->app['db']->quoteIdentifier($key)] = $this->app['utils']->sanitizeVariable($value);
            }
            $this->app['db']->insert(self::$table_name, $data);
            $address_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
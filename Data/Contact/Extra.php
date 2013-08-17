<?php

/**
 * Event
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Data\Contact;

use Silex\Application;

class Extra
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_extra';
    }

    /**
     * Create the EVENT table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $table_extra_type = FRAMEWORK_TABLE_PREFIX.'event_extra_type';
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `extra_id` INT(11) NOT NULL AUTO_INCREMENT,
        `extra_type_id` INT(11) DEFAULT NULL,
        `extra_type_name` VARCHAR(64) NOT NULL DEFAULT '',
        `category_id` INT(11) NOT NULL DEFAULT '-1',
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `extra_type_type` ENUM('TEXT','HTML','VARCHAR','INT','FLOAT','DATE','DATETIME') NOT NULL DEFAULT 'VARCHAR',
        `extra_text` TEXT NOT NULL,
        `extra_html` TEXT NOT NULL,
        `extra_varchar` VARCHAR(255) NOT NULL DEFAULT '',
        `extra_int` INT(11) NOT NULL DEFAULT '0',
        `extra_float` FLOAT NOT NULL DEFAULT '0',
        `extra_date` DATE NOT NULL DEFAULT '0000-00-00',
        `extra_datetime` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
        `extra_time` TIME NOT NULL DEFAULT '00:00:00',
        `extra_timestamp` TIMESTAMP,
        PRIMARY KEY (`extra_id`),
        INDEX (`group_id`)
        )
    COMMENT='The table for extra fields'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'event_extra'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }



}

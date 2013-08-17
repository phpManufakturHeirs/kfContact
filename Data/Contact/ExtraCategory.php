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

class ExtraCategory
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_extra_category';
    }

    /**
     * Create the table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $table_extra_type = FRAMEWORK_TABLE_PREFIX.'contact_extra_type';
        $table_category = FRAMEWORK_TABLE_PREFIX.'contact_category';
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `extra_category_id` INT(11) NOT NULL AUTO_INCREMENT,
        `extra_type_id` INT(11) DEFAULT NULL,
        `category_id` INT(11) DEFAULT NULL,
        `extra_category_timestamp` TIMESTAMP,
        PRIMARY KEY (`extra_category_id`),
        CONSTRAINT
            FOREIGN KEY (`extra_type_id`)
            REFERENCES $table_extra_type(`extra_type_id`)
            ON DELETE CASCADE,
        CONSTRAINT
            FOREIGN KEY (`category_id`)
            REFERENCES $table_category (`category_id`)
            ON DELETE CASCADE
        )
    COMMENT='The table to assign extra fields to a contact category'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_extra_category'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }


}

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

class Category
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_category';
    }

    /**
     * Create the CATEGORY TYPE table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `category_id` INT(11) NOT NULL AUTO_INCREMENT,
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `category_name` VARCHAR(64) NOT NULL DEFAULT '',
        `category_timestamp` TIMESTAMP,
        PRIMARY KEY (`category_id`),
        INDEX (`contact_id`,`category_name`)
        )
    COMMENT='The category table for the contacts'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_category_type'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    public function getDefaultRecord()
    {
        return array(
            'category_id' => -1,
            'contact_id' => -1,
            'category_name' => '',
            'category_timestamp' => '0000-00-00 00:00:00'
        );
    }

    /**
     * Return all CATEGORIES for the given Contact ID
     *
     * @param integer $contact_id
     * @throws \Exception
     * @return array|boolean
     */
    public function selectByContactID($contact_id)
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `contact_id`='$contact_id'";
            $results = $this->app['db']->fetchAll($SQL);
            if (is_array($results)) {
                $category = array();
                $level = 0;
                foreach ($results as $result) {
                    foreach ($result as $key => $value) {
                        $category[$level][$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                    }
                    $level++;
                }
                return $category;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Insert a new CATEGORY record
     *
     * @param array $data
     * @param reference integer $category_id
     * @throws \Exception
     */
    public function insert($data, &$category_id=null)
    {
        try {
            $insert = array();
            foreach ($data as $key => $value) {
                if (($key == 'category_id') || ($key == 'category_timestamp')) continue;
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
            }
            $this->app['db']->insert(self::$table_name, $insert);
            $category_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    public function delete($category_name)
    {
        try {
            $this->app['db']->delete(self::$table_name, array('category_name' => $category_name));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
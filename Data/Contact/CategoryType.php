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

class CategoryType
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_category_type';
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
        `category_type_id` INT(11) NOT NULL AUTO_INCREMENT,
        `category_type_name` VARCHAR(64) NOT NULL DEFAULT '',
        `category_type_description` VARCHAR(255) NOT NULL DEFAULT '',
        PRIMARY KEY (`category_type_id`),
        UNIQUE (`category_type_name`)
        )
    COMMENT='The category type definition table'
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

    /**
     * Initialize the category type list with the defaults from /communication.types.json
     *
     * @throws \Exception
     */
    public function initCategoryTypeList()
    {
        try {
            // get the number of titles in the list
            $count = $this->app['db']->fetchColumn("SELECT COUNT(`category_type_id`) FROM `".self::$table_name."`");
            if ($count < 1) {
                // no entries!
                $json_import = MANUFAKTUR_PATH.'/Contact/Data/Setup/Import/category.json';
                if (!file_exists($json_import)) {
                    throw new \Exception("Can't read the category type definition list: $json_import", array(__METHOD__, __LINE__));
                }
                $types = $this->app['utils']->readJSON($json_import);
                foreach ($types as $type) {
                    $this->app['db']->insert(self::$table_name, array(
                        'category_type_name' => $type['type'],
                        'category_type_description' => $this->app['utils']->sanitizeText($type['description'])
                    ));
                }
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return a array with all categories prepared for usage with TWIG
     *
     * @throws \Exception
     * @return array
     */
    public function getArrayForTwig()
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` ORDER BY `category_type_name` ASC";
            $categories = $this->app['db']->fetchAll($SQL);
            $result = array();
            foreach ($categories as $category) {
                $result[$category['category_type_name']] = ucfirst(strtolower($category['category_type_name']));
            }
            return $result;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Check if the desired CATEGORY exists
     *
     * @param string $category_name
     * @throws \Exception
     * @return boolean
     */
    public function existsCategory($category_name)
    {
        try {
            $SQL = "SELECT `category_type_name` FROM `".self::$table_name."` WHERE `category_type_name`='$category_name'";
            $result = $this->app['db']->fetchColumn($SQL);
            return ($result == $category_name) ? true : false;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
}
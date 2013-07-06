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

class Tag
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_tag';
    }

    /**
     * Create the Tag table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
    		`tag_id` INT(11) NOT NULL AUTO_INCREMENT,
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `tag_name` VARCHAR(32) NOT NULL DEFAULT '',
    		`tag_timestamp` TIMESTAMP,
        PRIMARY KEY (`tag_id`),
        INDEX (`contact_id`,`tag_name`)
        )
    COMMENT='The tags for the contact table'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_tag'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Select a TAG record by the given tag_id
     * Return FALSE if the record does not exists
     *
     * @param integer $tag_id
     * @throws \Exception
     * @return multitype:array|boolean
     */
    public function select($tag_id)
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `tag_id`='$tag_id'";
            $result = $this->app['db']->fetchAssoc($SQL);
            if (is_array($result) && isset($result['tag_id'])) {
                $contact = array();
                foreach ($result as $key => $value) {
                    $contact[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                return $contact;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    public function delete($tag_name)
    {
        try {
            $this->app['db']->delete(self::$table_name, array('tag_name' => $tag_name));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
}
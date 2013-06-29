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

class Title
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_title';
    }

    /**
     * Create the TITLE table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `title_id` INT(11) NOT NULL AUTO_INCREMENT,
        `title_identifier` VARCHAR(32) NOT NULL DEFAULT '',
        `title_short` VARCHAR(32) NOT NULL DEFAULT '',
        `title_long` VARCHAR(64) NOT NULL DEFAULT '',
        PRIMARY KEY (`title_id`),
        UNIQUE (`title_identifier`)
        )
    COMMENT='The person title definition table'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_title'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Initialize the title list with the defaults from /titles.json
     *
     * @throws \Exception
     */
    public function initTitleList()
    {
        try {
            // get the number of titles in the list
            $count = $this->app['db']->fetchColumn("SELECT COUNT(`title_id`) FROM `".self::$table_name."`");
            if ($count < 1) {
                // no entries!
                $json_import = MANUFAKTUR_PATH.'/Contact/Data/Setup/Import/titles.json';
                if (!file_exists($json_import)) {
                    throw new \Exception("Can't read the title definition list: $json_import");
                }
                $titles = $this->app['utils']->readJSON($json_import);
                foreach ($titles as $title) {
                    $this->app['db']->insert(self::$table_name, array(
                        'title_identifier' => $title['identifier'],
                        'title_short' => $this->app['utils']->sanitizeText($title['short']),
                        'title_long' => $this->app['utils']->sanitizeText($title['long'])
                    ));
                }
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return a array with all titles, prepared for usage with TWIG
     *
     * @throws \Exception
     * @return array
     */
    public function getArrayForTwig()
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` ORDER BY `title_short` ASC";
            $titles = $this->app['db']->fetchAll($SQL);
            $result = array();
            foreach ($titles as $title) {
                $result[$title['title_identifier']] = $this->app['utils']->unsanitizeText($title['title_short']);
            }
            return $result;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
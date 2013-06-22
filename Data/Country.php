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

class Country
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_country';
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
        `country_id` INT(11) NOT NULL AUTO_INCREMENT,
        `country_code` VARCHAR(3) NOT NULL DEFAULT '',
        `country_name` VARCHAR(128) NOT NULL DEFAULT '',
        PRIMARY KEY (`country_id`),
        UNIQUE (`country_code`)
        )
    COMMENT='The country list for the contact application'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addDebug("Created table 'contact_country'", array('method' => __METHOD__, 'line' => __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    public function initCountryList()
    {
        try {
            // get the number of countries in the list
            $count = $this->app['db']->fetchColumn("SELECT COUNT(`country_id`) FROM `".self::$table_name."`");
            if ($count < 1) {
                // no entries!
                $json_import = MANUFAKTUR_PATH.'/Contact/Data/Setup/Import/countries.json';
                if (!file_exists($json_import)) {
                    throw new \Exception("Can't read the country definition list: $json_import");
                }
                $countries = $this->app['utils']->readJSON($json_import);
                foreach ($countries as $country) {
                    $this->app['db']->insert(self::$table_name, array(
                        'country_code' => $country['country_code'],
                        'country_name' => $this->app['utils']->sanitizeText($country['country_name'])
                    ));
                }
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}
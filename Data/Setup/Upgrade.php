<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Data\Setup;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Protocol;
use phpManufaktur\Contact\Data\Contact\ExtraType;
use phpManufaktur\Contact\Data\Contact\ExtraCategory;
use phpManufaktur\Contact\Data\Contact\Extra;

class Upgrade
{
    protected $app = null;
    protected $db_config = null;

    /**
     * Check if the give column exists in the table
     *
     * @param string $table
     * @param string $column_name
     * @return boolean
     */
    protected function columnExists($table, $column_name)
    {
        try {
            $query = $this->app['db']->query("DESCRIBE `$table`");
            while (false !== ($row = $query->fetch())) {
                if ($row['Field'] == $column_name) return true;
            }
            return false;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Check if the given $table exists
     *
     * @param string $table
     * @throws \Exception
     * @return boolean
     */
    protected function tableExists($table)
    {
        try {
            $query = $this->app['db']->query("SHOW TABLES LIKE '$table'");
            return (false !== ($row = $query->fetch())) ? true : false;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Release 2.0.13
     */
    protected function release_2013()
    {
        try {
            if (!$this->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_protocol')) {
                // create protocol table
                $Protocol = new Protocol($this->app);
                $Protocol->createTable();
                $this->app['monolog']->addInfo('[Contact Upgrade] Create table `contact_protocol`');
            }

            if (!$this->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_contact', 'contact_since')) {
                // add field contact_since in contact_contact
                $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_contact` ADD `contact_since` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `contact_type`";
                $this->app['db']->query($SQL);
                $this->app['monolog']->addInfo('[Contact Upgrade] Add field `contact_since` to table `contact_contact`');
            }

            if ($this->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_person', 'person_contact_since')) {
                // move data from `person_contact_since` to `contact_since`
                $SQL = "SELECT `contact_id`, `person_contact_since` FROM `".FRAMEWORK_TABLE_PREFIX."contact_person`";
                $results = $this->app['db']->fetchAll($SQL);
                foreach ($results as $result) {
                    // move all dates to `contact_contact`
                    $this->app['db']->update(
                        FRAMEWORK_TABLE_PREFIX.'contact_contact',
                        array('contact_since' => $result['person_contact_since']),
                        array('contact_id' => $result['contact_id'])
                    );
                }
                $this->app['monolog']->addInfo('[Contact Upgrade] Moved all `person_contact_since` dates to `contact_since`');
                // delete column
                $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_person` DROP `person_contact_since`";
                $this->app['db']->query($SQL);
                $this->app['monolog']->addInfo('[Contact Upgrade] Deleted column `person_contact_since`');
            }

            if (!$this->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_note', 'note_originator')) {
                // add field `note_originator`
                $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_note` ADD `note_originator` VARCHAR(64) NOT NULL DEFAULT 'SYSTEM' AFTER `note_content`";
                $this->app['db']->query($SQL);
                $this->app['monolog']->addInfo('[Contact Upgrade] Add field `note_originator` to table `contact_note`');
            }

            if (!$this->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_note', 'note_date')) {
                // add field `note_date`
                $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_note` ADD `note_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `note_originator`";
                $this->app['db']->query($SQL);
                $this->app['monolog']->addInfo('[Contact Upgrade] Add field `note_date` to table `contact_note`');
            }

            if (!$this->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_extra_type')) {
                $ExtraType = new ExtraType($this->app);
                $ExtraType->createTable();
                $this->app['monolog']->addInfo('[Contact Upgrade] Create table `contact_extra_type`');
            }

            if (!$this->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_extra_category')) {
                $ExtraCategory = new ExtraCategory($this->app);
                $ExtraCategory->createTable();
                $this->app['monolog']->addInfo('[Contact Upgrade] Create table `contact_extra_category`');
            }

            if (!$this->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_extra')) {
                $Extra = new Extra($this->app);
                $Extra->createTable();
                $this->app['monolog']->addInfo('[Contact Upgrade] Create table `contact_extra`');
            }

        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Execute all available upgrade steps
     *
     * @param Application $app
     * @throws \Exception
     * @return string message
     */
    public function exec(Application $app)
    {
        try {
            $this->app = $app;

            // get Doctrine settings
            $this->db_config = $this->app['utils']->readConfiguration(FRAMEWORK_PATH . '/config/doctrine.cms.json');

            // Release 2.0.13
            $this->app['monolog']->addInfo('[Contact Upgrade] Execute upgrade for release 2.0.13');
            $this->release_2013();

            // prompt message and return
            $this->app['monolog']->addInfo('[Contact Upgrade] The upgrade process was successfull.');
            return "The upgrade process was successfull!";
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}

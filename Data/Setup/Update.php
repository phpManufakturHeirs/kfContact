<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/contact
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Data\Setup;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Protocol;
use phpManufaktur\Contact\Data\Contact\ExtraType;
use phpManufaktur\Contact\Data\Contact\ExtraCategory;
use phpManufaktur\Contact\Data\Contact\Extra;
use phpManufaktur\Contact\Data\Contact\Message;
use phpManufaktur\Contact\Data\Contact\Overview;
use phpManufaktur\Contact\Control\Configuration;
use phpManufaktur\Basic\Control\CMS\InstallAdminTool;
use phpManufaktur\Contact\Data\Contact\Form;

class Update
{
    protected $app = null;
    protected $db_config = null;
    protected $Configuration = null;

    /**
     * Release 2.0.13
     */
    protected function release_2013()
    {
        try {
            if (!$this->app['db.utils']->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_protocol')) {
                // create protocol table
                $Protocol = new Protocol($this->app);
                $Protocol->createTable();
                $this->app['monolog']->addInfo('[Contact Update] Create table `contact_protocol`');
            }

            if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_contact', 'contact_since')) {
                // add field contact_since in contact_contact
                $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_contact` ADD `contact_since` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `contact_type`";
                $this->app['db']->query($SQL);
                $this->app['monolog']->addInfo('[Contact Update] Add field `contact_since` to table `contact_contact`');
            }

            if ($this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_person', 'person_contact_since')) {
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
                $this->app['monolog']->addInfo('[Contact Update] Moved all `person_contact_since` dates to `contact_since`');
                // delete column
                $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_person` DROP `person_contact_since`";
                $this->app['db']->query($SQL);
                $this->app['monolog']->addInfo('[Contact Update] Deleted column `person_contact_since`');
            }

            if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_note', 'note_originator')) {
                // add field `note_originator`
                $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_note` ADD `note_originator` VARCHAR(64) NOT NULL DEFAULT 'SYSTEM' AFTER `note_content`";
                $this->app['db']->query($SQL);
                $this->app['monolog']->addInfo('[Contact Update] Add field `note_originator` to table `contact_note`');
            }

            if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_note', 'note_date')) {
                // add field `note_date`
                $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_note` ADD `note_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00' AFTER `note_originator`";
                $this->app['db']->query($SQL);
                $this->app['monolog']->addInfo('[Contact Update] Add field `note_date` to table `contact_note`');
            }

            if (!$this->app['db.utils']->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_extra_type')) {
                $ExtraType = new ExtraType($this->app);
                $ExtraType->createTable();
                $this->app['monolog']->addInfo('[Contact Update] Create table `contact_extra_type`');
            }

            if (!$this->app['db.utils']->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_extra_category')) {
                $ExtraCategory = new ExtraCategory($this->app);
                $ExtraCategory->createTable();
                $this->app['monolog']->addInfo('[Contact Update] Create table `contact_extra_category`');
            }

            if (!$this->app['db.utils']->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_extra')) {
                $Extra = new Extra($this->app);
                $Extra->createTable();
                $this->app['monolog']->addInfo('[Contact Update] Create table `contact_extra`');
            }

            if (!$this->app['db.utils']->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_message')) {
                $Message = new Message($this->app);
                $Message->createTable();
                $this->app['monolog']->addInfo('[Contact Update] Create table `contact_message`');
            }


        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Release 2.0.14
     */
    protected function release_2014()
    {
        $has_changed = false;
        if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_overview', 'address_area')) {
            // add field `adress_area`
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_overview` ADD `address_area` VARCHAR(128) NOT NULL DEFAULT '' AFTER `address_city`";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add field `address_area` to table `contact_overview`');
            $has_changed = true;
        }
        if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_overview', 'address_state')) {
            // add field `adress_area`
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_overview` ADD `address_state` VARCHAR(128) NOT NULL DEFAULT '' AFTER `address_area`";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add field `address_state` to table `contact_overview`');
            $has_changed = true;
        }
        if ($has_changed) {
            // execute a rebuild of all addresses in the overview table
            $this->app['monolog']->addInfo('[Contact Update] Start rebuilding the table `contact_overview`');
            $ContactOverview = new Overview($this->app);
            $ContactOverview->rebuildOverview();
            $this->app['monolog']->addInfo('[Contact Update] Finished rebuilding the table `contact_overview`');
        }
    }

    /**
     * Release 2.0.15
     */
    protected function release_2015()
    {
        if (false === ($this->app['db.utils']->enumValueExists(FRAMEWORK_TABLE_PREFIX.'contact_contact', 'contact_status', 'PENDING'))) {
            // add PENDING to contact_status
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_contact` CHANGE `contact_status` `contact_status` ENUM('ACTIVE', 'LOCKED', 'PENDING', 'DELETED') NOT NULL DEFAULT 'ACTIVE'";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add ENUM value PENDING to field `contact_status` in table `contact_contact`');
        }

        if (false === ($this->app['db.utils']->enumValueExists(FRAMEWORK_TABLE_PREFIX.'contact_overview', 'contact_status', 'PENDING'))) {
            // add PENDING to contact_status
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_overview` CHANGE `contact_status` `contact_status` ENUM('ACTIVE', 'LOCKED', 'PENDING', 'DELETED') NOT NULL DEFAULT 'ACTIVE'";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add ENUM value PENDING to field `contact_status` in table `contact_overview`');
        }

    }

    /**
     * Release 2.0.21
     */
    protected function release_2021()
    {
        if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_overview', 'contact_login')) {
            // add field
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_overview` ADD `contact_login` VARCHAR(64) NOT NULL DEFAULT '' AFTER `contact_id`";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add field `contact_login` to table `contact_overview`');
            // execute a rebuild of all addresses in the overview table
            $this->app['monolog']->addInfo('[Contact Update] Start rebuilding the table `contact_overview`');
            $ContactOverview = new Overview($this->app);
            $ContactOverview->rebuildOverview();
            $this->app['monolog']->addInfo('[Contact Update] Finished rebuilding the table `contact_overview`');
        }
    }

    /**
     * Release 2.0.30
     */
    protected function release_2030()
    {
        $files = array(
            '/Contact/Template/default/backend',
            '/Contact/Template/default/font-awesome',
            '/Contact/Control/Import/Dialog.php'
        );
        foreach ($files as $file) {
            // remove no longer needed directories and files
            if ($this->app['filesystem']->exists(MANUFAKTUR_PATH.$file)) {
                $this->app['filesystem']->remove(MANUFAKTUR_PATH.$file);
                $this->app['monolog']->addInfo(sprintf('[Contact Update] Removed file or directory %s', $file));
            }
        }
    }

    /**
     * Release 2.0.32
     */
    protected function release_2032()
    {
        if (!$this->app['db.utils']->tableExists(FRAMEWORK_TABLE_PREFIX.'contact_form')) {
            $Form = new Form($this->app);
            $Form->createTable();
            $this->app['monolog']->addInfo('[Contact Update] Create table `contact_form`');
        }
    }

    /**
     * Release 2.0.36
     */
    protected function release_2036()
    {
        if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_category_type', 'category_type_access')) {
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_category_type` ADD `category_type_access` ENUM('ADMIN','PUBLIC') NOT NULL DEFAULT 'ADMIN' AFTER `category_type_id`";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add field `category_type_access` to table `contact_category_type`');
        }
        if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_category_type', 'category_type_target_url')) {
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_category_type` ADD `category_type_target_url` TEXT NOT NULL AFTER `category_type_access`";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add field `category_type_target_url` to table `contact_category_type`');
        }

        $rebuild_overview = false;
        if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_overview', 'category_id')) {
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_overview` ADD `category_id` INT(11) NOT NULL DEFAULT -1 AFTER `address_country_code`";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add field `category_id` to table `contact_overview`');
            $rebuild_overview = true;
        }
        if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_overview', 'category_name')) {
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_overview` ADD `category_name` VARCHAR(64) NOT NULL DEFAULT '' AFTER `category_id`";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add field `category_name` to table `contact_overview`');
            $rebuild_overview = true;
        }
        if (!$this->app['db.utils']->columnExists(FRAMEWORK_TABLE_PREFIX.'contact_overview', 'category_access')) {
            $SQL = "ALTER TABLE `".FRAMEWORK_TABLE_PREFIX."contact_overview` ADD `category_access` ENUM('ADMIN','PUBLIC') NOT NULL DEFAULT 'ADMIN' AFTER `category_name`";
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo('[Contact Update] Add field `category_access` to table `contact_overview`');
            $rebuild_overview = true;
        }
        if ($rebuild_overview) {
            $Overview = new Overview($this->app);
            $Overview->rebuildOverview();
        }

        $config = $this->Configuration->getConfiguration();
        if (!isset($config['command'])) {
            $config['command'] = array(
                'register' => array(
                    'field' => array(
                        'required' => array(
                            'person_gender',
                            'person_last_name',
                        ),
                        'unused' => array(
                            'person_title',
                        )
                    )
                )
            );
            $this->Configuration->setConfiguration($config);
            $this->Configuration->saveConfiguration();
        }
    }

    /**
     * Execute all available update steps
     *
     * @param Application $app
     * @throws \Exception
     * @return string message
     */
    public function exec(Application $app)
    {
        try {
            $this->app = $app;

            // Create Configuration if not exists - only constructor needed
            $this->Configuration = new Configuration($app);

            // get Doctrine settings
            $this->db_config = $this->app['utils']->readConfiguration(FRAMEWORK_PATH . '/config/doctrine.cms.json');

            // Release 2.0.13
            $this->app['monolog']->addInfo('[Contact Update] Execute update for release 2.0.13');
            $this->release_2013();

            // Release 2.0.14
            $this->app['monolog']->addInfo('[Contact Update] Execute update for release 2.0.14');
            $this->release_2014();

            // Release 2.0.15
            $this->app['monolog']->addInfo('[Contact Update] Execute update for release 2.0.15');
            $this->release_2015();

            // Release 2.0.21
            $this->app['monolog']->addInfo('[Contact Update] Execute update for release 2.0.21');
            $this->release_2021();

            // Release 2.0.30
            $this->app['monolog']->addInfo('[Contact Update] Execute update for release 2.0.30');
            $this->release_2030();

            // Release 2.0.32
            $this->app['monolog']->addInfo('[Contact Update] Execute update for release 2.0.32');
            $this->release_2032();

            // Release 2.0.36
            $this->release_2036();


            // setup kit_framework_contact as Add-on in the CMS
            $admin_tool = new InstallAdminTool($app);
            $admin_tool->exec(MANUFAKTUR_PATH.'/Contact/extension.json', '/contact/cms');

            // prompt message and return
            $this->app['monolog']->addInfo('[Contact Update] The update process was successfull.');

            return $app['translator']->trans('Successfull updated the extension %extension%.',
                array('%extension%' => 'Contact'));
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}

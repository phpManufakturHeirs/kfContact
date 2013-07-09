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

class Overview
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_overview';
    }

    /**
     * Create the OVERVIEW table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
        `id` INT(11) NOT NULL AUTO_INCREMENT,
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `contact_name` VARCHAR(128) NOT NULL DEFAULT '',
        `contact_type` ENUM('PERSON','COMPANY') NOT NULL DEFAULT 'PERSON',
        `contact_status` ENUM('ACTIVE','LOCKED','DELETED') NOT NULL DEFAULT 'ACTIVE',
        `person_id` INT(11) NOT NULL DEFAULT '-1',
        `person_gender` ENUM('MALE','FEMALE') NOT NULL DEFAULT 'MALE',
        `person_title` VARCHAR(32) NOT NULL DEFAULT '',
        `person_first_name` VARCHAR(128) NOT NULL DEFAULT '',
        `person_last_name` VARCHAR(128) NOT NULL DEFAULT '',
        `person_nick_name` VARCHAR(128) NOT NULL DEFAULT '',
        `person_birthday` DATE NOT NULL DEFAULT '0000-00-00',
        `company_id` INT(11) NOT NULL DEFAULT '-1',
        `company_name` VARCHAR(128) NOT NULL DEFAULT '',
        `company_department` VARCHAR(128) NOT NULL DEFAULT '',
        `communication_phone` VARCHAR(255) NOT NULL DEFAULT '',
        `communication_email` VARCHAR(255) NOT NULL DEFAULT '',
        `address_id` INT(11) NOT NULL DEFAULT '-1',
        `address_street` VARCHAR(128) NOT NULL DEFAULT '',
        `address_zip` VARCHAR(32) NOT NULL DEFAULT '',
        `address_city` VARCHAR(128) NOT NULL DEFAULT '',
        `address_country_code` VARCHAR(8) NOT NULL DEFAULT '',
        `tags` VARCHAR(512) NOT NULL DEFAULT '',
        `timestamp` TIMESTAMP,
        PRIMARY KEY (`id`),
        UNIQUE (`contact_id`)
        )
    COMMENT='Summary/Overview over all contacts'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_overview'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Refresh insert and update contact records into the overview table.
     * This function should be called each time a contact is inserted or updated.
     *
     * @param integer $contact_id
     * @throws \Exception
     */
    public function refresh($contact_id)
    {
        try {
            // get the contact block
            $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_contact` WHERE `contact_id`='$contact_id'";
            $contact = $this->app['db']->fetchAssoc($SQL);

            if ($contact['contact_type'] == 'COMPANY') {
                // get the company
                $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_company` WHERE `contact_id`='$contact_id'";
                $company = $this->app['db']->fetchAssoc($SQL);

                if ($company['company_primary_address_id'] > 0) {
                    $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_address` WHERE `address_id`='{$company['company_primary_address_id']}'";
                    $address = $this->app['db']->fetchAssoc($SQL);
                }

                if ($company['company_primary_email_id'] > 0) {
                    $SQL = "SELECT `communication_value` FROM `".FRAMEWORK_TABLE_PREFIX."contact_communication` WHERE `communication_id`='{$company['company_primary_email_id']}'";
                    $email = $this->app['db']->fetchColumn($SQL);
                }

                if ($company['company_primary_phone_id'] > 0) {
                    $SQL = "SELECT `communication_value` FROM `".FRAMEWORK_TABLE_PREFIX."contact_communication` WHERE `communication_id`='{$company['company_primary_email_id']}'";
                    $phone = $this->app['db']->fetchColumn($SQL);
                }
            }
            else {
                // get the person
                $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_person` WHERE `contact_id`='$contact_id'";
                $person = $this->app['db']->fetchAssoc($SQL);

                if ($person['person_primary_address_id'] > 0) {
                    $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_address` WHERE `address_id`='{$person['person_primary_address_id']}'";
                    $address = $this->app['db']->fetchAssoc($SQL);
                }

                if ($person['person_primary_email_id'] > 0) {
                    $SQL = "SELECT `communication_value` FROM `".FRAMEWORK_TABLE_PREFIX."contact_communication` WHERE `communication_id`='{$person['person_primary_email_id']}'";
                    $email = $this->app['db']->fetchColumn($SQL);
                }

                if ($person['person_primary_phone_id'] > 0) {
                    $SQL = "SELECT `communication_value` FROM `".FRAMEWORK_TABLE_PREFIX."contact_communication` WHERE `communication_id`='{$person['person_primary_phone_id']}'";
                    $phone = $this->app['db']->fetchColumn($SQL);
                }
            }

            // select the TAGS
            $SQL = "SELECT `tag_name` FROM `".FRAMEWORK_TABLE_PREFIX."contact_tag` WHERE `contact_id`='$contact_id'";
            $tags = array();
            $tags_result = $this->app['db']->fetchAll($SQL);
            foreach ($tags_result as $key => $value) {
                $tags[] = $value;
            }

            $record = array(
                'contact_id' => $contact_id,
                'contact_name' => $contact['contact_name'],
                'contact_type' => $contact['contact_type'],
                'contact_status' => $contact['contact_status'],
                'person_id' => isset($person['person_id']) ? $person['person_id'] : -1,
                'person_first_name' => isset($person['person_first_name']) ? $person['person_first_name'] : '',
                'person_last_name' => isset($person['person_last_name']) ? $person['person_last_name'] : '',
                'person_birthday' => isset($person['person_birthday']) ? $person['person_birthday'] : '0000-00-00',
                'person_title' => isset($person['person_title']) ? $person['person_title'] : '',
                'person_gender' => isset($person['person_gender']) ? $person['person_gender'] : 'MALE',
                'company_id' => isset($company['company_id']) ? $company['company_id'] : -1,
                'company_name' => isset($company['company_name']) ? $company['company_name'] : '',
                'company_department' => isset($company['company_department']) ? $company['company_department'] : '',
                'communication_phone' => isset($phone) ? $phone : '',
                'communication_email' => isset($email) ? $email : '',
                'address_id' => isset($address['address_id']) ? $address['address_id'] : -1,
                'address_street' => isset($address['address_street']) ? $address['address_street'] : '',
                'address_city' => isset($address['address_city']) ? $address['address_city'] : '',
                'address_zip' => isset($address['address_zip']) ? $address['address_zip'] : '',
                'address_country_code' => isset($address['address_country_code']) ? $address['address_country_code'] : '',
                'tags' => implode(',', $tags)
            );

            // prepare the data record
            $data = array();
            foreach ($record as $key => $value) {
                $data[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
            }

            $SQL = "SELECT `contact_id` FROM `".self::$table_name."` WHERE `contact_id`='$contact_id'";
            if (($check = $this->app['db']->fetchColumn($SQL)) == $contact_id) {
                // update the overview
                $this->app['db']->update(self::$table_name, $data, array('contact_id' => $contact_id));
            }
            else {
                // insert a new record
                $this->app['db']->insert(self::$table_name, $data);
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Select all items of the overview sorted by contact_id in ascending order
     *
     * @return array overview list with all items
     * @throws \Exception
     */
    public function selectAll()
    {
        try {
            return $this->app['db']->fetchAll("SELECT * FROM `".self::$table_name."` ORDER BY `contact_id` ASC");
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Rebuild the complete overview table
     *
     * @throws \Exception
     */
    public function rebuildOverview()
    {
        try {
            $contact = new Contact($this->app);
            $contacts = $contact->selectAll();
            foreach ($contacts as $contact) {
                $this->refresh($contact['contact_id']);
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return the column names of the overview table
     *
     * @throws \Exception
     * @return multitype:unknown
     */
    public function getColumns()
    {
        try {
            $result = $this->app['db']->fetchAll("SHOW COLUMNS FROM `".self::$table_name."`");
            $columns = array();
            foreach ($result as $column) {
                $columns[] = $column['Field'];
            }
            return $columns;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    public function count($status=null)
    {
        try {
            $SQL = "SELECT COUNT(*) FROM `".self::$table_name."`";
            if (is_array($status) && !empty($status)) {
                $SQL .= " WHERE (";
                $start = true;
                foreach ($status as $stat) {
                    if (!$start) {
                        $SQL .= " OR ";
                    }
                    else {
                        $start = false;
                    }
                    $SQL .= "`contact_status`='$stat'";
                }
                $SQL .= ")";
            }
            return $this->app['db']->fetchColumn($SQL);
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    public function selectList($limit_from, $rows_per_page, $select_status=null, $order_by=null, $order_direction='ASC')
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."`";
            if (is_array($select_status) && !empty($select_status)) {
                $SQL .= " WHERE (";
                $start = true;
                foreach ($select_status as $status) {
                    if (!$start) {
                        $SQL .= " OR ";
                    }
                    else {
                        $start = false;
                    }
                    $SQL .= "`contact_status`='$status'";
                }
                $SQL .= ")";
            }
            if (is_array($order_by) && !empty($order_by)) {
                $SQL .= " ORDER BY ";
                $start = true;
                foreach ($order_by as $by) {
                    if (!$start) {
                        $SQL .= ", ";
                    }
                    else {
                        $start = false;
                    }
                    $SQL .= "`$by`";
                }
                $SQL .= " $order_direction";
            }
            $SQL .= " LIMIT $limit_from, $rows_per_page";
            return $this->app['db']->fetchAll($SQL);
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
}
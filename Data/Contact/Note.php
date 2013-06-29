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

class Note
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
        self::$table_name = FRAMEWORK_TABLE_PREFIX.'contact_note';
    }

    /**
     * Create the NOTE table
     *
     * @throws \Exception
     */
    public function createTable()
    {
        $table = self::$table_name;
        $SQL = <<<EOD
    CREATE TABLE IF NOT EXISTS `$table` (
    		`note_id` INT(11) NOT NULL AUTO_INCREMENT,
        `contact_id` INT(11) NOT NULL DEFAULT '-1',
        `note_title` VARCHAR(255) NOT NULL DEFAULT '',
    		`note_type` ENUM('TEXT', 'HTML') NOT NULL DEFAULT 'TEXT',
    		`note_content` TEXT NOT NULL,
        `note_status` ENUM('ACTIVE', 'LOCKED', 'DELETED') NOT NULL DEFAULT 'ACTIVE',
        `note_timestamp` TIMESTAMP,
        PRIMARY KEY (`note_id`),
        INDEX (`contact_id`)
        )
    COMMENT='The notes for the contact table'
    ENGINE=InnoDB
    AUTO_INCREMENT=1
    DEFAULT CHARSET=utf8
    COLLATE='utf8_general_ci'
EOD;
        try {
            $this->app['db']->query($SQL);
            $this->app['monolog']->addInfo("Created table 'contact_note'", array(__METHOD__, __LINE__));
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return a default (empty) NOTE record
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        return array(
            'note_id' => -1,
            'contact_id' => -1,
            'note_title' => '',
            'note_type' => 'TEXT',
            'note_content' => '',
            'note_status' => 'ACTIVE',
            'note_timestamp' => '0000-00-00 00:00:00'
        );
    }

    /**
     * Select a note by the given note_id
     * Return FALSE if the record does not exists
     *
     * @param integer $contact_id
     * @throws \Exception
     * @return multitype:array|boolean
     */
    public function select($note_id)
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `note_id`='$note_id'";
            $result = $this->app['db']->fetchAssoc($SQL);
            if (is_array($result) && isset($result['note_id'])) {
                $note = array();
                foreach ($result as $key => $value) {
                    $note[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                }
                return $note;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Insert a new record in the NOTE table
     *
     * @param array $data
     * @param reference integer $note_id
     * @throws \Exception
     */
    public function insert($data, &$note_id=null)
    {
        try {
            $insert = array();
            $TextOnly = (isset($data['note_type']) && ($data['note_type'] === 'HTML')) ? false : true;
            foreach ($data as $key => $value) {
                if (($key == 'note_id') || ($key == 'note_timestamp')) continue;
            		if ($TextOnly && ($key === 'note_content')) {
            			$value = strip_tags($value);
            		}
                $insert[$this->app['db']->quoteIdentifier($key)] = is_string($value) ? $this->app['utils']->sanitizeText($value) : $value;
            }
            $this->app['db']->insert(self::$table_name, $insert);
            $note_id = $this->app['db']->lastInsertId();
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Return all NOTES for the given Contact ID
     *
     * @param integer $contact_id
     * @param string $status
     * @param string $status_operator
     * @throws \Exception
     * @return array|boolean
     */
    public function selectByContactID($contact_id, $status='DELETED', $status_operator='!=')
    {
        try {
            $SQL = "SELECT * FROM `".self::$table_name."` WHERE `contact_id`='$contact_id' AND `note_status`{$status_operator}'{$status}'";
            $results = $this->app['db']->fetchAll($SQL);
            if (is_array($results)) {
                $note = array();
                $level = 0;
                foreach ($results as $result) {
                    foreach ($result as $key => $value) {
                        $note[$level][$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                    }
                    $level++;
                }
                return $note;
            }
            else {
                return false;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }
}
<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Data\Import\KeepInTouch;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Title;
use phpManufaktur\Contact\Data\Contact\TagType;
use phpManufaktur\Contact\Data\Contact\CategoryType;

class KeepInTouch
{
    protected $app = null;
    protected $Title = null;
    protected $TagType = null;
    protected $CategoryType = null;

    /**
     * Constructor
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->Title = new Title($app);
        $this->TagType = new TagType($app);
        $this->CategoryType = new CategoryType($app);
    }

    public function existsKIT()
    {
        return file_exists(CMS_PATH.'/modules/kit/class.interface.php');
    }

    /**
     * Check the installed KeepInTouch Release
     *
     * @throws \Exception
     * @return string release number
     */
    public function getKITrelease()
    {
        try {
            $SQL = "SELECT `version` FROM `".CMS_TABLE_PREFIX."addons` WHERE `name`='KeepInTouch'";
            return $this->app['db']->fetchColumn($SQL);
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Count all active and locked KIT records
     *
     * @throws \Exception
     * @return integer number of KIT records
     */
    public function countKITrecords()
    {
        try {
            $SQL = "SELECT COUNT(`contact_id`) AS total FROM `".CMS_TABLE_PREFIX."mod_kit_contact` WHERE `contact_status`!='statusDeleted'";
            return $this->app['db']->fetchColumn($SQL);
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    public function getAllKITids()
    {
        try {
            $SQL = "SELECT `contact_id` FROM `".CMS_TABLE_PREFIX."mod_kit_contact` WHERE `contact_status`!='statusDeleted'";
            return $this->app['db']->fetchAll($SQL);
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Create a identifier from the given string. Allow only A-Z, 0-9, space and
     * underscore.
     *
     * @param string $string
     * @return Ambigous <string, mixed>
     */
    protected function createIdentifier($string)
    {
        $identifier = '';
        for ($i=0; $i < strlen($string); $i++) {
            if (false !== strpos('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ_ ÄÖÜß', $string[$i])) {
                $identifier .= str_replace(array(' ','Ä','Ö','Ü'), array('_','AE','OE','UE'), $string[$i]);
            }
        }
        return $identifier;
    }

    /**
     * Get the value for the identifier of the KeepInTouch table mod_kit_contact_array_cfg
     *
     * @param string $identifier
     * @throws \Exception
     * @return string
     */
    protected function getIdentifierValue($identifier)
    {
        try {
            $SQL = "SELECT `array_value` FROM `".CMS_TABLE_PREFIX."mod_kit_contact_array_cfg` WHERE `array_identifier`='$identifier'";
            return $this->app['db']->fetchColumn($SQL);
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get the KeepInTouch record for the given KIT ID.
     * This function preprocess the data for the later import
     *
     * @param integer $kit_id
     * @throws \Exception
     * @return boolean|Ambigous <unknown, multitype:multitype: string unknown >
     */
    public function getKITrecord($kit_id)
    {
        try {
            $SQL = "SELECT * FROM `".CMS_TABLE_PREFIX."mod_kit_contact` WHERE `contact_id`='$kit_id'";
            $origin = $this->app['db']->fetchAssoc($SQL);
            if (!isset($origin['contact_id'])) {
                // contact_id does not exists!
                return false;
            }
            $contact = array();
            $contact['origin'] = $origin;
            // get addresses
            $contact['addresses'] = array();
            if (!empty($origin['contact_address_ids'])) {
                $address_ids = explode(',', $origin['contact_address_ids']);
                foreach ($address_ids as $address_id) {
                    $SQL = "SELECT * FROM `".CMS_TABLE_PREFIX."mod_kit_contact_address` WHERE `address_id`='$address_id'";
                    $address = $this->app['db']->fetchAssoc($SQL);
                    if (!isset($address['address_id'])) {
                        continue;
                    }
                    if (isset($origin['contact_address_standard']) && ($origin['contact_address_standard'] == $address_id)) {
                        $address['is_default'] = true;
                    }
                    else {
                        $address['is_default'] = false;
                    }
                    $contact['addresses'][] = $address;
                }
            }
            // get emails
            $contact['communication'] = array();
            if (!empty($origin['contact_email'])) {
                $email_addresses = explode(',', $origin['contact_email']);
                $i=0;
                foreach ($email_addresses as $email_item) {
                    list($type, $email) = explode('|', $email_item);
                    if (isset($origin['contact_email_standard']) && ($origin['contact_email_standard'] == $i)) {
                        $usage = 'PRIMARY';
                        // this is also the login !!!
                        $contact['login'] = strtolower(trim($email));
                    }
                    elseif ($type == 'typeCompany') {
                        $usage = 'BUSINESS';
                    }
                    else {
                        $usage = 'PRIVATE';
                    }
                    $contact['communication'][] = array(
                        'type' => 'EMAIL',
                        'address' => strtolower(trim($email)),
                        'usage' => $usage
                    );
                    $i++;
                }
            }
            // get phone & fax
            if (!empty($origin['contact_phone'])) {
                $phone_addresses = explode(',', $origin['contact_phone']);
                $i=0;
                foreach ($phone_addresses as $phone_item) {
                    list($type, $phone) = explode('|', $phone_item);
                    if (isset($origin['contact_phone_standard']) && ($origin['contact_phone_standard'] == $i)) {
                        $usage = 'PRIMARY';
                    }
                    else {
                        $usage = 'PRIVATE';
                    }
                    if ($type == 'phonePhone') {
                        $use_type = 'PHONE';
                    }
                    elseif ($type == 'phoneHandy') {
                        $use_type = 'CELL';
                    }
                    elseif ($type == 'phoneFax') {
                        $use_type == 'FAX';
                    }
                    else {
                        // unknown type
                        $i++;
                        continue;
                    }
                    $contact['communication'][] = array(
                        'type' => $use_type,
                        'address' => strtolower(trim($phone)),
                        'usage' => $usage
                    );
                    $i++;
                }
            }
            // get internet
            if (!empty($origin['contact_internet'])) {
                $internet_addresses = explode(',', $origin['contact_internet']);
                $i=0;
                foreach ($internet_addresses as $internet_item) {
                    list($type, $internet) = explode('|', $internet_item);
                    if ($type == 'inetFacebook') {
                        $use_type = 'FACEBOOK';
                    }
                    elseif ($type == 'inetHomepage') {
                        $use_type = 'URL';
                    }
                    elseif ($type == 'inetTwitter') {
                        $use_type == 'TWITTER';
                    }
                    elseif ($type == 'inetXing') {
                        $use_type == 'XING';
                    }
                    else {
                        // unknown type
                        $i++;
                        continue;
                    }
                    $contact['communication'][] = array(
                        'type' => $use_type,
                        'address' => strtolower(trim($internet)),
                        'usage' => 'PRIVATE'
                    );
                    $i++;
                }
            }

            // get gender of the person
            $contact['person_gender'] = (isset($origin['contact_person_title']) && ($origin['contact_person_title'] == 'titleMister')) ? 'MALE' : 'FEMALE';

            // check title
            if ($origin['contact_person_title_academic'] == 'academicDr') {
                $contact['person_title'] = 'DOC';
            }
            elseif ($origin['contact_person_title_academic'] == 'academicProf') {
                $contact['person_title'] = 'PROF';
            }
            elseif ($origin['contact_person_title_academic'] == 'academicNone') {
                $contact['person_title'] = 'NO_TITLE';
            }
            else {
                $value = $this->getIdentifierValue($origin['contact_person_title_academic']);
                $check = strtoupper($value);
                $identifier = $this->createIdentifier($check);
                if (!$this->Title->existsTitle($identifier)) {
                    $data = array(
                        'title_identifier' => $identifier,
                        'title_short' => $value,
                        'title_long' => $value
                    );
                    $this->Title->insert($data);
                }
                $contact['person_title'] = $identifier;
            }


            // check distribution
            $contact['tags'] = array();
            if (!empty($origin['contact_distribution_ids'])) {
                $distributions = explode(',', $origin['contact_distribution_ids']);
                foreach ($distributions as $distribution) {
                    $value = $this->getIdentifierValue($distribution);
                    $check = strtoupper($value);
                    $identifier = $this->createIdentifier($check);
                    if (!$this->TagType->existsTag($identifier)) {
                        $data = array(
                            'tag_name' => $identifier,
                            'tag_description' => $value
                        );
                        $this->TagType->insert($data);
                    }
                    $contact['tags'][] = $identifier;
                }
            }

            // check newsletter
            if (!empty($origin['contact_newsletter_ids'])) {
                $newsletters = explode(',', $origin['contact_newsletter_ids']);
                foreach ($newsletters as $newsletter) {
                    $value = $this->getIdentifierValue($newsletter);
                    $check = strtoupper($value);
                    $identifier = $this->createIdentifier($check);
                    $identifier = 'NL_'.$identifier;
                    if (!$this->TagType->existsTag($identifier)) {
                        $data = array(
                            'tag_name' => $identifier,
                            'tag_description' => $value.' (NL)'
                        );
                        $this->TagType->insert($data);
                    }
                    $contact['tags'][] = $identifier;
                }
            }

            // check categories
            $contact['categories'] = array();
            if (!empty($origin['contact_category_ids'])) {
                $categories = explode(',', $origin['contact_category_ids']);
                foreach ($categories as $category) {
                    $value = $this->getIdentifierValue($category);
                    $check = strtoupper($value);
                    $identifier = $this->createIdentifier($check);
                    if (!$this->CategoryType->existsCategory($identifier)) {
                        $data = array(
                            'category_type_name' => $identifier,
                            'category_type_description' => $value
                        );
                        $this->CategoryType->insert($data);
                    }
                    $contact['categories'][] = $identifier;
                }
            }
            // return the contact array
            return $contact;
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get the KeepInTouch protocol entries for the given KIT ID
     *
     * @param integer $kit_id
     * @throws \Exception
     * @return multitype:multitype:unknown
     */
    public function getProtocol($kit_id)
    {
        try {
            $SQL = "SELECT * FROM `".CMS_TABLE_PREFIX."mod_kit_contact_protocol` WHERE `contact_id`='$kit_id' AND `protocol_status`='statusActive'";
            $results = $this->app['db']->fetchAll($SQL);
            $protocols = array();
            if (is_array($results)) {
                foreach ($results as $result) {
                    $protocol = array();
                    foreach ($result as $key => $value) {
                        $protocol[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                    }
                    $protocols[] = $protocol;
                }
                return $protocols;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

    /**
     * Get all KeepInTouch memos for the given KIT ID
     *
     * @param integer $kit_id
     * @throws \Exception
     * @return multitype:multitype:unknown
     */
    public function getMemos($kit_id)
    {
        try {
            $SQL = "SELECT * FROM `".CMS_TABLE_PREFIX."mod_kit_contact_memos` WHERE `contact_id`='$kit_id' AND `memo_status`='statusActive'";
            $results = $this->app['db']->fetchAll($SQL);
            $memos = array();
            if (is_array($results)) {
                foreach ($results as $result) {
                    $memo = array();
                    foreach ($result as $key => $value) {
                        $memo[$key] = is_string($value) ? $this->app['utils']->unsanitizeText($value) : $value;
                    }
                    $memos[] = $memo;
                }
                return $memos;
            }
        } catch (\Doctrine\DBAL\DBALException $e) {
            throw new \Exception($e);
        }
    }

}

<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Helper;

use Silex\Application;
use phpManufaktur\Contact\Control\Helper\ContactParent;
use phpManufaktur\Contact\Data\Contact\TagType as TagTypeData;

class ContactTagType extends ContactParent
{
    protected $TagTypeData = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->TagTypeData = new TagTypeData($this->app);
    }

    /**
     * Return a default (empty) PERSON contact record.
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        return $this->TagTypeData->getDefaultRecord();
    }

    public function select($tag_type_id)
    {
        return $this->TagTypeData->select($tag_type_id);
    }

    /**
     * Validate the given TAG type record
     *
     * @param array $type_data
     * @return boolean
     */
    public function validate(&$type_data)
    {
        if (is_null($type_data['tag_type_id']) || !isset($type_data['tag_type_id'])) {
            $this->setMessage("Missing the %identifier%! The ID should be set to -1 if you insert a new record.",
                array('%identifier%' => 'tag_type_id'));
            return false;
        }

        if (is_null($type_data['tag_name']) || !isset($type_data['tag_name']) || empty($type_data['tag_name'])) {
            $this->setMessage("The tag name must be always set und not empty!");
            return false;
        }

        if (is_null($type_data['tag_description']) || !isset($type_data['tag_description'])) {
            // set an empty value
            $type_data['tag_description'] = '';
        }
        return true;
    }

    /**
     * Insert a new NOTE record. Check first for values which belong to depending
     * contact tables
     *
     * @param array $data
     * @param reference integer $tag_type_id
     * @return boolean
     */
    public function insert($data, &$tag_type_id=null)
    {

        if ($this->validate($data)) {
            if (!$this->TagTypeData->existsTag($data['tag_name'])) {
                $this->TagTypeData->insert($data, $tag_type_id);
                $this->setMessage("Inserted the new tag %tag_name%.", array('%tag_name%' => $data['tag_name']));
                return true;
            }
            else {
                // the tag already exists
                $this->setMessage("The tag %tag_name% already exists!", array('%tag_name%' => $data['tag_name']));
                return false;
            }
        }
        // has not validated
        return false;
    }

    /**
     * Process the update for the given TAG type record
     *
     * @param array $type_data
     * @param integer $tag_type_id
     * @return boolean
     */
    public function update($type_data, $tag_type_id)
    {
        if ($this->validate($type_data)) {
            $this->TagTypeData->update($type_data, $tag_type_id);
            $this->setMessage("The tag type has successfull updated.");
            return true;
        }
        return false;
    }

    public function delete($tag_type_id)
    {
        $this->TagTypeData->delete($tag_type_id);
        $this->setMessage("Deleted tag type with the ID %tag_type_id%", array('%tag_type_id%' => $tag_type_id));
        return true;
    }
}


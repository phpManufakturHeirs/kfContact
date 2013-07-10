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
use phpManufaktur\Contact\Data\Contact\Tag;
use phpManufaktur\Contact\Data\Contact\TagType;

class ContactTag extends ContactParent
{

    protected $Tag = null;
    protected $TagType = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->Tag = new Tag($this->app);
        $this->TagType = new TagType($this->app);
    }

    public function getDefaultRecord()
    {
        return $this->Tag->getDefaultRecord();
    }

    public function validate(&$tag_data, $contact_data=array(), $option=array())
    {
        // tag name must be set
        if (is_null($tag_data['tag_name']) || !isset($tag_data['tag_name']) || empty($tag_data['tag_name'])) {
            $this->setMessage('Missing the key %field_name%, it must always set and not empty!',
                array('%field_name%' => 'tag_name'));
            return false;
        }

        // tag name must be valid
        $matches = array();
        $tag_data['tag_name'] = str_replace(' ', '_', strtoupper($tag_data['tag_name']));
        if (preg_match_all('/[^A-Z0-9_$]/', $tag_data['tag_name'], $matches)) {
            // name check fail
            $this->setMessage('Allowed characters for the %identifier% identifier are only A-Z, 0-9 and the Underscore. The identifier will be always converted to uppercase.',
                array('%identifier%' => 'Tag'));
            return false;
        }

        // tag name must exists
        if (!$this->TagType->existsTag($tag_data['tag_name'])) {
            $this->setMessage('The #tag %tag_name% does not exists!',
                array('%tag_name%' => strtoupper($tag_data['tag_name'])));
            return false;
        }

        return true;
    }

    /**
     * Insert a TAG
     *
     * @param array $data
     * @param integer $contact_id
     * @param reference integer $tag_id
     * @param reference boolean $has_inserted
     * @return boolean
     */
    public function insert($data, $contact_id, &$tag_id=null, &$has_inserted=null)
    {
        // enshure that the contact_id isset
        $data['contact_id'] = $contact_id;
        $has_inserted = false;

        if (is_null($data['tag_name']) || !isset($data['tag_name']) || empty($data['tag_name'])) {
            // nothing to do...
            return true;
        }
        // validate...
        if (!$this->validate($data)) {
            return false;
        }

        if ($this->Tag->isTagAlreadySet($data['tag_name'], $contact_id)) {
            // nothing to do, TAG is already inserted ...
            return true;
        }

        $this->Tag->insert($data, $tag_id);
        $has_inserted = true;
        return true;
    }

    public function delete($tag_name)
    {
        $this->Tag->delete($tag_name);
    }

}
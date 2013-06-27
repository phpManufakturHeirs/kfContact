<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Address;

class ContactAddress extends ContactParent
{
    protected $Address = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->Address = new Address($this->app);
    }

    public function getDefaultRecord()
    {
        return $this->Address->getDefaultRecord();
    }

    /**
     * Validate the given ADDRESS
     *
     * @param reference array $address_data
     * @param array $contact_data
     * @param array $option
     * @return boolean
     */
    public function validate(&$address_data, $contact_data=array(), $option=array())
    {

        return true;
    }

    /**
     * Insert a ADDRESS record
     *
     * @param array $data
     * @param integer $contact_id
     * @param reference integer $address_id
     * @return boolean
     */
    public function insert($data, $contact_id, &$address_id)
    {
        if ((isset($data['address_street']) && (!empty($data['address_street']))) ||
            (isset($data['address_city']) && !empty($data['address_city'])) ||
            (isset($data['address_zip']) && !empty($data['address_zip']))) {
            // insert the address
            if (!isset($data['contact_id']) || ($data['contact_id'] < 1)) {
                $data['contact_id'] = $contact_id;
            }
            if (!$this->validate($data)) {
                return false;
            }
            $this->Address->insert($data, $address_id);
        }
        return true;
    }

    public function update($new_data, $old_data, $address_id, $has_changed=false)
    {

    }

}
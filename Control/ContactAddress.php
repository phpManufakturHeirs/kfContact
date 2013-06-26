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

    public function validate($data)
    {

        return true;
    }

    public function insert($data, $contact_id, &$address_id)
    {
        // check the minimun requirements
        if ((isset($data['address_street']) && (!empty($data['address_street']))) ||
            (isset($data['address_city']) && !empty($data['address_city'])) ||
            (isset($data['address_zip']) && !empty($data['address_zip']))) {
            $check = true;
            $this->clearMessage();
            if (!isset($data['contact_id']) || ($data['contact_id'] < 1)) {
                $data['contact_id'] = $contact_id;
            }
            $this->Address->insert($data, $address_id);
            return $check;
        }
        // nothing to do - return TRUE
        return true;
    }

}
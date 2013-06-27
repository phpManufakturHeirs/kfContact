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
use phpManufaktur\Contact\Data\Contact\Communication;
use phpManufaktur\Contact\Data\Contact\Person as PersonData;

class ContactPerson extends ContactParent
{
    protected $CommunicationData = null;
    protected $PersonData = null;
    protected $ContactCommunication = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->CommunicationData = new Communication($this->app);
        $this->PersonData = new PersonData($this->app);
        $this->ContactCommunication = new ContactCommunication($this->app);
    }

    /**
     * Return a default (empty) PERSON contact record.
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        return $this->PersonData->getDefaultRecord();
    }

    /**
     * Validate the given PERSON data record
     *
     * @param reference array $person_data
     * @param array $contact_data
     * @param array $option
     * @return boolean
     */
    public function validate(&$person_data, $contact_data=array(), $option=array())
    {
        if (!isset($person_data['person_id'])) {
            $this->setMessage("Missing the %identifier%! The ID should be set to -1 if you insert a new record.",
                array('%identifier%' => 'person_id'));
            return false;
        }
        return true;
    }

    /**
     * Insert a new PERSON record. Check first for values which belong to depending
     * contact tables
     *
     * @param array $data
     * @param integer $contact_id
     * @param string $person_id
     * @throws ContactException
     * @return boolean
     */
    public function insert($data, $contact_id, &$person_id=null)
    {
        if (!isset($data['contact_id'])) {
            $data['contact_id'] = $contact_id;
        }
        if (!$this->validate($data)) {
            return false;
        }
        $person_id = -1;
        $this->PersonData->insert($data, $person_id);
        $this->app['monolog']->addInfo("Inserted person record for the contactID {$contact_id}", array(__METHOD__, __LINE__));
        return true;
    }

    public function update($new_data, $old_data, $person_id, &$has_changed=false)
    {
        $has_changed = false;
        if (!$this->validate($new_data)) {
            return false;
        }
        $changed = array();
        foreach ($new_data as $key => $value) {
            if ($key === 'person_id') continue;
            if (isset($old_data[$key]) && ($old_data[$key] != $value)) {
                $changed[$key] = $value;
            }
        }
        if (!empty($changed)) {
            $this->PersonData->update($changed, $person_id);
            $has_changed = true;
        }
        return true;
    }
}


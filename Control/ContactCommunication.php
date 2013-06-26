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
use Symfony\Component\Validator\Constraints as Assert;
use phpManufaktur\Contact\Data\Contact\Communication;
use phpManufaktur\Contact\Data\Contact\CommunicationType;
use phpManufaktur\Contact\Data\Contact\CommunicationUsage;

class ContactCommunication extends ContactParent
{
    protected $Communication = null;
    protected $CommunicationType = null;
    protected $CommunicationUsage = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->Communication = new Communication($this->app);
        $this->CommunicationType = new CommunicationType($this->app);
        $this->CommunicationUsage = new CommunicationUsage($this->app);
    }

    /**
     * Return a default (empty) COMMUNICATION record
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        return $this->Communication->getDefaultRecord();
    }

    /**
     * Validate the COMMUNICATION entry
     *
     * @param reference array $communication_data
     * @param array $contact_data
     * @param array $option
     * @return boolean
     */
    public function validate(&$communication_data, $contact_data=array(), $option=array())
    {
        if (!isset($communication_data['contact_id']) || !is_numeric($communication_data['contact_id'])) {
            if (isset($contact_data['contact']['contact_id'])) {
                $communication_data['contact_id'] = $contact_data['contact']['contact_id'];
            }
            else {
                $this->setMessage("Missing the CONTACT ID in the COMMUNICATION record.");
                return false;
            }
        }

        if (!isset($communication_data['communication_id']) || !is_numeric($communication_data['communication_id'])) {
            $this->setMessage("Missing the COMMUNICATION ID in the COMMUNICATION record.");
            return false;
        }

        if (!isset($communication_data['communication_type']) || empty($communication_data['communication_type'])) {
            $this->setMessage("The COMMUNICATION TYPE must be set!");
            return false;
        }

        if (!$this->CommunicationType->existsType($communication_data['communication_type'])) {
            $this->setMessage("The COMMUNICATION TYPE %type% does not exists!",
                array('%type%' => $communication_data['communication_type']));
            return false;
        }

        if (!isset($communication_data['communication_usage']) || empty($communication_data['communication_usage'])) {
            if (isset($option['usage']['default']) && !empty($option['usage']['default'])) {
                $communication_data['communication_usage'] = $option['usage']['default'];
            }
            else {
                $this->setMessage("The COMMUNICATION USAGE must be set!");
                return false;
            }
        }

        if (!$this->CommunicationUsage->existsUsage($communication_data['communication_usage'])) {
            $this->setMessage("The COMMUNICATION USAGE %usage% does not exists!",
                array('%usage%' => $communication_data['communication_usage']));
            return false;
        }

        if (!isset($communication_data['communication_value']) || empty($communication_data['communication_value'])) {
            if (isset($option['value']['ignore_if_empty']) && (false === $option['value']['ignore_if_empty'])) {
                // dont ignore an empty value
                $this->setMessage("The COMMUNICATION VALUE should not be empty!");
                return false;
            }
        }

        if (($communication_data['communication_type'] === 'EMAIL') && !empty($communication_data['communication_value'])) {
            $errors = $this->app['validator']->validateValue($communication_data['communication_value'], new Assert\Email());
            if (count($errors) > 0) {
                $this->setMessage("The email %email% is not valid, please check your input!",
                    array('%email%' => $communication_data['communication_value']));
                return false;
            }
        }

        return true;
    }

    /**
     * Insert a new COMMUNICATION record
     *
     * @param array $data
     * @param integer $contact_id
     * @param reference integer $communication_id
     * @return boolean
     */
    public function insert($data, $contact_id, &$communication_id)
    {
        // insert the communication record
        if (!isset($data['contact_id'])) {
            $data['contact_id'] = $contact_id;
        }
        if (!$this->validate($data)) {
            return false;
        }

        // it is possible that the validation igonore empty values!
        if (empty($data['communication_value'])) {
            // ... do nothing!
            return true;
        }
        $this->Communication->insert($data, $communication_id);
        return true;
    }

    /**
     * Update the given COMMUNICATION record
     *
     * @param array $new_data
     * @param array $old_data
     * @param integer $communication_id
     * @return boolean
     */
    public function update($new_data, $old_data, $communication_id)
    {
        if (empty($new_data['communication_value'])) {
            // check if this entry can be deleted
            if ($this->Communication->isUsedAsPrimaryConnection($communication_id,
                $old_data['contact_id'], $old_data['communication_type'])) {
                // entry is marked for primary communication and can not deleted!
                $this->setMessage("The %type% entry %value% is marked for primary communication and can not removed!",
                    array('%type%' => $old_data['communication_type'], '%value%' => $old_data['communication_value']));
                return false;
            }
            // delete the entry
            $this->Communication->delete($communication_id);
            $this->setMessage("The communication entry %communication% was successfull deleted.",
                array('%communication%' => $old_data['communication_value']));
            return true;
        }

        // validate the new data
        if (!$this->validate($new_data)) {
            return false;
        }

        // process the new data
        $changed = array();
        foreach ($new_data as $key => $value) {
            if ($key === 'communication_id') continue;
            if ($old_data[$key] !== $value) {
                $changed[$key] = $value;
            }
        }

        if (!empty($changed)) {
            // update the communication record
            $this->Communication->update($changed, $communication_id);
        }
        return true;
    }
}
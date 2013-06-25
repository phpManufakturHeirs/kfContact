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
use phpManufaktur\Contact\Data\Contact\CommunicationType as CommunicationTypeData;
use phpManufaktur\Contact\Data\Contact\CommunicationUsage as CommunicationUsageData;
use phpManufaktur\Contact\Data\Contact\Communication as CommunicationData;

class ContactCommunication extends ContactParent
{
    protected $CommunicationTypeData = null;
    protected $CommunicationUsageData = null;
    protected $CommunicationData = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->CommunicationTypeData = new CommunicationTypeData($this->app);
        $this->CommunicationUsageData = new CommunicationUsageData($this->app);
        $this->CommunicationData = new CommunicationData($this->app);
    }

    /**
     * Return a default (empty) COMMUNICATION record
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        return $this->CommunicationData->getDefaultRecord();
    }

    public function validate($data)
    {
        $check = true;
        $this->clearMessage();

        if (isset($data['communication'])) {
            foreach ($data['communication'] as $communication) {
                if (isset($communication['communication_type']) && isset($communication['communication_value']) &&
                ($communication['communication_type'] === 'EMAIL')) {
                    $errors = $this->app['validator']->validateValue($communication['communication_value'], new Assert\Email());
                    if (count($errors) > 0) {
                        $this->setMessage('The email address %email% is not valid, please check your input!',
                            array('%email%' => $communication['communication_value']));
                        $check = false;
                    }
                }
            }
        }
        return $check;
    }

    public function insert($data, $contact_id, &$communication_id)
    {
        // check the minimum parameters
        if (!isset($data['communication_type']) || empty($data['communication_type']) ||
            !isset($data['communication_value']) || empty($data['communication_value'])) {
            $this->setMessage("Missing the communication parameters 'type' or 'value', can't insert the record!");
            return false;
        }
        // check if type exists
        if (!$this->CommunicationTypeData->existsType($data['communication_type'])) {
            $this->setMessage("The communication type %type% is not defined, can't insert the record!",
                array('%type%' => $data['communication_type']));
            $this->app['monolog']->addInfo("The communication type {$data['communication_type']} is not defined!",
                array(__METHOD__, __LINE__));
            return false;
        }
        // check if the usage exists
        if (isset($data['communication_usage']) && !$this->CommunicationUsageData->existsUsage($data['communication_usage'])) {
            $this->setMessage("The communication usage %usage% is not defined, can't insert the record!",
                array('%usage%' => $data['communication_usage']));
            $this->app['monolog']->addInfo("The communication usage {$data['communication_usage']} is not defined!",
                array(__METHOD__, __LINE__));
            return false;
        }
        // insert the communication record
        if (!isset($data['contact_id'])) {
            $data['contact_id'] = $contact_id;
        }
        $this->CommunicationData->insert($data, $communication_id);
        return true;
    }
}
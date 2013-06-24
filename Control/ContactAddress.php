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
use phpManufaktur\Contact\Data\Contact\Address as AddressData;

class ContactAddress
{

    protected $app = null;
    protected static $message = '';
    protected $AdressData = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->AdressData = new AddressData($this->app);
    }

    /**
     * @return the $message
     */
    public function getMessage()
    {
        return self::$message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message, $params=array())
    {
        self::$message .= $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'message.twig'),
            array('message' => $this->app['translator']->trans($message, $params)));
    }

    /**
     * Check if a message is active
     *
     * @return boolean
     */
    public function isMessage()
    {
        return !empty(self::$message);
    }

    /**
     * Clear the existing message(s)
     */
    public function clearMessage()
    {
        self::$message = '';
    }

    public function getDefaultRecord()
    {
        return array(
            'address_id' => -1,
            'contact_id' => -1,
            'address_type' => 'OTHER',
            'address_identifier' => '',
            'address_description' => '',
            'address_street' => '',
            'address_appendix_1' => '',
            'address_appendix_2' => '',
            'address_zip' => '',
            'address_city' => '',
            'address_area' => '',
            'address_state' => '',
            'address_country_code' => '',
            'address_status' => 'ACTIVE',
            'address_timestamp' => '0000-00-00 00:00:00'
        );
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
            $this->AdressData->insert($data, $address_id);

            return $check;
        }
        // nothing to do - return TRUE
        return true;
    }

}
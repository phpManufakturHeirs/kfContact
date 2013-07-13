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

use phpManufaktur\Contact\Control\Helper\ContactParent;
use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Company;

class ContactCompany extends ContactParent
{

    protected $CompanyData = null;

    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->CompanyData = new Company($this->app);
    }

    /**
     * Return a default (empty) COMPANY contact record.
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        return $this->CompanyData->getDefaultRecord();
    }

    /**
     * Validate the given company record
     *
     * @param array $company_data the company record to validate
     * @param array $contact_data the complete contact record
     * @param array $option for the validation
     * @throws ContactException
     * @return boolean
     */
    public function validate(&$company_data, $contact_data=array(), $option=array())
    {

        // the company_id must be always set!
        if (!isset($company_data['company_id'])) {
            $this->setMessage("Missing the %identifier%! The ID should be set to -1 if you insert a new record.",
                array('%identifier%' => 'company_id'));
            return false;
        }

        // check if any value is NULL
        foreach ($company_data as $key => $value) {
            if (is_null($value)) {
                switch ($key) {
                    case 'contact_id':
                    case 'primary_address_id':
                    case 'primary_person_id':
                    case 'primary_phone_id':
                    case 'primary_email_id':
                    case 'primary_note_id':
                        $company_data[$key] = -1;
                        break;
                    case 'company_name':
                    case 'company_department':
                    case 'company_additional':
                    case 'company_additional_2':
                    case 'company_additional_3':
                        $company_data[$key] = '';
                        break;
                    case 'company_status':
                        $company_data[$key] = 'ACTIVE';
                        break;
                    default:
                        throw new ContactException("The key $key is not defined!");
                        break;
                }
            }
        }
        return true;
    }
}
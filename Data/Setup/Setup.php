<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Data\Setup;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Address;
use phpManufaktur\Contact\Data\Contact\Communication;
use phpManufaktur\Contact\Data\Contact\Company;
use phpManufaktur\Contact\Data\Contact\Contact;
use phpManufaktur\Contact\Data\Contact\Person;
use phpManufaktur\Contact\Data\Contact\Title;
use phpManufaktur\Contact\Data\Contact\Country;
use phpManufaktur\Contact\Data\Contact\CommunicationType;
use phpManufaktur\Contact\Data\Contact\CommunicationUsage;
use phpManufaktur\Contact\Data\Contact\AddressType;
use phpManufaktur\Contact\Data\Contact\Note;
use phpManufaktur\Contact\Data\Contact\Overview;

class Setup
{

    protected $app = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function exec()
    {
        try {
            $Address = new Address($this->app);
            $Address->createTable();

            $AddressType = new AddressType($this->app);
            $AddressType->createTable();
            $AddressType->initAddressTypeList();

            $Communication = new Communication($this->app);
            $Communication->createTable();

            $CommunicationType = new CommunicationType($this->app);
            $CommunicationType->createTable();
            $CommunicationType->initCommunicationTypeList();

            $CommunicationUsage = new CommunicationUsage($this->app);
            $CommunicationUsage->createTable();
            $CommunicationUsage->initCommunicationUsageList();

            $Company = new Company($this->app);
            $Company->createTable();

            $Contact = new Contact($this->app);
            $Contact->createTable();

            $Person = new Person($this->app);
            $Person->createTable();

            $Title = new Title($this->app);
            $Title->createTable();
            $Title->initTitleList();

            $Country = new Country($this->app);
            $Country->createTable();
            $Country->initCountryList();

            $Note = new Note($this->app);
            $Note->createTable();

            $Overview = new Overview($this->app);
            $Overview->createTable();

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
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
use phpManufaktur\Contact\Data\Address;
use phpManufaktur\Contact\Data\Communication;
use phpManufaktur\Contact\Data\Company;
use phpManufaktur\Contact\Data\Contact;
use phpManufaktur\Contact\Data\Network;
use phpManufaktur\Contact\Data\Person;
use phpManufaktur\Contact\Data\Title;
use phpManufaktur\Contact\Data\Country;
use phpManufaktur\Contact\Data\CommunicationType;
use phpManufaktur\Contact\Data\CommunicationUsage;

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

            $Network = new Network($this->app);
            $Network->createTable();

            $Person = new Person($this->app);
            $Person->createTable();

            $Title = new Title($this->app);
            $Title->createTable();
            $Title->initTitleList();

            $Country = new Country($this->app);
            $Country->createTable();
            $Country->initCountryList();
        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}
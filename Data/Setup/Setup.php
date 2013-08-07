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
use phpManufaktur\Contact\Data\Contact\CategoryType;
use phpManufaktur\Contact\Data\Contact\Category;
use phpManufaktur\Contact\Data\Contact\TagType;
use phpManufaktur\Contact\Data\Contact\Tag;

class Setup
{

    protected $app = null;

    public function exec(Application $app)
    {
        try {
            $this->app = $app;

            $Contact = new Contact($this->app);
            $Contact->createTable();

            $CommunicationType = new CommunicationType($this->app);
            $CommunicationType->createTable();
            $CommunicationType->initCommunicationTypeList();

            $CommunicationUsage = new CommunicationUsage($this->app);
            $CommunicationUsage->createTable();
            $CommunicationUsage->initCommunicationUsageList();

            $Communication = new Communication($this->app);
            $Communication->createTable();

            $Country = new Country($this->app);
            $Country->createTable();
            $Country->initCountryList();

            $AddressType = new AddressType($this->app);
            $AddressType->createTable();
            $AddressType->initAddressTypeList();

            $Address = new Address($this->app);
            $Address->createTable();

            $Title = new Title($this->app);
            $Title->createTable();
            $Title->initTitleList();

            $Note = new Note($this->app);
            $Note->createTable();

            $Person = new Person($this->app);
            $Person->createTable();

            $Company = new Company($this->app);
            $Company->createTable();

            $Overview = new Overview($this->app);
            $Overview->createTable();

            $CategoryType = new CategoryType($this->app);
            $CategoryType->createTable();
            $CategoryType->initCategoryTypeList();

            $Category = new Category($this->app);
            $Category->createTable();

            $TagType = new TagType($this->app);
            $TagType->createTable();

            $Tag = new Tag($this->app);
            $Tag->createTable();

            return "The setup was successfull!";

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}

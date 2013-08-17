<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Data\Uninstall;

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

class Uninstall
{

    protected $app = null;

    public function exec(Application $app)
    {
        try {
            $this->app = $app;

            $Communication = new Communication($this->app);
            $Communication->dropTable();

            $CommunicationType = new CommunicationType($this->app);
            $CommunicationType->dropTable();

            $CommunicationUsage = new CommunicationUsage($this->app);
            $CommunicationUsage->dropTable();

            $Contact = new Contact($this->app);
            $Contact->dropTable();

            $Country = new Country($this->app);
            $Country->dropTable();

            $AddressType = new AddressType($this->app);
            $AddressType->dropTable();

            $Address = new Address($this->app);
            $Address->dropTable();

            $Title = new Title($this->app);
            $Title->dropTable();

            $Person = new Person($this->app);
            $Person->dropTable();

            $Company = new Company($this->app);
            $Company->dropTable();

            $Note = new Note($this->app);
            $Note->dropTable();

            $Overview = new Overview($this->app);
            $Overview->dropTable();

            $CategoryType = new CategoryType($this->app);
            $CategoryType->dropTable();

            $Category = new Category($this->app);
            $Category->dropTable();

            $TagType = new TagType($this->app);
            $TagType->dropTable();

            $Tag = new Tag($this->app);
            $Tag->dropTable();

            return "The uninstall process was successfull!";

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}

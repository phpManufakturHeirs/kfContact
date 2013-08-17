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
use phpManufaktur\Contact\Data\Contact\Protocol;

class Uninstall
{

    public function exec(Application $app)
    {
        try {
            $Communication = new Communication($app);
            $Communication->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_communication`');

            $CommunicationType = new CommunicationType($app);
            $CommunicationType->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_communication_type`');

            $CommunicationUsage = new CommunicationUsage($app);
            $CommunicationUsage->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_communication_usage`');

            $Contact = new Contact($app);
            $Contact->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_contact`');

            $Country = new Country($app);
            $Country->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_country`');

            $AddressType = new AddressType($app);
            $AddressType->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_address_type`');

            $Address = new Address($app);
            $Address->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_address`');

            $Title = new Title($app);
            $Title->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_title`');

            $Person = new Person($app);
            $Person->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_person`');

            $Company = new Company($app);
            $Company->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_company`');

            $Note = new Note($app);
            $Note->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_note`');

            $Overview = new Overview($app);
            $Overview->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_overview`');

            $CategoryType = new CategoryType($app);
            $CategoryType->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_category_type`');

            $Category = new Category($app);
            $Category->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_category`');

            $TagType = new TagType($app);
            $TagType->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_tag_type`');

            $Tag = new Tag($app);
            $Tag->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_tag`');

            $Protocol = new Protocol($app);
            $Protocol->dropTable();
            $app['monolog']->addInfo('[Contact Uninstall] Drop table `contact_protocol`');

            $app['monolog']->addInfo('[Contact Uninstall] Dropped all tables successfull');
            return "The uninstall process was successfull!";

        } catch (\Exception $e) {
            throw new \Exception($e);
        }
    }
}

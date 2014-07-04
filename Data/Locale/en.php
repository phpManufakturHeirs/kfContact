<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/contact
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

if ('á' != "\xc3\xa1") {
    // the language files must be saved as UTF-8 (without BOM)
    throw new \Exception('The language file ' . __FILE__ . ' is damaged, it must be saved UTF-8 encoded!');
}

return array(

    'Address city'
        => 'City',
    'Address street'
        => 'Street',
    'Address zip'
        => 'ZIP',

    'Category type access'
        => 'Category access',
    'Communication email'
        => 'Email',
    'contact_id'
        => 'Contact ID',

    'FEMALE'
        => 'Female',

    'MALE'
        => 'Male',

    'Person first name'
        => 'First name',
    'Person gender'
        => 'Gender',
    'Person last name'
        => 'Last name',
    'Person nick name'
        => 'Nickname',

);

<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

use phpManufaktur\Contact\Data\Setup\Setup;
use phpManufaktur\Contact\Control\Contact;
use phpManufaktur\Contact\Control\Dialog\SimplePersonContact;

// scan the /Locale directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Contact/Data/Locale');

$app->get('/admin/contact/setup', function() use($app) {
    $Setup = new Setup($app);
    $Setup->exec();
    return "Success!";
});

$app->get('/admin/contact/test', function() use($app) {
    $contact = new Contact($app);
    print_r($contact->getContactRecord());

    return "<p>OK</p>";
});

$app->match('/admin/contact/simple/contact', function() use($app) {
    $contact = new SimplePersonContact($app);
    return $contact->exec();
});
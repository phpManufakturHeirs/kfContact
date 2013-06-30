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
use phpManufaktur\Contact\Control\Dialog\Simple\SimpleContact;
use phpManufaktur\Contact\Control\Dialog\Simple\SimpleList;

// scan the /Locale directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Contact/Data/Locale');

$app->get('/admin/contact/setup', function() use($app) {
    $Setup = new Setup($app);
    $Setup->exec();
    return "Success!";
});

$app->match('/admin/contact/simple/contact', function() use($app) {
    $contact = new SimpleContact($app);
    return $contact->exec();
});

$app->match('/admin/contact/simple/contact/{contact_id}', function($contact_id) use($app) {
    $contact = new SimpleContact($app);
    $contact->setContactID($contact_id);
    return $contact->exec();
});

$app->match('/admin/contact/simple/list', function() use ($app) {
    $list = new SimpleList($app);
    return $list->exec();
});

$app->match('/admin/contact/test', function() use($app) {
    $SQL = "SELECT * FROM `".FRAMEWORK_TABLE_PREFIX."contact_person` WHERE ".
        "`contact_id`='5'";

    $SQL = "SELECT `person_primary_email_id` FROM `".FRAMEWORK_TABLE_PREFIX."contact_person` WHERE ".
        "`contact_id`='5' AND `person_primary_email_id`='7' AND `person_status`!='DELETED'";

    $check = $app['db']->fetchAssoc($SQL);

    print_r($check);
    return true;
});

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
use phpManufaktur\Contact\Control\Dialog\Simple\Contact as SimpleContact;
use phpManufaktur\Contact\Control\Dialog\Simple\ContactList as SimpleContactList;
use phpManufaktur\Contact\Control\Dialog\Simple\TagType as SimpleTagType;

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
    $list = new SimpleContactList($app);
    return $list->exec();
});

$app->match('/admin/contact/simple/list/page/{page}', function($page) use ($app) {
    $list = new SimpleContactList($app);
    $list->setCurrentPage($page);
    return $list->exec();
});

$app->match('/admin/contact/simple/tag/type', function() use($app) {
    $TagType = new SimpleTagType($app);
    return $TagType->exec();
});

$app->match('/admin/contact/simple/tag/type/{type_id}', function($type_id) use($app) {
    $TagType = new SimpleTagType($app);
    $TagType->setTagTypeID($type_id);
    return $TagType->exec();
});

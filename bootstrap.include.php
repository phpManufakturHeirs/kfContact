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
use phpManufaktur\Contact\Control\Dialog\Simple\TagEdit as SimpleTagEdit;
use phpManufaktur\Contact\Control\Dialog\Simple\TagList as SimpleTagList;
use phpManufaktur\Contact\Control\Dialog\Simple\CategoryList as SimpleCategoryList;
use phpManufaktur\Contact\Control\Dialog\Simple\CategoryEdit as SimpleCategoryEdit;
use phpManufaktur\Contact\Control\Dialog\Simple\TitleList as SimpleTitleList;
use phpManufaktur\Contact\Control\Dialog\Simple\TitleEdit as SimpleTitleEdit;

// scan the /Locale directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Contact/Data/Locale');

// scan the /Locale/Custom directory and add all available languages
$app['utils']->addLanguageFiles(MANUFAKTUR_PATH.'/Contact/Data/Locale/Custom');

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

$app->match('/admin/contact/simple/tag/edit', function() use($app) {
    $TagEdit = new SimpleTagEdit($app);
    return $TagEdit->exec();
});

$app->match('/admin/contact/simple/tag/edit/id/{tag_id}', function($tag_id) use($app) {
    $TagEdit = new SimpleTagEdit($app);
    $TagEdit->setTagTypeID($tag_id);
    return $TagEdit->exec();
});

$app->match('/admin/contact/simple/tag/list', function() use($app) {
    $TagList = new SimpleTagList($app);
    return $TagList->exec();
});

$app->match('/admin/contact/simple/category/list', function() use($app) {
    $CategoryList = new SimpleCategoryList($app);
    return $CategoryList->exec();
});

$app->match('/admin/contact/simple/category/edit', function() use($app) {
    $CategoryEdit = new SimpleCategoryEdit($app);
    return $CategoryEdit->exec();
});

$app->match('/admin/contact/simple/category/edit/id/{category_id}', function($category_id) use($app) {
    $CategoryEdit = new SimpleCategoryEdit($app);
    $CategoryEdit->setCategoryID($category_id);
    return $CategoryEdit->exec();
});

$app->match('/admin/contact/simple/title/list', function() use($app) {
    $TitleList = new SimpleTitleList($app);
    return $TitleList->exec();
});

$app->match('/admin/contact/simple/title/edit', function() use($app) {
    $TitleEdit = new SimpleTitleEdit($app);
    return $TitleEdit->exec();
});

$app->match('/admin/contact/simple/title/edit/id/{title_id}', function($title_id) use($app) {
    $TitleEdit = new SimpleTitleEdit($app);
    $TitleEdit->setTitleID($title_id);
    return $TitleEdit->exec();
});



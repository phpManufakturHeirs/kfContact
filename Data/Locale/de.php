<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

if ('á' != "\xc3\xa1") {
    // the language files must be saved as UTF-8 (without BOM)
    throw new \Exception('The language file ' . __FILE__ . ' is damaged, it must be saved UTF-8 encoded!');
}

return array(
    'baron'
        => 'Baron',
    'Country'
        => 'Land',
    'doc'
        => 'Dr.',
    'earl'
        => 'Graf',
    'female'
        => 'Frau',
    'gender'
        => 'Geschlecht',
    'male'
        => 'Mann',
    'Nick name'
        => 'Spitzname',
    'prof'
        => 'Prof.',
    'The contact record must contain a email address or a login name as unique identifier!'
        => 'Der Kontakt Datensatz muss eine E-Mail Adresse oder einen Anmeldenamen (Login) für eine eindeutige Identifizierung enthalten!',
    'The form is not valid, please check your input and try again!'
        => 'Das Formular ist nicht gültig, bitte überprüfen Sie Ihre Eingabe und übermitteln Sie das Formular erneut!',
    'The last name must be at least two characters long!'
        => 'Der Nachname muss aus mindestens zwei Buchstaben bestehen!',
    'The email address %email% is not valid, please check your input!'
        => 'Die E-Mail Adresse %email% ist nicht gültig, bitte überprüfen Sie Ihre Eingabe!',
    'Title'
        => 'Titel',
    'Zip'
        => 'Postleitzahl'
);
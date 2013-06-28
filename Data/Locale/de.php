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
    "At minimum you must specify a street, a city or a zip code for a valid address"
        => "Für eine gültige Adresse müssen Sie mindestens eine Straße, eine Stadt oder eine Postleitzahl angeben.",
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
    'Gender'
        => 'Geschlecht',
    "Inserted the new contact with the ID %contact_id%."
        => 'Es wurde ein neuer Kontakt mit der ID %contact_id% hinzugefügt',
    'male'
        => 'Mann',
    "Missing the %identifier%! The ID should be set to -1 if you insert a new record."
        => 'Das Feld <b>%identifier%</b> fehlt! Diese ID sollte auf -1 gesetzt sein, wenn Sie einen neuen Datensatz einfügen möchten.',
    'Nick name'
        => 'Spitzname',
    'Note'
        => 'Notiz',
    'prof'
        => 'Prof.',
    "The Address with the ID %address_id% was successfull deleted."
        => 'Die Adresse mit der ID %address_id% wurde erfolgreich gelöscht.',
    "The communication entry %communication% was successfull deleted."
        => 'Der Kommunikationseintrag <b>%communication%</b> wurde gelöscht.',
    "The COMMUNICATION TYPE %type% does not exists!"
        => 'Der Kommunikationstyp <b>%type%</b> existiert nicht, bitte prüfen Sie Ihre Eingabe!',
    "The COMMUNICATION TYPE must be set!"
        => 'Das Feld <b>communication type</b> muss gesetzt sein!',
    "The COMMUNICATION USAGE must be set!"
        => 'Das Feld <b>communication usage</b> muss gesetzt sein!',
    "The COMMUNICATION USAGE %usage% does not exists!"
        => 'Die Kommunikationsverwendung <b>%usage%</b> existiert nicht, bitte prüfen Sie Ihre Eingabe!',
    "The COMMUNICATION VALUE should not be empty!"
        => 'Der Kommunikationswert darf nicht leer oder Null sein!',
    "The contact login must be set!"
        => 'Der Kontakt <b>Login</b> muss gesetzt sein!',
    'The contact record must contain a email address or a login name as unique identifier!'
        => 'Der Kontakt Datensatz muss eine E-Mail Adresse oder einen Anmeldenamen (Login) für eine eindeutige Identifizierung enthalten!',
    "The contact record was not changed!"
        => 'Der Kontakt Datensatz wurde nicht geändert.',
    "The contact with the ID %contact_id% does not exists!"
        => 'Es existiert kein Kontakt Datensatz mit der ID %contact_id%!',
    "The contact with the ID %contact_id% was successfull updated."
        => 'Der Kontakt mit der ID %contact_id% wurde erfolgreich aktualisiert.',
    'The country code %country_code% does not exists!'
        => 'Der Ländercode <b>%country_code%</b> existiert nicht!',
    'The email address %email% is not valid, please check your input!'
        => 'Die E-Mail Adresse %email% ist nicht gültig, bitte überprüfen Sie Ihre Eingabe!',
    'The form is not valid, please check your input and try again!'
        => 'Das Formular ist nicht gültig, bitte überprüfen Sie Ihre Eingabe und übermitteln Sie das Formular erneut!',
    'The last name must be at least two characters long!'
        => 'Der Nachname muss aus mindestens zwei Buchstaben bestehen!',
    'The login <b>%login%</b> is already in use, please choose another one!'
        => "Der Login <b>%login%</b> wird bereits verwendet, bitte legen Sie einen anderen Login fest!",
    "The %type% entry %value% is marked for primary communication and can not removed!"
        => 'Der Typ %type% mit dem Wert %value% ist für die primäre Kommunikation mit dem Kontakt festgelegt und kann nicht gelöscht werden!',
    "The update returned 'FALSE' but no message ..."
        => "Die Aktualisierungsfunktion hat den Datensatz <b>nicht</b> aktualisiert und keinen Grund dafür mitgeteilt.",
    'The zip %zip% is not valid!'
        => 'Die Postleitzahl <b>%zip%</b> ist nicht gültig, bitte prüfen Sie Ihre Eingabe!',
    'Title'
        => 'Titel',
    'Zip'
        => 'Postleitzahl'
);
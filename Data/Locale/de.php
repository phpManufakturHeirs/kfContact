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
    '- new category -'
        => '- neue Kategorie -',
    '- new contact -'
        => '- neuer Kontakt -',
    '- new tag -'
        => '- neue Markierung -',
    'active'
        => 'Aktiv',
    'Add a new category'
        => 'Eine neue Kategorie hinzufügen',
    'Add a new tag'
        => 'Eine neue Markierung hinzufügen',
    'Add a new title'
        => 'Einen neuen Titel hinzufügen',
    'Additional'
        => 'Zusatz',
    'Address'
        => 'Adresse',
    'address_city'
        => 'Stadt',
    'address_country_code'
        => 'Land',
    'address_street'
        => 'Straße',
    'address_zip'
        => 'PLZ',
    'Allowed characters for the %identifier% identifier are only A-Z, 0-9 and the Underscore. The identifier will be always converted to uppercase.'
        => 'Erlaubte Zeichen für den %identifier% Bezeichner sind A-Z, 0-9 und der Unterstrich. Der Bezeichner wird stets in Großbuchstaben umgewandelt.',
    "At minimum you must specify a street, a city or a zip code for a valid address"
        => "Für eine gültige Adresse müssen Sie mindestens eine Straße, eine Stadt oder eine Postleitzahl angeben.",

    'baron'
        => 'Baron',
    'Birthday'
        => 'Geburtstag',
    'Business address'
        => 'Geschäftsadresse',

    "Can't delete the Adress with the ID %address_id% because it is used as primary address."
        => "Die Adresse mit der ID %address_id% kann nicht gelöscht werden, da sie als primäre Adresse für diesen Kontakt verwendet wird.",
    "Can't delete the Note with the ID %note_id% because it is used as primary note for this contact."
        => "Die Notiz mit der ID %note_id% kann nicht gelöscht werden, da sie als primäre Information für den Kontakt verwendet wird.",
    "Can't read the contact with the ID %contact_id% - it is possibly deleted."
        => 'Der Kontakt Datensatz mit der ID %contact_id% konnte nicht gelesen werden, er wurde möglicher Weise gelöscht.',
    'category_type_id'
        => 'Kategorie ID',
    'Category'
        => 'Kategorie',
    'Category description'
        => 'Kategorie Beschreibung',
    'Category description (translated)'
        => 'Kategorie Beschreibung (Übersetzung)',
    'Category name'
        => 'Kategorie Bezeichner',
    'Category name (translated)'
        => 'Kategorie Bezeichner (Übersetzung)',
    'Category type list'
        => 'Übersicht über die Kategorien',
    'Change to the overview'
        => 'Zur Übersicht wechseln',
    'Cell'
        => 'Mobilfunk',
    'City'
        => 'Stadt',
    'Communication'
        => 'Kommunikation',
    'communication_email'
        => 'E-Mail',
    'communication_phone'
        => 'Telefon',
    'Company'
        => 'Firma',
    'COMPANY'
        => 'Firma',
    'Company department'
        => 'Abteilung',
    'company_department'
        => 'Abteilung',
    'Company name'
        => 'Firma',
    'company_name'
        => 'Firma',
    'contact_id'
        => 'ID',
    'Contact'
        => 'Kontakt',
    'Contact name'
        => 'Kontakt Bezeichner',
    'contact_name'
        => 'Bezeichner',
    'Contact login'
        => 'Kontakt Anmeldename',
    'contact_login'
        => 'Login',
    'contact_type'
        =>'Typ',
    "Contact insert fail, but the process does not return the reason!"
        => "Der Datensatz konnte nicht eingefügt werden, der Prozess hat leider keinen Grund dafür mitgeteilt.",
    'Country'
        => 'Land',
    'Create a new category'
        => 'Eine neue Kategorie erstellen',
    'Create a new Tag <i>#tag</i>'
        => 'Eine neue Markierung (#tag) erstellen',
    'Create a new title'
        => 'Einen neuen Titel erstellen',
    'Customer'
        => 'Kunde',

    'Delete Tag'
        => 'Markierung (#tag) löschen',
    'delete the category <b>%category_name%</b>'
        => 'die Kategorie <b>%category_name%</b> löschen',
    'delete the #tag <b>%tag_name%</b>'
        => 'den #tag <b>%tag_name%</b> löschen',
    'delete the title <b>%title_identifier%</b>'
        => 'den Titel <b>%title_identifier%</b> löschen',
    'deleted'
        => 'Gelöscht',
    'Delivery address'
        => 'Lieferadresse',
    'Description'
        => 'Beschreibung',
    'Description (translated)'
        => 'Beschreibung (Übersetzung)',
    'doc'
        => 'Dr.',

    'earl'
        => 'Graf',
    'Edit categories'
        => 'Kategorien bearbeiten',
    'Edit the category %category_name%'
        => 'Die Kategorie %category_name% bearbeiten',
    'Edit the <i>#tag</i> %tag_name%'
        => 'Die Markierung #tag <i>%tag_name%</i> bearbeiten',
    'Edit the title %title_identifier%'
        => 'Den Titel %title_identifier% bearbeiten',
    'Edit titles'
        => 'Titel bearbeiten',

    'FEMALE'
        => 'Frau',
    'female'
        => 'Frau',
    'First name'
        => 'Vorname',

    'Gender'
        => 'Geschlecht',

    'Identifier'
        => 'Bezeichner',
    "Inserted the new contact with the ID %contact_id%."
        => 'Es wurde ein neuer Kontakt mit der ID %contact_id% hinzugefügt',
    'Intern'
        => 'Intern',

    'Last name'
        => 'Nachname',
    'locked'
        => 'Gesperrt',
    'Long name'
        => 'Langbezeichnung',
    'Long name (translated)'
        => 'Langbezeichnung (Übersetzung)',

    'MALE'
        => 'Herr',
    'male'
        => 'Herr',
    'Merchant'
        => 'Händler',
    "Missing the %identifier%! The ID should be set to -1 if you insert a new record."
        => 'Das Feld <b>%identifier%</b> fehlt! Diese ID sollte auf -1 gesetzt sein, wenn Sie einen neuen Datensatz einfügen möchten.',
    'Missing the key %field_name%, it must always set and not empty!'
        => 'Der Schlüssel %field_name% muss immer gesetzt werden und darf nicht leer sein!',

    'Nick name'
        => 'Spitzname',
    'Note'
        => 'Notiz',

    'Options'
        => 'Optionen',

    'PERSON'
        => 'Person',
    'person_birthday'
        => 'Geburtstag',
    'person_first_name'
        => 'Vorname',
    'person_last_name'
        => 'Nachname',
    'person_nick_name'
        => 'Spitzname',
    'Person title'
        => 'Titel',
    'Phone'
        => 'Telefon',
    'Please define a short name for the title!'
        => 'Bitte legen Sie eine Kurzbezeichnung für den Titel fest!',
    'Please select the contact type you want to create.'
        => 'Bitte wählen Sie den Kontakt Typ aus, den Sie erstellen möchten.',
    'prof'
        => 'Prof.',

    'Select contact type'
        => 'Kontakt Typ',
    'Short name'
        => 'Kurzbezeichnung',
    'Short name (translated)'
        => 'Kurzbezeichnung (Übersetzung)',
    'Street'
        => 'Straße',

    'Tag'
        => 'Markierung',
    'Tag (translated)'
        => 'Markierung (Übersetzung)',
    'Tag list'
        => 'Markierungen (#tags) Übersicht',
    'Tags'
        => 'Markierungen',
    "The Address with the ID %address_id% was successfull deleted."
        => 'Die Adresse mit der ID %address_id% wurde erfolgreich gelöscht.',
    'The category %category_name% was successfull deleted.'
        => 'Die Kategorie %category_name% wurde erfolgreich gelöscht.',
    'The category %category_name% was successfull inserted.'
        => 'Die Kategorie %category_name% wurde erfolgreich eingefügt.',
    'The category %category_name% was successfull updated'
        => 'Die Kategorie %category_name% wurde erfolgreich aktualisiert.',
    'The category type with the ID %category_id% does not exists!'
        => 'Die Kategorie mit der ID %category_id% existiert nicht!',
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
    'The contact list is empty.'
        => 'Die Kontaktliste enthält keine Einträge!',
    "The contact login must be set!"
        => 'Der Kontakt <b>Login</b> muss gesetzt sein!',
    "The contact name %name% already exists! The update has still executed, please check if you really want this duplicate name."
        => "Der Kontakt Name <b>%name%</b> wird bereits verwendet! Der Datensatz wurde trotzdem aktualisiert, bitte prüfen Sie ob sie den doppelten Eintrag beibehalten möchten.",
    'The contact record must contain a email address or a login name as unique identifier!'
        => 'Der Kontakt Datensatz muss eine E-Mail Adresse oder einen Anmeldenamen (Login) für eine eindeutige Identifizierung enthalten!',
    "The contact record was not changed!"
        => 'Der Kontakt Datensatz wurde nicht geändert.',
    "The contact_type must be always set (%contact_types%)."
        => 'Der Kontakt Typ muss immer gesetzt sein, mögliche Werte: %contact_types%.',
    "The contact with the ID %contact_id% does not exists!"
        => 'Es existiert kein Kontakt Datensatz mit der ID %contact_id%!',
    "The contact with the ID %contact_id% was successfull updated."
        => 'Der Kontakt mit der ID %contact_id% wurde erfolgreich aktualisiert.',
    'The country code %country_code% does not exists!'
        => 'Der Ländercode <b>%country_code%</b> existiert nicht!',
    'The email address %email% is not valid, please check your input!'
        => 'Die E-Mail Adresse %email% ist nicht gültig, bitte überprüfen Sie Ihre Eingabe!',
    "The %entry% entry with the ID %id% was not processed, there exists no fitting record for comparison!"
        => "Der Eintrag %entry% mit der ID %id% wurde nicht aktualisiert, es wurde kein passender Eintrag in der Tabelle gefunden!",
    "The field %field% can not be empty!"
        => 'Das Feld %field% darf nicht leer sein!',
    'The form is not valid, please check your input and try again!'
        => 'Das Formular ist nicht gültig, bitte überprüfen Sie Ihre Eingabe und übermitteln Sie das Formular erneut!',
    'The last name must be at least two characters long!'
        => 'Der Nachname muss aus mindestens zwei Buchstaben bestehen!',
    'The login <b>%login%</b> is already in use, please choose another one!'
        => "Der Login <b>%login%</b> wird bereits verwendet, bitte legen Sie einen anderen Login fest!",
    "The login_name or a email address must be always set, can't insert the record!"
        => 'Das Feld login_name oder eine E-Mail Adresse müssen immer gesetzt sein, kann den neuen Datensatz nicht einfügen!',
    "The process has not returned a status message"
        => 'Der Prozess hat keinen Status für den Vorgang gemeldet',
    'The record with the ID %id% was successfull inserted.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich eingefügt.',
    'The record with the ID %id% was successfull updated.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich aktualisiert.',
    'The record with the ID %id% was successfull deleted.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich gelöscht.',
    'The tag type %tag_name% already exists!'
        => 'Die Markierung %tag_name% existiert bereits und kann nicht erneut eingefügt werden!',
    "The %type% entry %value% is marked for primary communication and can not removed!"
        => 'Der Typ %type% mit dem Wert %value% ist für die primäre Kommunikation mit dem Kontakt festgelegt und kann nicht gelöscht werden!',
    "The update returned 'FALSE' but no message ..."
        => "Die Aktualisierungsfunktion hat den Datensatz <b>nicht</b> aktualisiert und keinen Grund dafür mitgeteilt.",
    'The zip %zip% is not valid!'
        => 'Die Postleitzahl <b>%zip%</b> ist nicht gültig, bitte prüfen Sie Ihre Eingabe!',
    'Title'
        => 'Titel',
    'title_id'
        => 'ID',
    'Title list'
        => 'Titel Übersicht',

    'Unchecked'
        => '- ungeprüft -',

    'Zip'
        => 'Postleitzahl'
);
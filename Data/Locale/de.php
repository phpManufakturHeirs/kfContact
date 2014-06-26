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
    '- new category -'
        => '- neue Kategorie -',
    '- new contact -'
        => '- neuer Kontakt -',
    '- new tag -'
        => '- neue Markierung -',
    '- no contact selected -'
        => '- kein Kontakt ausgewählt -',
    'active'
        => 'Aktiv',
    'Active'
        => 'Aktiv',
    'Add a new category'
        => 'Eine neue Kategorie erstellen',
    'Add a new contact'
        => 'Einen neuen Kontakt erstellen',
    'Add a new extra field'
        => 'Ein neues Zusatzfeld erstellen',
    'Add a new tag'
        => 'Eine neue Markierung (#tag) erstellen',
    'Add a new title'
        => 'Einen neuen Titel hinzufügen',
    'Additional'
        => 'Zusatz',
    'Address'
        => 'Adresse',
    'Address area'
        => 'Bezirk',
    'Address city'
        => 'Stadt',
    'Address country code'
        => 'Land',
    'Address state'
        => 'Bundesland',
    'Address street'
        => 'Straße',
    'Address zip'
        => 'PLZ',
    'address_city'
        => 'Stadt',
    'address_country_code'
        => 'Land',
    'address_street'
        => 'Straße',
    'address_zip'
        => 'PLZ',
    'ADMIN'
        => 'Administrator',
    'Allowed characters for the %identifier% identifier are only A-Z, 0-9 and the Underscore. The identifier will be always converted to uppercase.'
        => 'Erlaubte Zeichen für den %identifier% Bezeichner sind A-Z, 0-9 und der Unterstrich. Der Bezeichner wird stets in Großbuchstaben umgewandelt.',
    'Area'
        => 'Bezirk, Region',
    'AT'
        => 'Österreich',
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
    'Categories'
        => 'Kategorien',
    'Category'
        => 'Kategorie',
    'Category access'
        => 'Kategorie Zugriff',
    'Category description'
        => 'Kategorie Beschreibung',
    'Category description (translated)'
        => 'Kategorie Beschreibung (Übersetzung)',
    'Category name'
        => 'Kategorie',
    'Category name (translated)'
        => 'Kategorie Bezeichner (Übersetzung)',
    'Category type access'
        => 'Kategorie Zugriff',
    'Category type id'
        => 'Kategorie',
    'Category type list'
        => 'Übersicht über die Kategorien',
    'Change to the overview'
        => 'Zur Übersicht wechseln',
    'Cell'
        => 'Mobilfunk',
    'CH'
        => 'Schweiz',
    'City'
        => 'Stadt',
    'Click to sort column ascending'
        => 'Anklicken um die Spalte aufsteigend zu sortieren',
    'Click to sort column descending'
        => 'Anklicken um die Spalte absteigend zu sortieren',
    'Communication'
        => 'Kommunikation',
    'Communication cell'
        => 'Mobil',
    'Communication email'
        => 'E-Mail',
    'communication_email'
        => 'E-Mail',
    'Communication fax'
        => 'Telefax',
    'Communication phone'
        => 'Telefon',
    'communication_phone'
        => 'Telefon',
    'Communication url'
        => 'URL',
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
    'Contact id'
        => 'ID',
    'Contact name'
        => 'Kontakt Bezeichner',
    'contact_name'
        => 'Bezeichner',
    'Contact login'
        => 'Kontakt Anmeldename',
    'Contact pending'
        => 'Kontaktdaten werden geprüft',
    'Contact published'
        => 'Kontaktdaten veröffentlicht',
    'contact_login'
        => 'Login',
    'Contact since'
        => 'Kontakt seit',
    'Contact status'
        => 'Status',
    'Contact timestamp'
        => 'Letzte Änderung',
    'contact_type'
        =>'Typ',
    'Contact data submitted'
        => 'Kontaktdaten übermittelt',
    "Contact insert fail, but the process does not return the reason!"
        => "Der Datensatz konnte nicht eingefügt werden, der Prozess hat leider keinen Grund dafür mitgeteilt.",
    'Contact Type: %type%'
        => 'Kontakt Typ: %type%',
    '%count% hits for the search term </i>%search%</i>.'
        => '%count% Treffer für den Suchbegriff <i>%search%</i>.',
    'Country'
        => 'Land',
    'Create a new category'
        => 'Eine neue Kategorie erstellen',
    'Create a new Tag <i>#tag</i>'
        => 'Eine neue Markierung (#tag) erstellen',
    'Create a new title'
        => 'Einen neuen Titel erstellen',
    'Create contact'
        => 'Kontakt anlegen',
    'Customer'
        => 'Kunde',
    'Customer relationship management for the kitFramework'
        => 'Kontakt- und Adressverwaltung (CRM) für das kitFramework',

    'DE'
        => 'Deutschland',
    'Delete Tag'
        => 'Markierung (#tag) löschen',
    'delete the category <b>%category_type_name%</b>'
        => 'die Kategorie <b>%category_type_name%</b> löschen',
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
    'Detected a KeepInTouch installation (Release: %release%) with %count% active or locked contacts.'
        => 'Es wurde eine KeepInTouch Installation (Release: %release%) mit %count% aktiven oder gesperrten Kontakten gefunden.',
    'Determine contact type'
        => 'Kontakt Typ festlegen',
    'doc'
        => 'Dr.',

    'earl'
        => 'Graf',
    'Edit categories'
        => 'Kategorien bearbeiten',
    'Edit category'
        => 'Kategorie bearbeiten',
    'Edit contact'
        => 'Kontakt bearbeiten',
    'Edit extra fields'
        => 'Zusatzfelder bearbeiten',
    'Edit extra field'
        => 'Zusatzfeld bearbeiten',
    'Edit tags'
        => 'Markierungen bearbeiten',
    'Edit tag'
        => 'Markierung bearbeiten',
    'Edit the category %category_type_name%'
        => 'Die Kategorie %category_type_name% bearbeiten',
    'Edit the <i>#tag</i> %tag_name%'
        => 'Die Markierung #tag <i>%tag_name%</i> bearbeiten',
    'Edit the title %title_identifier%'
        => 'Den Titel %title_identifier% bearbeiten',
    'Edit title'
        => 'Titel bearbeiten',
    'Edit titles'
        => 'Titel bearbeiten',

    'Failed to send a email with the subject <b>%subject%</b> to the addresses: <b>%failed%</b>.'
        => 'Eine E-Mail mit dem Betreff <b>%subject%</b> konnte an die folgenden Adressaten nicht übermittelt werden: <b>%failed%</b>.',
    'Female'
        => 'Frau',
    'Fields of type `select`, `radio` or `checkbox` need one or more values defined as array in `choices`!'
        => 'Felder vom Typ `select`, `radio` oder `checkbox` benötigen einen oder mehrere Werte, die als Array in `choices` übergeben werden!',
    'First name'
        => 'Vorname',
    'Forms'
        => 'Formulare',
    'FR'
        => 'Frankreich',

    'Gender'
        => 'Geschlecht',

    "I'm a sample header"
        => 'Ich bin ein Beispiel für eine Überschrift',
    'I accept that this software is provided under <a href="http://opensource.org/licenses/MIT" target="_blank">MIT License</a>.'
        => 'Ich akzeptiere, dass diese Software unter der <a href="http://opensource.org/licenses/MIT" target="_blank">MIT Lizenz</a> veröffentlicht wurde.',
    'Identifier'
        => 'Bezeichner',
    'If you are the owner of the contact record you can change or update the data, please login. If you have never got any account information please select "Forgot password?"'
        => 'Falls Sie der Inhaber des Kontaktdatensatz sind können Sie die Daten jederzeit aktualisieren oder ändern, bitte melden Sie sich an. Falls Sie bisher keine Zugangsdaten erhalten haben, wählen Sie bitte "Haben Sie ihr Passwort vergessen?".',
    'If you have never got a password or still forgot it, you can order a link to create a new one. Just type in the email address which is assigned to the contact record you want zu change or update and we will send youn an email.'
        => 'Falls Sie bisher kein Passwort erhalten oder das Passwort verloren haben, können Sie einen Link anfordern um ein neues Passwort zu erstellen. Geben Sie einfach die E-Mail Adresse an, die dem Kontaktdatensatz zugeordnet ist den Sie ändern oder aktualisieren möchten, wir senden Ihnen einen Link zu.',
    'Import contacts from KeepInTouch (KIT)'
        => 'Kontakte aus KeepInTouch (KIT) importieren',
    "Inserted the new contact with the ID %contact_id%."
        => 'Es wurde ein neuer Kontakt mit der ID %contact_id% hinzugefügt',
    'Intern'
        => 'Intern',
    'Invalid GUID, can not evaluate the desired account!'
        => 'Ungültiger Aktivierungslink! Die GUID wurde möglichweise bereits verwendet.',

    'Last name'
        => 'Nachname',
    'locked'
        => 'Gesperrt',
    'Locked'
        => 'Gesperrt',
    'login_name'
        => 'Kontakt Anmeldename',
    'Long name'
        => 'Langbezeichnung',
    'Long name (translated)'
        => 'Langbezeichnung (Übersetzung)',

    'Male'
        => 'Herr',
    'mandatory field'
        => 'Pflichtfeld',
    'Merchant'
        => 'Händler',
    'Missing the field definitions in `form.json`!'
        => 'In der `form.json` wurden keine Feld Definitionen gefunden!',
    "Missing the %identifier%! The ID should be set to -1 if you insert a new record."
        => 'Das Feld <b>%identifier%</b> fehlt! Diese ID sollte auf -1 gesetzt sein, wenn Sie einen neuen Datensatz einfügen möchten.',
    'Missing the key %field_name%, it must always set and not empty!'
        => 'Der Schlüssel %field_name% muss immer gesetzt werden und darf nicht leer sein!',
    'Missing the parameter <b>%parameter%</b>, please check the kitCommand expression!'
        => 'Vermisse den Parameter <b>%parameter%</b>, bitte prüfen Sie den kitCommand Ausdruck!',
    'Missing the `name` field in the definition!'
        => 'Bei den Definitionen für die Eingabefelder ist die Angabe des `name` Feld Pflicht!',
    'Missing the `type` field in the definition!'
        => 'Bei den Definitionen für die Eingabefelder ist die Angabe des `type` Feld Pflicht!',

    'Nick name'
        => 'Spitzname',
    'no extra field assigned'
        => 'kein Zusatzfeld zugeordnet',
    'No hits for the search term <i>%search%</i>!'
        => 'Keine Treffer für den Suchbegriff <i>%search%</i>!',
    'Note'
        => 'Notiz',
    'Note content'
        => 'Notiz',

    'Options'
        => 'Optionen',
    'Order name'
        => 'Bezeichnung',
    'Organization'
        => 'Organisation',

    'pending'
        => 'Ungeklärt',
    'PERSON'
        => 'Person',
    'Person birthday'
        => 'Geburtstag',
    'Person first name'
        => 'Vorname',
    'Person gender'
        => 'Anrede',
    'Person last name'
        => 'Nachname',
    'Person nick name'
        => 'Spitzname',
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
    'Please select one #tag at minimum!'
        => 'Bitte wählen Sie mindestens eine Markierung aus!',
    'Please select the type of contact you want to create. Be aware that you can not change the contact type if the record is created.'
        => 'Bitte legen Sie den Typ des neuen Kontakt fest. Bitte beachten Sie, dass Sie den Typ nicht mehr ändern können wenn der Datensatz angelegt ist.',
    'Please specify a search term!'
        => 'Bitte geben Sie einen Suchbegriff ein!',
    'prof'
        => 'Prof.',
    'PUBLIC'
        => 'Öffentlich',
    'Publish a contact'
        => 'Kontaktdaten freigeben',

    'Register a contact'
        => 'Kontaktdaten bestätigen',

    'Search'
        => 'Suche',
    'Search contact'
        => 'Kontakt suchen',
    'Select category'
        => 'Kategorie festlegen',
    'Select contact'
        => 'Kontakt auswählen',
    'Select contact type'
        => 'Kontakt Typ',
    'Select tags'
        => 'Markierungen auswählen',
    'Short name'
        => 'Kurzbezeichnung',
    'Short name (translated)'
        => 'Kurzbezeichnung (Übersetzung)',
    'Sorry, but there occured a problem while processing the form. We have informed the webmaster.'
        => 'Entschuldigung, während der Verarbeitung des Formulars ist ein Problem aufgetreten. Wir haben den Webmaster informiert.',
    'Sorry, but we have a problem. Please contact the webmaster and tell him to check the status of the email address %email%.'
        => 'Entschuldigung, wir haben ein Problem. Bitte wenden Sie sich an den Webmaster und bitten Sie ihn, den Status der E-Mail Adresse %email% zu überprüfen!',
    'Start import from KeepInTouch'
        => 'Den Import aus KeepInTouch starten',
    'State'
        => 'Bundesland',
    "Stay in touch, read our newsletter!"
        => 'Bleiben Sie mit uns in Kontakt, abonnieren Sie unseren Newsletter!',
    'Street'
        => 'Straße',
    'Submission from form %form%'
        => 'Übermittlung vom Formular %form%',

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
    'The category %category_type_name% was successfull deleted.'
        => 'Die Kategorie %category_type_name% wurde erfolgreich gelöscht.',
    'The category %category_type_name% was successfull inserted.'
        => 'Die Kategorie %category_type_name% wurde erfolgreich eingefügt.',
    'The category %category_type_name% was successfull updated'
        => 'Die Kategorie %category_type_name% wurde erfolgreich aktualisiert.',
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
    'The contact record has been successfull inserted.'
        => 'Der Kontaktdatensatz wurde erfolgreich eingefügt.',
    'The contact record has been successfull updated'
        => 'Der Kontaktdatensatz wurde erfolgreich aktualisiert',
    'The contact record is now published, the submitter has received an email with further information.'
        => 'Der Kontaktdatensatz wurde veröffentlicht, dem Übermittler wurde eine E-Mail mit weiteren Informationen zugesendet.',
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
    'The import from KeepInTouch was successfull finished.'
        => 'Der Import aus KeepInTouch wurde erfolgreich abgeschlossen.',
    'The last name must be at least two characters long!'
        => 'Der Nachname muss aus mindestens zwei Buchstaben bestehen!',
    'The login <b>%login%</b> is already in use, please choose another one!'
        => "Der Login <b>%login%</b> wird bereits verwendet, bitte legen Sie einen anderen Login fest!",
    "The login_name or a email address must be always set, can't insert the record!"
        => 'Das Feld <i>Anmeldename</i> oder eine <i>E-Mail Adresse</i> müssen immer gesetzt sein, kann den neuen Datensatz nicht einfügen!',
    'The phone number %number% failed the validation, please check it!'
        => 'Die Telefonnummer %number% ist wahrscheinlich fehlerhaft, bitte überprüfen!',
    "The process has not returned a status message"
        => 'Der Prozess hat keinen Status für den Vorgang gemeldet',
    'The record with the ID %id% was successfull inserted.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich eingefügt.',
    'The record with the ID %id% was successfull updated.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich aktualisiert.',
    'The record with the ID %id% was successfull deleted.'
        => 'Der Datensatz mit der ID %id% wurde erfolgreich gelöscht.',
    'The submitted contact record will be proofed and published as soon as possible, we will send you an email!'
        => 'Der übermittelte Kontaktdatensatz wird so rasch wie möglich geprüft und veröffentlicht, wir melden uns bei Ihnen per E-Mail!',
    'The tag type %tag_name% already exists!'
        => 'Die Markierung %tag_name% existiert bereits und kann nicht erneut eingefügt werden!',
    "The %type% entry %value% is marked for primary communication and can not removed!"
        => 'Der Typ %type% mit dem Wert %value% ist für die primäre Kommunikation mit dem Kontakt festgelegt und kann nicht gelöscht werden!',
    "The update returned 'FALSE' but no message ..."
        => "Die Aktualisierungsfunktion hat den Datensatz <b>nicht</b> aktualisiert und keinen Grund dafür mitgeteilt.",
    'The URL %url% is not valid, accepted is a pattern like http://example.com or https://www.example.com.'
        => 'Die URL %url% ist nicht gültig. Akzeptiert werden nur vollständige URL Angaben wie z.B. http://example.com oder https://www.example.com',
    'The zip %zip% is not valid!'
        => 'Die Postleitzahl <b>%zip%</b> ist nicht gültig, bitte prüfen Sie Ihre Eingabe!',
    'There exists already a contact record for you, but the status of this record is <strong>%status%</strong>. Please contact the webmaster to activate the existing record.'
        => 'Es existiert bereits ein Adressdatensatz für Sie, der Status dieses Datensatz ist jedoch auf <strong>%status%</strong> gesetzt. Bitte setzen Sie sich mit dem Webmaster in Verbindung um den Datensatz freizugeben.',
    'There exists already a contact record for you, but this record is assigned to a <strong>%type%</strong> and can not be changed. Please use the same type or contact the webmaster.'
        => 'Es existiert bereits ein Adressdatensatz für Sie, dieser ist jedoch einer <strong>%type%</strong> zugeordnet, der Typ kann nicht geändert werden. Bitte verwenden Sie den gleichen Kontakttyp oder kontaktieren Sie den Webmaster, damit dieser den Datensatz ändert.',
    'There exists no KeepInTouch installation at the parent CMS!'
        => 'Es existiert keine KeepInTouch Installation auf dem übergeordneten Content Management System!',
    'This is a sample panel text whith some unnecessary content'
        => 'Dies ist ein Beispiel für einen Panel Text mit etwas sinnfreiem Inhalt.',
    'This tag will be assigned to all user-defined `Contact` forms.'
        => 'Diese Markierung wird allen benutzerdefinierten `Contact` Formularen hinzugefügt.',
    'The value of the parameter contact_id must be an integer value and greater than 0'
        => 'Der Wert für den Parameter <i>contact_id</i> muss eine Ganzzahl größer als Null sein!',

    'Title'
        => 'Titel',
    'title_id'
        => 'ID',
    'Title list'
        => 'Titel Übersicht',
    'To prevent a timeout of the script the import was aborted after import of %counter% records. Please reload this page to continue the import process.'
        => 'Das Script wurde nach dem Import von %counter% Datensätzen abgebrochen, um eine Überschreitung der zulässigen Ausführungsdauer zu vermeiden. Bitte laden Sie diese Seite erneut um den Import forzusetzen.',

    'Unchecked'
        => '- ungeprüft -',
    'Unknown phone number format <strong>%format%</strong>, please check the settings!'
        => 'Unbekannte Telefonnummer Formatierung <strong>%format%</strong>, bitte prüfen Sie die Einstellungen!',

    'You are authenticated but not allowed to edit this contact'
        => 'Sie sind angemeldet, verfügen jedoch nicht über die Berechtigung diesen Kontaktdatensatz zu bearbeiten!',
    'Your contact record is now published, we have send you a confirmation mail with further information.'
        => 'Ihr Kontaktdatensatz wurde veröffentlicht, wir haben Ihnen eine E-Mail mit weiteren Informationen zugesendet.',

    'Zip'
        => 'Postleitzahl'
);

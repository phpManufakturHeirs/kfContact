<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Note;
use phpManufaktur\Contact\Data\Contact\Contact as ContactData;

class ContactNote extends ContactParent
{
    protected $Note = null;
    protected $Contact = null;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->Note = new Note($this->app);
        $this->ContactData = new ContactData($this->app);
    }

    /**
     * Return a default (empty) PERSON contact record.
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        return $this->Note->getDefaultRecord();
    }

    /**
     * Validate the given NOTE record
     *
     * @param reference array $note_data
     * @param array $contact_data
     * @param array $option
     * @return boolean
     */
    public function validate(&$note_data, $contact_data=array(), $option=array())
    {
        // the note_id must be always set!
        if (!isset($note_data['note_id'])) {
            $this->setMessage("Missing the %identifier%! The ID should be set to -1 if you insert a new record.",
                array('%identifier%' => 'note_id'));
            return false;
        }

        // check if any value is NULL
        foreach ($note_data as $key => $value) {
            if (is_null($value)) {
                switch ($key) {
                    case 'note_title':
                    case 'note_content':
                        $note_data[$key] = '';
                        break;
                    case 'contact_id':
                        $note_data[$key] = -1;
                        break;
                    case 'note_type':
                        $note_data[$key] = 'TEXT';
                        break;
                    case 'note_status':
                        $note_data[$key] = 'ACTIVE';
                        break;
                    default:
                        throw new ContactException("The key $key is not defined!");
                        break;
                }
            }
        }
        return true;
    }

    /**
     * Insert a new NOTE record. Check first for values which belong to depending
     * contact tables
     *
     * @param array $data
     * @param integer $contact_id
     * @param string $person_id
     * @throws ContactException
     * @return boolean
     */
    public function insert($data, $contact_id, &$note_id=null)
    {
        // enshure that the contact_id isset
        $data['contact_id'] = $contact_id;

        if (!empty($data['note_content'])) {
            if (!$this->validate($data)) {
                return false;
            }
            $note_id = -1;
            $this->Note->insert($data, $note_id);
            $this->app['monolog']->addInfo("Inserted note record for the contactID {$contact_id}", array(__METHOD__, __LINE__));
            if ($this->ContactData->getPrimaryNoteID($contact_id) < 1) {
                $this->ContactData->setPrimaryNoteID($contact_id, $note_id);
                $this->app['monolog']->addInfo("Set note ID $note_id as primary ID for contact $contact_id");
            }
        }
        return true;
    }
}


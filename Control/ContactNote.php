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

class ContactNote extends ContactParent
{

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
    }

    /**
     * Return a default (empty) PERSON contact record.
     *
     * @return array
     */
    public function getDefaultRecord()
    {
        $Note = new Note($this->app);
        return $Note->getDefaultRecord();
    }

    /**
     * Validate the given PERSON data record
     *
     * @param array $data
     * @return boolean
     */
    public function validate($data)
    {
        $message = '';
        $check = true;

        // not in use ...

        self::$message = $message;
        return $check;
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
        try {
            $note_id = -1;
            if (!isset($data['contact_id'])) {
                $data['contact_id'] = $contact_id;
            }
            $Note = new Note($this->app);
            $Note->insert($data, $note_id);
            $this->app['monolog']->addInfo("Inserted note record for the contactID {$contact_id}", array(__METHOD__, __LINE__));
            return true;
        } catch (\Exception $e) {
            throw new ContactException($e);
        }
    }
}


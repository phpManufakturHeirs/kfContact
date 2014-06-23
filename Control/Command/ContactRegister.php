<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Command;

use Silex\Application;
use phpManufaktur\Basic\Control\kitCommand\Basic;
use Symfony\Component\Form\FormFactory;
use phpManufaktur\Contact\Control\Configuration;
use phpManufaktur\Contact\Data\Contact\CategoryType;
use phpManufaktur\Contact\Data\Contact\ExtraCategory;
use phpManufaktur\Contact\Data\Contact\ExtraType;
use Carbon\Carbon;
use phpManufaktur\Contact\Data\Contact\TagType;

/**
 * Class ContactRegister
 * @package phpManufaktur\Contact\Control\Command
 */
class ContactRegister extends Basic
{
    protected $app = null;
    protected static $contact_type = null;
    protected static $category_type_id = null;
    protected static $tags = null;
    protected static $config = null;
    protected static $parameter = null;

    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\kitCommand\Basic::initParameters()
     */
    protected function initParameters(Application $app, $parameter_id=-1)
    {
        parent::initParameters($app, $parameter_id);

        self::$parameter = $this->getCommandParameters();

        // get the categoris from the parameter
        if (isset(self::$parameter['categories'])) {
            $CategoryType = new CategoryType($app);
            $cats = strpos(self::$parameter['categories'], ',') ? explode(',', self::$parameter['categories']) : array(self::$parameter['categories']);
            $category = array();
            foreach ($cats as $cat) {
                if ((false !== ($id = filter_var($cat, FILTER_VALIDATE_INT))) &&
                    (false !== ($type = $CategoryType->select($id))) &&
                    ($type['category_type_access'] == 'PUBLIC') &&
                    (!array_key_exists($type['category_type_id'], $category))) {
                    $category[$type['category_type_id']] = $app['utils']->humanize($type['category_type_name']);
                }
                elseif ((false !== ($type = $CategoryType->selectByName(trim($cat)))) &&
                        ($type['category_type_access'] == 'PUBLIC') &&
                        (!array_key_exists($type['category_type_id'], $category))) {
                    $category[$type['category_type_id']] = $app['utils']->humanize($type['category_type_name']);
                }
            }
            self::$parameter['categories'] = $category;
        }

        if (isset(self::$parameter['tags'])) {
            // get the tags from the parameter
            $TagType = new TagType($app);
            $tgs = strpos(self::$parameter['tags'], ',') ? explode(',', self::$parameter['tags']) : array(self::$parameter['tags']);
            $tags = array();
            foreach ($tgs as $tag) {
                if ((false !== ($id = filter_var($tag, FILTER_VALIDATE_INT))) &&
                    (false !== ($type = $TagType->select($id))) &&
                    !array_key_exists($type['tag_type_id'], $tags)) {
                    $tags[$type['tag_name']] = $app['utils']->humanize($type['tag_name']);
                }
                elseif ((false !== ($type = $TagType->selectByName(trim($tag)))) &&
                        !array_key_exists($type['tag_type_id'], $tags)) {
                    $tags[$type['tag_name']] = $app['utils']->humanize($type['tag_name']);
                }
            }
            self::$parameter['tags'] = $tags;
        }


        $request = $this->app['request']->get('form');
        if (isset($request['contact_type'])) {
            self::$contact_type = $request['contact_type'];
        }
        if (isset($request['category_type_id'])) {
            self::$category_type_id = $request['category_type_id'];
        }

        $Config = new Configuration($app);
        self::$config = $Config->getConfiguration();
    }

    /**
     * Get the Form to select the contact type
     *
     * @return FormFactory
     */
    protected function getFormSelectContactType()
    {
        return $this->app['form.factory']->createBuilder('form')
        ->add('contact_type', 'choice', array(
            'choices' => array('PERSON' => 'PERSON', 'COMPANY' => 'Organization'),
            'empty_value' => false,
            'multiple' => false,
            'expanded' => true,
            'data' => 'PERSON'
        ))
        ->getForm();
    }

    /**
     * Get the form to select the contact category
     *
     * @return FormFactory
     */
    protected function getFormSelectCategory()
    {
        // reset the category array
        reset(self::$parameter['categories']);

        return $this->app['form.factory']->createBuilder('form')
        ->add('contact_type', 'hidden', array(
            'data' => self::$contact_type
        ))
        ->add('category_type_id', 'choice', array(
            'choices' => self::$parameter['categories'],
            'empty_value' => false,
            'multiple' => false,
            'expanded' => true,
            // set the first entry as default value
            'data' => key(self::$parameter['categories'])
        ))
        ->getForm();
    }

    /**
     * Get the form to select the #tags assigned to this contact
     *
     * @return FormFactory
     */
    protected function getFormSelectTags()
    {
        return $this->app['form.factory']->createBuilder('form')
        ->add('contact_type', 'hidden', array(
            'data' => self::$contact_type
        ))
        ->add('category_type_id', 'hidden', array(
            'data' => self::$category_type_id
        ))
        ->add('tags', 'choice', array(
            'choices' => self::$parameter['tags'],
            'multiple' => true,
            'expanded' => true
        ))
        ->getForm();
    }

    /**
     * Get the form to submit a PERSON or COMPANY contact record
     *
     * @param array $data
     * @return FormFactory
     */
    protected function getFormContactData($data=array())
    {
        $field = self::$config['command']['register']['field'];

        $form = $this->app['form.factory']->createBuilder('form')
        ->add('contact_id', 'hidden', array(
            'data' => isset($data['contact_id']) ? $data['contact_id'] : -1
        ))
        ->add('contact_type', 'hidden', array(
            'data' => isset($data['contact_type']) ? $data['contact_type'] : self::$contact_type
        ))
        ->add('category_id', 'hidden', array(
            'data' => isset($data['category_id']) ? $data['category_id'] : -1
        ))
        ->add('category_type_id', 'hidden', array(
            'data' => isset($data['category_type_id']) ? $data['category_type_id'] : self::$category_type_id
        ))
        ->add('tags', 'hidden', array(
            'data' => isset($data['tags']) ? $data['tags'] : is_array(self::$tags) ? implode(',', self::$tags) : ''
        ));

        $form->add('person_id', 'hidden', array(
            'data' => isset($data['person_id']) ? $data['person_id'] : -1
        ));
        $form->add('company_id', 'hidden', array(
            'data' => isset($data['company_id']) ? $data['company_id'] : -1
        ));

        if (self::$contact_type == 'PERSON') {
            if (!in_array('person_gender', $field['unused'])) {
                $form->add('person_gender', 'choice', array(
                    'required' => in_array('person_gender', $field['required']),
                    'choices' => array('MALE' => 'Male', 'FEMALE' => 'Female'),
                    'expanded' => true,
                    'data' => isset($data['person_gender']) ? $data['person_gender'] : 'MALE'
                ));
            }
            if (!in_array('person_title', $field['unused'])) {
                $form->add('person_title', 'choice', array(
                    'choices' => $this->app['contact']->getTitleArrayForTwig(),
                    'empty_value' => '- please select -',
                    'expanded' => false,
                    'multiple' => false,
                    'required' => in_array('person_title', $field['required']),
                    'data' => isset($data['person_title']) ? $data['person_title'] : ''
                ));
            }
            if (!in_array('person_first_name', $field['unused'])) {
                $form->add('person_first_name', 'text', array(
                    'required' => in_array('person_first_name', $field['required']),
                    'data' => isset($data['person_first_name']) ? $data['person_first_name'] : ''
                ));
            }
            if (!in_array('person_last_name', $field['unused'])) {
                $form->add('person_last_name', 'text', array(
                    'required' => in_array('person_last_name', $field['required']),
                    'data' => isset($data['person_last_name']) ? $data['person_last_name'] : ''
                ));
            }
            if (!in_array('person_nick_name', $field['unused'])) {
                $form->add('person_nick_name', 'text', array(
                    'required' => in_array('person_nick_name', $field['required']),
                    'data' => isset($data['person_nick_name']) ? $data['person_nick_name'] : ''
                ));
            }
            if (!in_array('person_birthday', $field['unused'])) {
                $form->add('person_birthday', 'text', array(
                    'required' => false,
                    'data' => isset($data['person_birthday']) ? $data['person_birthday'] : '',
                    'attr' => array('class' => 'datepicker')
                ));
            }
        }
        else {
            // COMPANY CONTACT
            if (!in_array('company_name', $field['unused'])) {
                $form->add('company_name', 'text', array(
                    'required' => in_array('company_name', $field['required']),
                    'data' => isset($data['company_name']) ? $data['company_name'] : ''
                ));
            }
            if (!in_array('company_department', $field['unused'])) {
                $form->add('company_department', 'text', array(
                    'required' => in_array('company_department', $field['required']),
                    'data' => isset($data['company_department']) ? $data['company_department'] : ''
                ));
            }
        }

        // COMMUNICATION

        // the email address is always needed!
        $form->add('communication_email_id', 'hidden', array(
            'data' => isset($data['communication_email_id']) ? $data['communication_email_id'] : -1
        ));
        $form->add('communication_email', 'email', array(
            'data' => isset($data['communication_email']) ? $data['communication_email'] : ''
        ));

        if (!in_array('communication_phone', $field['unused'])) {
            $form->add('communication_phone_id', 'hidden', array(
                'data' => isset($data['communication_phone_id']) ? $data['communication_phone_id'] : -1
            ));
            $form->add('communication_phone', 'text', array(
                'required' => in_array('communication_phone', $field['required']),
                'data' => isset($data['communication_phone']) ? $data['communication_phone'] : ''
            ));
        }

        if (!in_array('communication_cell', $field['unused'])) {
            $form->add('communication_cell_id', 'hidden', array(
                'data' => isset($data['communication_cell_id']) ? $data['communication_cell_id'] : -1
            ));
            $form->add('communication_cell', 'text', array(
                'required' => in_array('communication_cell', $field['required']),
                'data' => isset($data['communication_cell']) ? $data['communication_cell'] : ''
            ));
        }

        if (!in_array('communication_fax', $field['unused'])) {
            $form->add('communication_fax_id', 'hidden', array(
                'data' => isset($data['communication_fax_id']) ? $data['communication_fax_id'] : -1
            ));
            $form->add('communication_fax', 'text', array(
                'required' => in_array('communication_fax', $field['required']),
                'data' => isset($data['communication_fax']) ? $data['communication_fax'] : ''
            ));
        }

        // ADDRESS
        $form->add('address_id', 'hidden', array(
            'data' => isset($data['address_id']) ? $data['address_id'] : -1
        ));

        if (!in_array('address_street', $field['unused'])) {
            $form->add('address_street', 'text', array(
                'required' => in_array('address_street', $field['required']),
                'data' => isset($ddata['address_street']) ? $data['address_street'] : ''
            ));
        }

        if (!in_array('address_zip', $field['unused'])) {
            $form->add('address_zip', 'text', array(
                'required' => in_array('address_zip', $field['required']),
                'data' => isset($ddata['address_zip']) ? $data['address_zip'] : ''
            ));
        }

        if (!in_array('address_city', $field['unused'])) {
            $form->add('address_city', 'text', array(
                'required' => in_array('address_city', $field['required']),
                'data' => isset($ddata['address_city']) ? $data['address_city'] : ''
            ));
        }

        if (!in_array('address_area', $field['unused'])) {
            $form->add('address_area', 'text', array(
                'required' => in_array('address_area', $field['required']),
                'data' => isset($ddata['address_area']) ? $data['address_area'] : ''
            ));
        }

        if (!in_array('address_state', $field['unused'])) {
            $form->add('address_state', 'text', array(
                'required' => in_array('address_state', $field['required']),
                'data' => isset($ddata['address_state']) ? $data['address_state'] : ''
            ));
        }

        if (!in_array('address_country_code', $field['unused'])) {
            $form->add('address_country_code', 'choice', array(
                'required' => in_array('address_country_code', $field['required']),
                'choices' => $this->app['contact']->getCountryArrayForTwig(),
                'empty_value' => '- please select -',
                'expanded' => false,
                'multiple' => false,
                'data' => isset($data['address_country_code']) ? $data['address_country_code'] : '',
                'preferred_choices' => self::$config['countries']['preferred']
            ));
        }

        // NOTE
        if (!in_array('note_content', $field['unused'])) {
            $form->add('note_id', 'hidden', array(
                'data' => isset($data['note_id']) ? $data['note_id'] : -1
            ));
            $form->add('note_content', 'textarea', array(
                'required' => in_array('note_content', $field['required']),
                'data' => isset($data['note_content']) ? $data['note_content'] : ''
            ));
        }

        // EXTRA FIELDS
        $ExtraCategory = new ExtraCategory($this->app);
        $type_ids = $ExtraCategory->selectTypeIDByCategoryTypeID(
            isset($data['category_type_id']) ? $data['category_type_id'] : self::$category_type_id);

        $ExtraType = new ExtraType($this->app);
        foreach ($type_ids as $type_id) {
            // get the extra field specification
            if (false !== ($extra = $ExtraType->select($type_id))) {
                $name = 'extra_'.strtolower($extra['extra_type_name']);
                if (in_array($name, $field['unused'])) {
                    // don't use this field
                    continue;
                }
                $extra_type = null;
                switch ($extra['extra_type_type']) {
                    // determine the form type
                    case 'TEXT':
                        $form_type = 'textarea';
                        $class = $name;
                        break;
                    case 'HTML':
                        $form_type = 'textarea';
                        $extra_type = 'html';
                        $class = $name;
                        break;
                    case 'DATE':
                        $form_type = 'text';
                        $class = "datepicker $name";
                        break;
                    default:
                        $form_type = 'text';
                        $class = $name;
                        break;
                }
                $form->add($name, $form_type, array(
                    'attr' => array('class' => $class, 'type' => $extra_type),
                    'required' => in_array($name, $field['required']),
                    'label' => $this->app['utils']->humanize($extra['extra_type_name']),
                    'data' => isset($data[$name]) ? $data['name'] : ''
                ));
            }
        }

        return $form->getForm();
    }

    /**
     * Check the submitted contact data and INSERT or UPDATE a contact record
     *
     * @param array reference $data
     * @param string reference $mode - 'INSERT' or 'UPDATE'
     * @return boolean
     */
    protected function checkContactData(&$data=array(), &$mode='INSERT')
    {
        $contact_status = 'PENDING';
        $contact_tags = (strpos($data['tags'], ',')) ? explode(',', $data['tags']) : array($data['tags']);

        if (($data['contact_id'] < 1) && (false !== ($existing_id = $this->app['contact']->existsLogin($data['communication_email'])))) {
            // this contact already exists
            $existing_contact = $this->app['contact']->select($existing_id);

            if ($existing_contact['contact']['contact_id'] != $existing_id) {
                $this->setAlert('Sorry, but we have a problem. Please contact the webmaster and tell him to check the status of the email address %email%.',
                    array('%email%' => $data['communication_email']), self::ALERT_TYPE_DANGER);
                return false;
            }

            if ($existing_contact['contact']['contact_status'] != 'ACTIVE') {
                // this contact is not ACTIVE - reject the registering!
                $this->setAlert('There exists already a contact record for you, but the status of this record is <strong>%status%</strong>. '.
                    'Please contact the webmaster to activate the existing record.',
                    array('%status%' => $this->app['translator']->trans($this->app['utils']->humanize($existing_contact['contact']['contact_status']))),
                    self::ALERT_TYPE_WARNING);
                return false;
            }
            if ($existing_contact['contact']['contact_type'] != $data['contact_type']) {
                // problem: the contact type differ!
                $this->setAlert('There exists already a contact record for you, but this record is assigned to a <strong>%type%</strong> and can not be changed. Please use the same type or contact the webmaster.',
                    array('%type%' => $this->app['translator']->trans($this->app['utils']->humanize($existing_contact['contact']['contact_type']))),
                    self::ALERT_TYPE_WARNING);
                return false;
            }
            // compare the existing data with the submitted data
            $data['contact_id'] = $existing_contact['contact']['contact_id'];
            $contact_status = $existing_contact['contact']['contact_status'];

            $data['address_id'] = $existing_contact['address'][0]['address_id'];
            $data['address_street'] = (isset($data['address_street']) && !empty($data['address_street']) && ($data['address_street'] != $existing_contact['address'][0]['address_street'])) ?
                $data['address_street'] : $existing_contact['address'][0]['address_street'];
            $data['address_city'] = (isset($data['address_city']) && !empty($data['address_city']) && ($data['address_city'] != $existing_contact['address'][0]['address_city'])) ?
                $data['address_city'] : $existing_contact['address'][0]['address_city'];
            $data['address_zip'] = (isset($data['address_zip']) && !empty($data['address_zip']) && ($data['address_zip'] != $existing_contact['address'][0]['address_zip'])) ?
                $data['address_zip'] : $existing_contact['address'][0]['address_zip'];
            $data['address_area'] = (isset($data['address_area']) && !empty($data['address_area']) && ($data['address_area'] != $existing_contact['address'][0]['address_area'])) ?
                $data['address_area'] : $existing_contact['address'][0]['address_area'];
            $data['address_state'] = (isset($data['address_state']) && !empty($data['address_state']) && ($data['address_state'] != $existing_contact['address'][0]['address_state'])) ?
                $data['address_state'] : $existing_contact['address'][0]['address_state'];
            $data['address_country_code'] = (isset($data['address_country_code']) && !empty($data['address_country_code']) && ($data['address_country_code'] != $existing_contact['address'][0]['address_country_code'])) ?
                $data['address_country_code'] : $existing_contact['address'][0]['address_country_code'];

            if ($data['contact_type'] == 'PERSON') {
                $data['person_id'] = (($data['person_id'] > 0) && ($data['person_id'] != $existing_contact['person'][0]['person_id'])) ? $data['person_id'] : $existing_contact['person'][0]['person_id'];
                $data['person_gender'] = (isset($data['person_gender']) && !empty($data['person_gender']) && ($data['person_gender'] != $existing_contact['person'][0]['person_gender'])) ?
                    $data['person_gender'] : $existing_contact['person'][0]['person_gender'];
                $data['person_title'] = (isset($data['person_title']) && !empty($data['person_title']) && ($data['person_title'] != $existing_contact['person'][0]['person_title'])) ?
                    $data['person_title'] : $existing_contact['person'][0]['person_title'];
                $data['person_first_name'] = (isset($data['person_first_name']) && !empty($data['person_first_name']) && ($data['person_first_name'] != $existing_contact['person'][0]['person_first_name'])) ?
                    $data['person_first_name'] : $existing_contact['person'][0]['person_first_name'];
                $data['person_last_name'] = (isset($data['person_last_name']) && !empty($data['person_last_name']) && ($data['person_last_name'] != $existing_contact['person'][0]['person_last_name'])) ?
                    $data['person_last_name'] : $existing_contact['person'][0]['person_last_name'];
                $data['person_nick_name'] = (isset($data['person_nick_name']) && !empty($data['person_nick_name']) && ($data['person_nick_name'] != $existing_contact['person'][0]['person_nick_name'])) ?
                    $data['person_nick_name'] : $existing_contact['person'][0]['person_nick_name'];
                if (isset($data['person_birthday']) && !empty($data['person_birthday'])) {
                    $dt = Carbon::createFromFormat($this->app['translator']->trans('DATE_FORMAT'), $data['person_birthday']);
                    $birthday = $dt->toDateTimeString();
                }
                else {
                    $birthday = '0000-00-00';
                }
                $data['person_birthday'] = (($birthday != '0000-00-00') && ($birthday != $existing_contact['person'][0]['person_birthday'])) ?
                    $birthday : $existing_contact['person'][0]['person_birthday'];
            }
            else {
                // COMPANY
                $data['company_id'] = (($data['company_id'] > 0) && ($data['company_id'] != $existing_contact['company'][0]['company_id'])) ? $data['company_id'] : $existing_contact['company'][0]['company_id'];
                $data['company_name'] = (isset($data['company_name']) && !empty($data['company_name']) && ($data['company_name'] != $existing_contact['company'][0]['company_name'])) ?
                    $data['company_name'] : $existing_contact['company'][0]['company_name'];
                $data['company_department'] = (isset($data['company_department']) && !empty($data['company_department']) && ($data['company_department'] != $existing_contact['company'][0]['company_department'])) ?
                    $data['company_department'] : $existing_contact['company'][0]['company_department'];
            }

            if (isset($existing_contact['communication']) && is_array($existing_contact['communication'])) {
                foreach ($existing_contact['communication'] as $communication) {
                    switch ($communication['communication_type']) {
                        case 'EMAIL':
                            if ($communication['communication_usage'] == 'PRIMARY') {
                                $data['communication_email_id'] = $communication['communication_id'];
                            }
                            break;
                        case 'PHONE':
                            if ($communication['communication_type'] == 'PRIMARY') {
                                $data['communication_phone_id'] = $communication['communication_id'];
                                $data['communication_phone'] = (isset($data['communication_phone']) && !empty($data['communication_phone']) && ($data['communication_phone'] != $communication['communication_value'])) ? $data['communication_phone'] : $communication['communication_value'];
                            }
                            break;
                        case 'CELL':
                            if ($communication['communication_type'] == 'BUSINESS') {
                                $data['communication_cell_id'] = $communication['communication_id'];
                                $data['communication_cell'] = (isset($data['communication_cell']) && !empty($data['communication_cell']) && ($data['communication_cell'] != $communication['communication_value'])) ? $data['communication_cell'] : $communication['communication_value'];
                            }
                            break;
                        case 'FAX':
                            if ($communication['communication_type'] == 'BUSINESS') {
                                $data['communication_fax_id'] = $communication['communication_id'];
                                $data['communication_fax'] = (isset($data['communication_fax']) && !empty($data['communication_fax']) && ($data['communication_fax'] != $communication['communication_value'])) ? $data['communication_fax'] : $communication['communication_value'];
                            }
                            break;
                    }
                }
            }

            $data['note_id'] = isset($existing_contact['note'][0]['note_id']) ? $existing_contact['note'][0]['note_id'] : -1;
            if ($data['note_id'] > 0) {
                $data['note_content'] = (isset($data['note_content']) && !empty($data['note_content']) && ($data['note_content'] != $existing_contact['note'][0]['note_content'])) ?
                    $data['note_content'] : $existing_contact['note'][0]['note_content'];
            }

            $data['category_id'] = (($data['category_id'] > 0) && ($data['category_id'] != $existing_contact['category'][0]['category_id'])) ? $data['category_id'] : $existing_contact['category'][0]['category_id'];
            $data['category_type_id'] = ($data['category_type_id'] != $existing_contact['category'][0]['category_type_id']) ? $data['category_type_id'] : $existing_contact['category'][0]['category_type_id'];

            if (isset($existing_contact['extra_fields']) && is_array($existing_contact['extra_fields'])) {
                foreach ($existing_contact['extra_fields'] as $extra_field) {
                    $name = 'extra_'.strtolower($extra_field['extra_type_name']);
                    $id = 'extra_'.strtolower($extra_field['extra_type_name']).'_id';
                    $data[$name] = (isset($data[$name]) && !empty($data[$name]) && ($data[$name] != $extra_field['extra_value'])) ? $data[$name] : $extra_field['extra_value'];
                    $data[$id] = $extra_field['extra_id'];
                }
            }

            // important: on update grant that existing tags will be not removed !!!
            if (isset($existing_contact['tag']) && is_array($existing_contact['tag'])) {
                foreach ($existing_contact['tag'] as $tag_field) {
                    if (!array_key_exists($tag_field['tag_name'], self::$parameter['tags']) &&
                        !in_array($tag_field['tag_name'], $contact_tags)) {
                        $contact_tags[] = $tag_field['tag_name'];
                    }
                }
                $data['tags'] = implode(',', $contact_tags);
            }
        }

        /**
            Create the contact record for INSERT or UPDATE
         */
        $contact = array(
            'contact' => array(
                'contact_id' => $data['contact_id'],
                'contact_type' => $data['contact_type'],
                'contact_status' => $contact_status,
                'contact_login' => $data['communication_email']
            ),
            'address' => array(
                array(
                    'address_id' => $data['address_id'],
                    'contact_id' => $data['contact_id'],
                    'address_type' => ($data['contact_type'] == 'PERSON') ? 'PRIVATE' : 'BUSINESS',
                    'address_street' => isset($data['address_street']) ? $data['address_street'] : '',
                    'address_zip' => isset($data['address_zip']) ? $data['address_zip'] : '',
                    'address_city' => isset($data['address_city']) ? $data['address_city'] : '',
                    'address_area' => isset($data['address_area']) ? $data['address_area'] : '',
                    'address_state' => isset($data['address_state']) ? $data['address_state'] : '',
                    'address_country_code' => isset($data['address_country_code']) ? $data['address_country_code'] : ''
                )
            )
        );

        if (!isset($birthday) && isset($data['person_birthday']) && !empty($data['person_birthday'])) {
            $dt = Carbon::createFromFormat($this->app['translator']->trans('DATE_FORMAT'), $data['person_birthday']);
            $birthday = $dt->toDateTimeString();
        }
        else {
            $birthday = '0000-00-00';
        }

        if ($data['contact_type'] == 'PERSON') {
            $contact['person'] = array(
                array(
                    'person_id' => $data['person_id'],
                    'contact_id' => $data['contact_id'],
                    'person_gender' => isset($data['person_gender']) ? $data['person_gender'] : 'MALE',
                    'person_title' => isset($data['person_title']) ? $data['person_title'] : 'NO_TITLE',
                    'person_first_name' => isset($data['person_first_name']) ? $data['person_first_name'] : '',
                    'person_last_name' => isset($data['person_last_name']) ? $data['person_last_name'] : '',
                    'person_nick_name' => isset($data['person_nick_name']) ? $data['person_nick_name'] : '',
                    'person_birthday' => $birthday
                )
            );
        }
        else {
            $contact['company'] = array(
                array(
                    'company_id' => $data['company_id'],
                    'contact_id' => $data['contact_id'],
                    'company_name' => isset($data['company_name']) ? $data['company_name'] : '',
                    'company_department' => isset($data['company_department']) ? $data['company_department'] : ''
                )
            );
        }

        $contact['communication'][] = array(
            'communication_id' => $data['communication_email_id'],
            'contact_id' => $data['contact_id'],
            'communication_type' => 'EMAIL',
            'communication_usage' => 'PRIMARY',
            'communication_value' => $data['communication_email']
        );

        if (isset($data['communication_phone']) && !empty($data['communication_phone'])) {
            $contact['communication'][] = array(
                'communication_id' => $data['communication_phone_id'],
                'contact_id' => $data['contact_id'],
                'communication_type' => 'PHONE',
                'communication_usage' => 'PRIMARY',
                'communication_value' => $data['communication_phone']
            );
        }

        if (isset($data['communication_cell']) && !empty($data['communication_cell'])) {
            $contact['communication'][] = array(
                'communication_id' => $data['communication_cell_id'],
                'contact_id' => $data['contact_id'],
                'communication_type' => 'CELL',
                'communication_usage' => 'BUSINESS',
                'communication_value' => $data['communication_cell']
            );
        }

        if (isset($data['communication_fax']) && !empty($data['communication_fax'])) {
            $contact['communication'][] = array(
                'communication_id' => $data['communication_fax_id'],
                'contact_id' => $data['contact_id'],
                'communication_type' => 'FAX',
                'communication_usage' => 'BUSINESS',
                'communication_value' => $data['communication_fax']
            );
        }

        $contact['note'] = array(
            array(
                'note_id' => isset($data['note_id']) ? $data['note_id'] : -1,
                'contact_id' => $data['contact_id'],
                'note_title' => 'Remark',
                'note_type' => 'TEXT',
                'note_content' => isset($data['note_content']) ? $data['note_content'] : ''
            )
        );

        $CategoryType = new CategoryType($this->app);
        $category_type = $CategoryType->select($data['category_type_id']);

        $contact['category'] = array(
            array(
                'category_id' => $data['category_id'],
                'contact_id' => $data['contact_id'],
                'category_type_id' => $data['category_type_id'],
                'category_type_name' => $category_type['category_type_name']
            )
        );

        // EXTRA FIELDS
        $ExtraCategory = new ExtraCategory($this->app);
        $type_ids = $ExtraCategory->selectTypeIDByCategoryTypeID($data['category_type_id']);
        $ExtraType = new ExtraType($this->app);

        $contact['extra_fields'] = array();
        foreach ($type_ids as $type_id) {
            // get the extra field specification
            if (false !== ($extra = $ExtraType->select($type_id))) {
                $name = 'extra_'.strtolower($extra['extra_type_name']);
                $id = 'extra_'.strtolower($extra['extra_type_name']).'_id';
                if (isset($data[$name])) {
                    $contact['extra_fields'][] = array(
                        'extra_id' => isset($data[$id]) ? $data[$id] : -1,
                        'extra_type_id' => $extra['extra_type_id'],
                        'extra_type_name' => $extra['extra_type_name'],
                        'category_id' => $data['category_id'],
                        'category_type_name' => $category_type['category_type_name'],
                        'contact_id' => $data['contact_id'],
                        'extra_type_type' => $extra['extra_type_type'],
                        'extra_value' => $data[$name]
                    );
                }
            }
        }

        foreach ($contact_tags as $tag) {
            $contact['tag'][] = array(
                'tag_name' => $tag,
                'contact_id' => $data['contact_id']
            );
        }

        if ($data['contact_id'] < 1) {
            // insert a new record
            $mode = 'INSERT';
            if (!$this->app['contact']->insert($contact, $data['contact_id'])) {
                // problem inserting - an Alert will be set by the contact interface
                return false;
            }
            $contact['contact_id'] = $data['contact_id'];
            $data = $contact;
            // clear all Alerts from the contact interface!
            $this->clearAlert();

            $this->setAlert('The contact record has been successfull inserted.');
        }
        else {
            $mode = 'UPDATE';
            if (!$this->app['contact']->update($contact, $data['contact_id'])) {
                // problem updating - an Alert will be set by the contact interface
                return false;
            }
            $data = $contact;

            // clear all Alerts from the contact interface!
            $this->clearAlert();

            $this->setAlert('The contact record has been successfull updated');
        }

        return true;
    }

    /**
     * Send the activation link to the submitter
     *
     * @param integer $contact_id
     * @return boolean
     */
    protected function sendActivationLink($contact_id)
    {
        $contact = $this->app['contact']->selectOverview($contact_id);
        $allowed_roles = array('ROLE_USER', 'ROLE_CONTACT_EDIT_OWN');
        if (false === ($account = $this->app['account']->getUserData($contact['contact_login']))) {
            // user has no account - create it
            $this->app['account']->createAccount(
                $contact['contact_login'],
                $contact['communication_email'],
                $this->app['utils']->createPassword(),
                implode(',', $allowed_roles),
                $contact['order_name']
            );
        }
        else {
            // check if the new allowed roles are assigned to the user account
            $roles = explode(',', $account['roles']);
            foreach ($allowed_roles as $role) {
                if (!in_array($role, $roles)) {
                    $roles[] = $role;
                }
            }
            $data = array(
                'roles' => implode(',', $roles)
            );
            $this->app['account']->updateUserData($account['username'], $data);
        }

        // create a new GUID
        if (false === ($guid = $this->app['account']->createGUID($contact['communication_email'], false))) {
            $this->setAlert('Can not create GUID, submission aborted, please contact the webmaster.',
                array(), self::ALERT_TYPE_DANGER);
            return false;
        }

        // create the email body
        $body = $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/mail/user/register.contact.activate.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'contact' => $contact,
                'activation_link' => $this->getCMSpageURL().'?command=contact&action=confirm_register&guid='.$guid
            ));

        // send a email to the contact
        $message = \Swift_Message::newInstance()
        ->setSubject($this->app['translator']->trans('Register a contact'))
        ->setFrom(array(SERVER_EMAIL_ADDRESS => SERVER_EMAIL_NAME))
        ->setTo($contact['communication_email'])
        ->setBody($body)
        ->setContentType('text/html');
        // send the message
        $failedRecipients = null;
        if (!$this->app['mailer']->send($message, $failedRecipients))  {
            $this->setAlert("Can't send mail to %recipients%.", array(
                '%recipients%' => implode(',', $failedRecipients)), self::ALERT_TYPE_DANGER);
            return false;
        }
        return true;
    }

    /**
     * Controller to check the submitted contact data
     *
     * @param Application $app
     */
    public function ControllerRegisterContactCheck(Application $app)
    {
        $this->initParameters($app);

        $form = $this->getFormContactData();

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                $contact = $form->getData();
                $mode = 'INSERT';
                if ($this->checkContactData($contact, $mode)) {
                    // contact data are ok - send confirmation mails and say goodbye ...
                    if ($mode == 'INSERT') {
                        $this->sendActivationLink($contact['contact_id']);
                    }
                    return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                        '@phpManufaktur/Contact/Template', 'command/register.contact.submitted.twig',
                        $this->getPreferredTemplateStyle()),
                        array(
                            'basic' => $this->getBasicSettings(),
                            'contact' => $contact,
                            'mode' => $mode
                        ));
                }
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setAlert('The form is not valid, please check your input and try again!',
                    array(), self::ALERT_TYPE_DANGER);
            }
        }

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/register.contact.data.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView()
            ));
    }

    /**
     * Show the Contact Data Dialog
     *
     * @return string
     */
    protected function registerContact()
    {
        $form = $this->getFormContactData();

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/register.contact.data.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView()
            ));
    }

    /**
     * Show the Dialog to select one or more Tags
     *
     * @return string
     */
    protected function registerTags()
    {
        if (!isset(self::$parameter['tags'])) {
            self::$tags = array();
            return $this->registerContact();
        }
        elseif (is_array(self::$parameter['tags']) && (count(self::$parameter['tags']) < 2)) {
            reset(self::$parameter['tags']);
            self::$tags = array(key(self::$parameter['tags']));
            return $this->registerContact();
        }

        $form = $this->getFormSelectTags();
        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/register.contact.tags.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView()
            ));
    }

    /**
     * Check the response of the Category Select Dialog and call the
     * Contact Data Dialog as next step
     *
     * @param Application $app
     * @return string
     */
    public function ControllerRegisterCategoryCheck(Application $app)
    {
        $this->initParameters($app);

        $form = $this->getFormSelectCategory();

        $form->bind($this->app['request']);
        if ($form->isValid()) {
            // get the form data
            $contact = $form->getData();
            // show the dialog for PERSON or COMPANY contacts
            self::$contact_type = $contact['contact_type'];
            self::$category_type_id = $contact['category_type_id'];
            return $this->registerTags();
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->registerCategory();
        }
    }

    /**
     * Controller check the given tags and then call the contact dialog
     *
     * @param Application $app
     * @return string
     */
    public function ControllerRegisterTagsCheck(Application $app)
    {
        $this->initParameters($app);

        $form = $this->getFormSelectTags();
        $form->bind($this->app['request']);
        if ($form->isValid()) {
            // get the form data
            $data = $form->getData();
            // show the dialog for PERSON or COMPANY contacts
            self::$contact_type = $data['contact_type'];
            self::$category_type_id = $data['category_type_id'];
            self::$tags = $data['tags'];
            if (in_array('tags', self::$config['command']['register']['field']['required']) &&
                (count(self::$tags) < 1)) {
                // user must select one tag at minimum
                $this->setAlert('Please select one #tag at minimum!', array(), self::ALERT_TYPE_WARNING);
                return $this->registerTags();
            }
            return $this->registerContact();
        }
        else {
            // general error (timeout, CSFR ...)
            $this->setAlert('The form is not valid, please check your input and try again!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->registerTags();
        }
    }

    /**
     * Show the dialog to select a Contact Category.
     * If only one category is defined, redirect to the Contact Data Dialog
     *
     * @return string
     */
    protected function registerCategory()
    {
        if (count(self::$parameter['categories']) == 1) {
            // we have exacly one category - the key contains the ID ...
            reset(self::$parameter['categories']);
            self::$category_type_id = key(self::$parameter['categories']);
            return $this->registerTags();
        }

        $form = $this->getFormSelectCategory();
        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/register.contact.category.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView()
            ));
    }

    /**
     * Controller to start register a Public Contact
     *
     * @param Application $app
     * @return string
     */
    public function ControllerRegister(Application $app)
    {
        $this->initParameters($app);

        if (empty(self::$parameter['categories'])) {
            $this->setAlert('Please use the parameter <em>categories[]</em> to specify at minimum one category with PUBLIC access!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        // create the form
        $form = $this->getFormSelectContactType();

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                // get the form data
                $contact = $form->getData();
                // show the dialog for PERSON or COMPANY contacts
                self::$contact_type = $contact['contact_type'];
                return $this->registerCategory();
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setAlert('The form is not valid, please check your input and try again!',
                    array(), self::ALERT_TYPE_DANGER);
            }
        }

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/register.contact.type.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'form' => $form->createView()
            ));
    }

    /**
     * Controller to reject a contact record. The contact record will be deleted
     * and the submitter will receive an email.
     *
     * @param Application $app
     * @param string $guid
     * @return boolean
     */
    public function ControllerRegisterRejectAdmin(Application $app, $guid)
    {
        // don't use initParameters() of this class - we won't check parameters!
        parent::initParameters($app);

        if (false === ($account = $this->app['account']->getUserByGUID($guid))) {
            $this->setAlert('Invalid GUID, can not evaluate the desired account!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        if (false === ($contact = $this->app['contact']->selectOverview($account['email']))) {
            $this->setAlert('The GUID was valid but can not get the contact record desired to the account!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        $data = array(
            'contact' => array(
                'contact_id' => $contact['contact_id'],
                'contact_status' => 'DELETED'
            )
        );

        if (false === $this->app['contact']->update($data, $contact['contact_id'])) {
            return $this->promptAlert();
        }

        // create the email body
        $body = $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/mail/user/register.contact.rejected.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'contact' => $contact,
            ));

        // send a email to the contact
        $message = \Swift_Message::newInstance()
        ->setSubject($this->app['translator']->trans('Contact rejected'))
        ->setFrom(array(SERVER_EMAIL_ADDRESS => SERVER_EMAIL_NAME))
        ->setTo($contact['communication_email'])
        ->setBody($body)
        ->setContentType('text/html');
        // send the message
        $failedRecipients = null;
        if (!$this->app['mailer']->send($message, $failedRecipients))  {
            $this->setAlert("Can't send mail to %recipients%.", array(
                '%recipients%' => implode(',', $failedRecipients)), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        // clear all alerts from the contact interface
        $this->clearAlert();

        $this->setAlert('The contact was rejected and an email send to the submitter');
        return $this->promptAlert();
    }

    /**
     * Action to perform the publishing of the contact record - update record, send
     * mail to the submitter a.s.o.
     *
     * @param unknown $account
     * @param unknown $contact
     * @param string $published_by
     * @return \phpManufaktur\Basic\Control\Pattern\rendered
     */
    protected function publishContact($account, $contact, $published_by='user')
    {
        // activate the contact
        $data = array(
            'contact' => array(
                'contact_id' => $contact['contact_id'],
                'contact_status' => 'ACTIVE'
             )
        );
        if (!$this->app['contact']->update($data, $contact['contact_id'])) {
            return $this->promptAlert();
        }

        // create the email body
        $body = $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/mail/user/register.contact.published.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'contact' => $contact,
                'permalink' => FRAMEWORK_URL.'/contact/public/view/id/'.$contact['contact_id']
            ));

        // send a email to the contact
        $message = \Swift_Message::newInstance()
        ->setSubject($this->app['translator']->trans('Contact published'))
        ->setFrom(array(SERVER_EMAIL_ADDRESS => SERVER_EMAIL_NAME))
        ->setTo($contact['communication_email'])
        ->setBody($body)
        ->setContentType('text/html');
        // send the message
        $failedRecipients = null;
        if (!$this->app['mailer']->send($message, $failedRecipients))  {
            $this->setAlert("Can't send mail to %recipients%.", array(
                '%recipients%' => implode(',', $failedRecipients)), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        // clear all existing alerts
        $this->clearAlert();

        if ($published_by == 'user') {
            $this->setAlert('Your contact record is now published, we have send you a confirmation mail with further information.',
                array(), self::ALERT_TYPE_SUCCESS);
        }
        else {
            $this->setAlert('The contact record is now published, the submitter has received an email with further information.');
        }
        return $this->promptAlert();
    }

    /**
     * Controller for the Activation of a contact record by the admin
     *
     * @param Application $app
     * @param string $guid
     * @return \phpManufaktur\Basic\Control\Pattern\rendered
     */
    public function ControllerRegisterActivationAdmin(Application $app, $guid)
    {
        // don't use initParameters() of this class - we won't check parameters!
        parent::initParameters($app);

        if (false === ($account = $this->app['account']->getUserByGUID($guid))) {
            $this->setAlert('Invalid GUID, can not evaluate the desired account!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        if (false === ($contact = $this->app['contact']->selectOverview($account['email']))) {
            $this->setAlert('The GUID was valid but can not get the contact record desired to the account!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        return $this->publishContact($account, $contact, 'admin');
    }

    /**
     * Controller to check the Activation by GUID
     *
     * @param Application $app
     * @param string $guid
     */
    public function ControllerRegisterActivation(Application $app, $guid)
    {
        // don't use initParameters() of this class - we won't check parameters!
        parent::initParameters($app);

        if (false === ($account = $this->app['account']->getUserByGUID($guid))) {
            $this->setAlert('Invalid GUID, can not evaluate the desired account!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        if (false === ($contact = $this->app['contact']->selectOverview($account['email']))) {
            $this->setAlert('The GUID was valid but can not get the contact record desired to the account!',
                array(), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }

        $Config = new Configuration($app);
        self::$config = $Config->getConfiguration();

        if (strtolower(self::$config['command']['register']['publish']['activation']) == 'admin') {
            // the administrator must check and activate the contact record
            $guid = $this->app['account']->createGUID($account['email'], false);

            // create the email body
            $body = $this->app['twig']->render($this->app['utils']->getTemplateFile(
                '@phpManufaktur/Contact/Template', 'command/mail/admin/register.contact.check.twig',
                $this->getPreferredTemplateStyle()),
                array(
                    'basic' => $this->getBasicSettings(),
                    'contact' => $contact,
                    'publish_link' => $this->getCMSpageURL().'?command=contact&action=confirm_publish&guid='.$guid,
                    'reject_link' => $this->getCMSpageURL().'?command=contact&action=confirm_reject&guid='.$guid
                ));

            // send a email to the contact
            $message = \Swift_Message::newInstance()
            ->setSubject($this->app['translator']->trans('Publish a contact'))
            ->setFrom(array(SERVER_EMAIL_ADDRESS => SERVER_EMAIL_NAME))
            ->setTo(SERVER_EMAIL_ADDRESS)
            ->setReplyTo($contact['communication_email'])
            ->setBody($body)
            ->setContentType('text/html');
            // send the message
            $failedRecipients = null;
            if (!$this->app['mailer']->send($message, $failedRecipients))  {
                $this->setAlert("Can't send mail to %recipients%.", array(
                    '%recipients%' => implode(',', $failedRecipients)), self::ALERT_TYPE_DANGER);
                return $this->promptAlert();
            }

            // create the email body
            $body = $this->app['twig']->render($this->app['utils']->getTemplateFile(
                '@phpManufaktur/Contact/Template', 'command/mail/user/register.contact.pending.twig',
                $this->getPreferredTemplateStyle()),
                array(
                    'basic' => $this->getBasicSettings(),
                    'contact' => $contact,
                ));

            // send a email to the contact
            $message = \Swift_Message::newInstance()
            ->setSubject($this->app['translator']->trans('Contact pending'))
            ->setFrom(array(SERVER_EMAIL_ADDRESS => SERVER_EMAIL_NAME))
            ->setTo($contact['communication_email'])
            ->setBody($body)
            ->setContentType('text/html');
            // send the message
            $failedRecipients = null;
            if (!$this->app['mailer']->send($message, $failedRecipients))  {
                $this->setAlert("Can't send mail to %recipients%.", array(
                    '%recipients%' => implode(',', $failedRecipients)), self::ALERT_TYPE_DANGER);
                return $this->promptAlert();
            }

            $this->setAlert('The submitted contact record will be proofed and published as soon as possible, we will send you an email!',
                array(), self::ALERT_TYPE_SUCCESS);
            return $this->promptAlert();
        }
        elseif (strtolower(self::$config['command']['register']['publish']['activation']) == 'user') {
            // the user is allowed to activate the contact record
            return $this->publishContact($account, $contact);
        }
        else {
            // unknown activation value
            $this->setAlert("Don't understand the value %value% for the entry: command->register->publish->activate, please check the configuration!",
                array('%value%' => self::$config['command']['register']['publish']['activate']), self::ALERT_TYPE_DANGER);
            return $this->promptAlert();
        }
    }

}

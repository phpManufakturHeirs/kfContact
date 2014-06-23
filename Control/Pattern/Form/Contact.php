<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Pattern\Form;

use Silex\Application;
use Symfony\Component\Form\FormFactory;
use phpManufaktur\Contact\Control\Configuration;
use phpManufaktur\Basic\Control\Pattern\Alert;
use phpManufaktur\Contact\Data\Contact\TagType;
use phpManufaktur\Contact\Data\Contact\ExtraCategory;
use phpManufaktur\Contact\Data\Contact\ExtraType;

class Contact extends Alert
{
    protected static $config = null;
    protected static $person_fields = array(
        'person_id',
        'person_gender',
        'person_title',
        'person_first_name',
        'person_last_name',
        'person_nick_name',
        'person_birthday'
    );
    protected static $company_fields = array(
        'company_id',
        'company_name',
        'company_department',
        'company_additional',
        'company_additional_2',
        'company_additional_3'
    );


    /**
     * (non-PHPdoc)
     * @see \phpManufaktur\Basic\Control\Pattern\Alert::initialize()
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);

        $Config = new Configuration($app);
        self::$config = $Config->getConfiguration();
    }

    public function selectContactRecord($contact_id)
    {
        $data = array();
        $contact = $this->app['contact']->select($contact_id);
        if ((!isset($contact['contact']['contact_id'])) || ($contact['contact']['contact_id'] < 1)) {
            return false;
        }

        // get contact section
        foreach ($contact['contact'] as $key => $value) {
            $data[$key] = $value;
        }

        // get the person section
        if (isset($contact['person'][0]) && ($contact['person'][0]['person_id'] > 0)) {
            foreach ($contact['person'][0] as $key => $value) {
                $data[$key] = $value;
            }
        }

        // get the company section
        if (isset($contact['company'][0]) && ($contact['company'][0]['company_id'])) {
            foreach ($contact['company'][0] as $key => $value) {
                $data[$key] = $value;
            }
        }

        if (isset($contact['communication']) && is_array($contact['communication'])) {
            foreach ($contact['communication'] as $communication) {
                if (($communication['communication_status'] == 'ACTIVE') &&
                    ($communication['communication_id'] > 0)) {
                    switch ($communication['communication_type']) {
                        case 'EMAIL':
                            if ($communication['communication_usage'] == 'PRIMARY') {
                                $data['communication_email_id'] = $communication['communication_id'];
                                $data['communication_email'] = $communication['communication_value'];
                            }
                            elseif (!isset($data['communication_email_secondary'])) {
                                $data['communication_email_secondary_id'] = $communication['communication_id'];
                                $data['communication_email_secondary'] = $communication['communication_value'];
                            }
                            break;
                        case 'PHONE':
                            if ($communication['communication_usage'] == 'PRIMARY') {
                                $data['communication_phone_id'] = $communication['communication_id'];
                                $data['communication_phone'] = $communication['communication_value'];
                            }
                            elseif (!isset($data['communication_phone_secondary'])) {
                                $data['communication_phone_secondary_id'] = $communication['communication_id'];
                                $data['communication_phone_secondary'] = $communication['communication_value'];
                            }
                            break;
                        case 'CELL':
                            if ($communication['communication_usage'] == 'PRIMARY') {
                                $data['communication_cell_id'] = $communication['communication_id'];
                                $data['communication_cell'] = $communication['communication_value'];
                            }
                            elseif (!isset($data['communication_cell_secondary'])) {
                                $data['communication_cell_secondary_id'] = $communication['communication_id'];
                                $data['communication_cell_secondary'] = $communication['communication_value'];
                            }
                            break;
                        case 'FAX':
                            if ($communication['communication_usage'] == 'PRIMARY') {
                                $data['communication_fax_id'] = $communication['communication_id'];
                                $data['communication_fax'] = $communication['communication_value'];
                            }
                            elseif (!isset($data['communication_fax_secondary'])) {
                                $data['communication_fax_secondary_id'] = $communication['communication_id'];
                                $data['communication_fax_secondary'] = $communication['communication_value'];
                            }
                            break;
                        case 'URL':
                            if ($communication['communication_usage'] == 'PRIMARY') {
                                $data['communication_url_id'] = $communication['communication_id'];
                                $data['communication_url'] = $communication['communication_value'];
                            }
                            elseif (!isset($data['communication_url_secondary'])) {
                                $data['communication_url_secondary_id'] = $communication['communication_id'];
                                $data['communication_url_secondary'] = $communication['communication_value'];
                            }
                            break;
                        default:
                            // nothing to do here ...
                            break;
                    }
                }
            }
        }

        if (isset($contact['address']) && is_array($contact['address'])) {
            foreach ($contact['address'] as $address) {
                if (($address['address_status'] == 'ACTIVE') && ($address['address_id'] > 0)) {
                    switch ($address['address_type']) {
                        case 'PRIVATE':
                        case 'BUSINESS':
                            if (!isset($data['address_id'])) {
                                $data['address_id'] = $address['address_id'];
                                $data['address_street'] = $address['address_street'];
                                $data['address_zip'] = $address['address_zip'];
                                $data['address_city'] = $address['address_city'];
                                $data['address_area'] = $address['address_area'];
                                $data['address_state'] = $address['address_state'];
                                $data['address_country_code'] = $address['address_country_code'];
                            }
                            elseif (!isset($data['address_secondary_id'])) {
                                $data['address_secondary_id'] = $address['address_id'];
                                $data['address_secondary_street'] = $address['address_street'];
                                $data['address_secondary_zip'] = $address['address_zip'];
                                $data['address_secondary_city'] = $address['address_city'];
                                $data['address_secondary_area'] = $address['address_area'];
                                $data['address_secondary_state'] = $address['address_state'];
                                $data['address_secondary_country_code'] = $address['address_country_code'];
                            }
                            break;
                        case 'DELIVERY':
                            if (!isset($data['address_delivery_id'])) {
                                $data['address_delivery_id'] = $address['address_id'];
                                $data['address_delivery_street'] = $address['address_street'];
                                $data['address_delivery_zip'] = $address['address_zip'];
                                $data['address_delivery_city'] = $address['address_city'];
                                $data['address_delivery_area'] = $address['address_area'];
                                $data['address_delivery_state'] = $address['address_state'];
                                $data['address_delivery_country_code'] = $address['address_country_code'];
                            }
                            elseif (!isset($data['address_delivery_secondary_id'])) {
                                $data['address_delivery_secondary_id'] = $address['address_id'];
                                $data['address_delivery_secondary_street'] = $address['address_street'];
                                $data['address_delivery_secondary_zip'] = $address['address_zip'];
                                $data['address_delivery_secondary_city'] = $address['address_city'];
                                $data['address_delivery_secondary_area'] = $address['address_area'];
                                $data['address_delivery_secondary_state'] = $address['address_state'];
                                $data['address_delivery_secondary_country_code'] = $address['address_country_code'];
                            }
                            break;
                        case 'BILLING':
                            if (!isset($data['address_billing_id'])) {
                                $data['address_billing_id'] = $address['address_id'];
                                $data['address_billing_street'] = $address['address_street'];
                                $data['address_billing_zip'] = $address['address_zip'];
                                $data['address_billing_city'] = $address['address_city'];
                                $data['address_billing_area'] = $address['address_area'];
                                $data['address_billing_state'] = $address['address_state'];
                                $data['address_billing_country_code'] = $address['address_country_code'];
                            }
                            elseif (!isset($data['address_secondary_id'])) {
                                $data['address_billing_secondary_id'] = $address['address_id'];
                                $data['address_billing_secondary_street'] = $address['address_street'];
                                $data['address_billing_secondary_zip'] = $address['address_zip'];
                                $data['address_billing_secondary_city'] = $address['address_city'];
                                $data['address_billing_secondary_area'] = $address['address_area'];
                                $data['address_billing_secondary_state'] = $address['address_state'];
                                $data['address_billing_secondary_country_code'] = $address['address_country_code'];
                            }
                            break;
                        case 'OTHER':
                            if (!isset($data['address_other_id'])) {
                                $data['address_other_id'] = $address['address_id'];
                                $data['address_other_street'] = $address['address_street'];
                                $data['address_other_zip'] = $address['address_zip'];
                                $data['address_other_city'] = $address['address_city'];
                                $data['address_other_area'] = $address['address_area'];
                                $data['address_other_state'] = $address['address_state'];
                                $data['address_other_country_code'] = $address['address_country_code'];
                            }
                            elseif (!isset($data['address_secondary_id'])) {
                                $data['address_other_secondary_id'] = $address['address_id'];
                                $data['address_other_secondary_street'] = $address['address_street'];
                                $data['address_other_secondary_zip'] = $address['address_zip'];
                                $data['address_other_secondary_city'] = $address['address_city'];
                                $data['address_other_secondary_area'] = $address['address_area'];
                                $data['address_other_secondary_state'] = $address['address_state'];
                                $data['address_other_secondary_country_code'] = $address['address_country_code'];
                            }
                            break;
                    }
                }
            }
        }



        echo "<pre>";
        print_r($contact);
        print_r($data);
        echo "</pre>";
    }

    /**
     * Get the Contact form, depending on the settings in the field array.
     *
     * @see /Contact/config.contact.json - [pattern][form][contact][field]
     * @param unknown $data
     * @param unknown $field
     * @return boolean
     */
    public function getFormContact($data=array(), $field=array())
    {
        if (!is_array($field) || empty($field)) {
            // use the default configuration from config.contact.json
            $field = self::$config['pattern']['form']['contact']['field'];
        }

        if (!isset($field['predefined']) || !isset($field['visible']) || !isset($field['hidden']) || !isset($field['required'])) {
            $this->setAlert('Missing one or more keys in the field definition array! At least are needed: predefined, visible, hidden, required',
                array(), self::ALERT_TYPE_DANGER);
            return false;
        }

        $data['contact_id'] = (isset($data['contact_id'])) ? intval($data['contact_id']) : -1;
        $data['contact_type'] = (isset($data['contact_type'])) ? $data['contact_type'] : 'PERSON';

        // create the form
        $form = $this->app['form.factory']->createBuilder('form');

        // loop through the hidden fields
        foreach ($field['hidden'] as $hidden) {
            $form->add($hidden, 'hidden', array(
                'data' => isset($data[$hidden]) ? $data[$hidden] : null
            ));
        }

        $TagType = new TagType($this->app);
        $ExtraType = new ExtraType($this->app);
        $ExtraCategory = new ExtraCategory($this->app);

        // loop through the visible fields
        foreach ($field['visible'] as $visible) {
            switch ($visible) {
                case 'tags':
                    // contact tags
                    if (isset($field['predefined']['tags']) && is_array($field['predefined']['tags'])) {
                        $tags = array();
                        foreach ($field['predefined']['tags'] as $tag) {
                            if ((false !== ($id = filter_var($tag, FILTER_VALIDATE_INT))) &&
                                (false !== ($type = $TagType->select($id))) &&
                                !array_key_exists($type['tag_type_id'], $tags)) {
                                $tags[$type['tag_name']] = $this->app['utils']->humanize($type['tag_name']);
                            }
                            elseif ((false !== ($type = $TagType->selectByName(trim($tag)))) &&
                                    !array_key_exists($type['tag_type_id'], $tags)) {
                                $tags[$type['tag_name']] = $this->app['utils']->humanize($type['tag_name']);
                            }
                        }
                    }
                    else {
                        $tags = $this->app['contact']->getTagArrayForTwig();
                    }
                    $form->add($visible, 'choice', array(
                        'choices' => $tags,
                        'multiple' => true,
                        'expanded' => true,
                        'data' => (isset($data[$visible]) && is_array($data[$visible])) ? $data[$visible] : null
                    ));
                    break;
                case 'person_gender':
                    if ($data['contact_type'] == 'COMPANY') {
                        break;
                    }
                    $form->add($visible, 'choice', array(
                        'required' => in_array($visible, $field['required']),
                        'choices' => array('MALE' => 'Male', 'FEMALE' => 'Female'),
                        'expanded' => true,
                        'data' => isset($data[$visible]) ? $data[$visible] : 'MALE'
                    ));
                    break;
                case 'person_title':
                    if ($data['contact_type'] == 'COMPANY') {
                        break;
                    }
                    $form->add($visible, 'choice', array(
                        'choices' => $this->app['contact']->getTitleArrayForTwig(),
                        'empty_value' => '- please select -',
                        'required' => in_array($visible, $field['required']),
                        'data' => isset($data[$visible]) ? $data[$visible] : null
                    ));
                    break;
                case 'person_birthday':
                    if ($data['contact_type'] == 'COMPANY') {
                        break;
                    }
                    $form->add($visible, 'text', array(
                        'required' => in_array($visible, $field['required']),
                        'data' => isset($data[$visible]) ? $data[$visible] : '',
                        'attr' => array('class' => 'datepicker')
                    ));
                    break;
                case 'communication_email':
                case 'communication_phone':
                case 'communication_cell':
                case 'communication_fax':
                    $form->add($visible.'_id', 'hidden', array(
                        'data' => isset($data[$visible.'_id']) ? $data[$visible.'_id'] : -1
                    ));
                    $form->add($visible, 'text', array(
                        'required' => in_array($visible, $field['required']),
                        'data' => isset($data[$visible]) ? $data[$visible] : ''
                    ));
                    break;
                case 'address_country_code':
                    $form->add($visible, 'choice', array(
                        'required' => in_array($visible, $field['required']),
                        'choices' => $this->app['contact']->getCountryArrayForTwig(),
                        'empty_value' => '- please select -',
                        'data' => isset($data[$visible]) ? $data[$visible] : '',
                        'preferred_choices' => self::$config['countries']['preferred']
                    ));
                    break;
                case 'note_content':
                    $form->add('note_id', 'hidden', array(
                        'data' => isset($data['note_id']) ? $data['note_id'] : -1
                    ));
                    $form->add($visible, 'textarea', array(
                        'required' => in_array($visible, $field['required']),
                        'data' => isset($data[$visible]) ? $data[$visible] : ''
                    ));
                    break;
                case 'extra_fields':
                    // 'extra_fields' mean to show all 'extra_xxx' fields !!!
                    $type_ids = $ExtraCategory->selectTypeIDByCategoryTypeID(
                        isset($data['category_type_id']) ? $data['category_type_id'] : -1);
                    foreach ($type_ids as $type_id) {
                        // get the extra field specification
                        if (false !== ($extra = $ExtraType->select($type_id))) {
                            $name = 'extra_'.strtolower($extra['extra_type_name']);
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
                                'data' => isset($data[$name]) ? $data['name'] : null
                            ));
                        }
                    }
                    break;
                case 'person_first_name':
                case 'person_last_name':
                case 'person_nick_name':
                case 'company_name':
                case 'company_department':
                case 'address_street':
                case 'address_zip':
                case 'address_city':
                case 'address_area':
                case 'address_state':
                    // default text fields
                    if (($data['contact_type'] == 'COMPANY' && in_array($visible, self::$person_fields)) ||
                        ($data['contact_type'] == 'PERSON') && in_array($visible, self::$company_fields)) {
                        break;
                    }
                    $form->add($visible, 'text', array(
                        'required' => in_array($visible, $field['required']),
                        'data' => isset($data[$visible]) ? $data[$visible] : ''
                    ));
                    break;
                default:
                    if (stripos($visible, 'extra_') == 0) {
                        // possibly an extra field!
                        $name = strtolower(substr($visible, strlen('extra_')));
                        if (false !== ($type = $ExtraCategory->selectTypebyNameAndCategory($name,
                            isset($data['category_type_id']) ? $data['category_type_id'] : -1))) {
                            $extra_type = null;
                            switch ($type['extra_type_type']) {
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
                                'label' => $this->app['utils']->humanize($type['extra_type_name']),
                                'data' => isset($data[$name]) ? $data['name'] : null
                            ));
                        }
                        else {
                            $this->setAlert('The field %field% is unknown, please check the configuration!',
                                array('%field%' => $visible), self::ALERT_TYPE_DANGER);
                            return false;
                        }
                    }
                    else {
                        // unknown field
                        $this->setAlert('The field %field% is unknown, please check the configuration!',
                            array('%field%' => $visible), self::ALERT_TYPE_DANGER);
                        return false;
                    }
                    break;
            }
        }

        // return the form
        return $form->getForm();
    }
}

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

/**
 * Class ContactRegister
 * @package phpManufaktur\Contact\Control\Command
 */
class ContactRegister extends Basic
{
    protected $app = null;
    protected static $contact_type = null;
    protected static $category_type_id = null;
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
        if (isset(self::$parameter['category'])) {
            $CategoryType = new CategoryType($app);
            $cats = strpos(self::$parameter['category'], ',') ? explode(',', self::$parameter['category']) : array(self::$parameter['category']);
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
            self::$parameter['category'] = $category;
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

    protected function getFormSelectCategory()
    {
        return $this->app['form.factory']->createBuilder('form')
        ->add('contact_type', 'hidden', array(
            'data' => self::$contact_type
        ))
        ->add('category_type_id', 'choice', array(
            'choices' => self::$parameter['category'],
            'empty_value' => false,
            'multiple' => false,
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
            'data' => self::$contact_type
        ))
        ->add('category_type_id', 'hidden', array(
            'data' => self::$category_type_id
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
                    'data' => isset($data['person_birthday']) ? $data['person_birthday'] : ''
                ));
            }
        }
        else {
            // COMPANY CONTACT

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
        if (!in_array('note', $field['unused'])) {
            $form->add('note_id', 'hidden', array(
                'data' => isset($data['note_id']) ? $data['note_id'] : -1
            ));
            $form->add('note', 'textarea', array(
                'required' => in_array('note', $field['required']),
                'data' => isset($data['note']) ? $data['note'] : ''
            ));
        }

        // EXTRA FIELDS
        /*
        if (isset($contact['extra_fields'])) {
            foreach ($contact['extra_fields'] as $field) {
                $name= 'extra_'.strtolower($field['extra_type_name']);
                switch ($field['extra_type_type']) {
                    // determine the form type for the extra field
                    case 'TEXT':
                        $form_type = 'textarea';
                        break;
                    case 'HTML':
                        $form_type = 'textarea';
                        break;
                    default:
                        $form_type = 'text';
                        break;
                }

                // add the form field for the extra field
                $form->add($name, $form_type, array(
                    'attr' => array('class' => $name),
                    'data' => $field['extra_value'],
                    'label' => ucfirst(str_replace('_', ' ', strtolower($field['extra_type_name']))),
                    'required' => false
                ));

                // extra info for the Twig handling
                $extra_info[] = array(
                    'name' => $name,
                    'field' => $field
                );
            }
        }
        */

        return $form->getForm();
    }

    protected function checkContactData(&$data=array())
    {
        return false;
    }

    public function ControllerRegisterContactCheck(Application $app)
    {
        $this->initParameters($app);

        $form = $this->getFormContactData();

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                $contact = $form->getData();
                if ($this->checkContactData($contact)) {
                    // contact data are ok - send confirmation mails and say goodbye ...

                    return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                        '@phpManufaktur/Contact/Template', 'command/register.contact.submitted.twig',
                        $this->getPreferredTemplateStyle()),
                        array(
                            'basic' => $this->getBasicSettings(),
                            'contact' => $contact
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

    public function registerContact()
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


    public function ControllerRegisterCategoryCheck(Application $app)
    {
        return __METHOD__;
    }

    public function registerCategory()
    {
        if (count(self::$parameter['category']) == 1) {
            // we have exacly one category - the key contains the ID ...
            reset(self::$parameter['category']);
            self::$category_type_id = key(self::$parameter['category']);
            return $this->registerContact();
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
     * @param Application $app
     * @return string
     */
    public function ControllerRegister(Application $app)
    {
        $this->initParameters($app);

        if (empty(self::$parameter['category'])) {
            $this->setAlert('Please use the parameter <em>category[]</em> to specify at minimum one category with PUBLIC access!',
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
}

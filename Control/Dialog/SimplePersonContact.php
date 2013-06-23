<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Dialog;

use Silex\Application;
use phpManufaktur\Contact\Data\Contact\Title;
use phpManufaktur\Contact\Data\Contact\Country;
use phpManufaktur\Contact\Control\Contact as ContactControl;

class SimplePersonContact
{

    protected $app = null;
    protected static $contact_id = -1;
    protected static $message = '';

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        // set the content language
        $this->app['translator']->setLocale('de');
    }

    /**
     * @return the $message
     */
    public function getMessage()
    {
        return self::$message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message, $params=array())
    {
        self::$message .= $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'message.twig'),
            array('message' => $this->app['translator']->trans($message, $params)));
    }

    /**
     * Check if a message is active
     *
     * @return boolean
     */
    public function isMessage()
    {
        return !empty(self::$message);
    }

    /**
     * Clear the existing message(s)
     */
    public function clearMessage()
    {
        self::$message = '';
    }

    protected function getForm($data=array())
    {

        // get the title array
        $title = new Title($this->app);
        $title_array = $title->getArrayForTwig();

        // create array for the birthday years
        $years = array();
        for ($i = date('Y')-18; $i > (date('Y')-100); $i--) {
            $years[] = $i;
        }
        $birthday_array = array(
            'label' => 'Birthday',
            'format' => 'ddMMyyyy',
            'years' => $years,
            'required' => false
        );
        if (isset($address['birthday'])) {
            $birthday_array['data'] = $data['birthday'];
        }
        else {
            $birthday_array['empty_value'] = '';
        }

        // get the country array
        $country = new Country($this->app);
        $country_array = $country->getArrayForTwig();

        return $this->app['form.factory']->createBuilder('form', $data)
            ->add('type', 'hidden', array(
                'data' => 'PERSON'
            ))
            ->add('contact_id', 'text', array(
                'read_only' => true,
                'required' => false,
            ))
            ->add('person_id', 'text', array(
                'read_only' => true,
                'required' => false
            ))
            ->add('gender', 'choice', array(
                'choices' => array('MALE' => 'male', 'FEMALE' => 'female'),
                'expanded' => true,
            ))
            ->add('title', 'choice', array(
                'choices' => $title_array,
                'expanded' => false,
                'multiple' => false,
                'required' => false
            ))
            ->add('first_name', 'text', array(
                'required' => false
            ))
            ->add('last_name')
            ->add('nick_name', 'text', array(
                'required' => false
            ))
            ->add('birthday', 'date', $birthday_array)
            ->add('phone', 'text', array(
                'required' => false
            ))
            ->add('fax', 'text', array(
                'required' => false
            ))
            ->add('email', 'text', array(

            ))
            ->add('street', 'text', array(
                'required' => false
            ))
            ->add('zip', 'text', array(
                'required' => false
            ))
            ->add('city', 'text', array(
                'required' => false
            ))
            ->add('country', 'choice', array(
                'choices' => $country_array,
                'expanded' => false,
                'multiple' => false,
                'required' => false
            ))
            ->getForm();
    }

    public function exec()
    {
        $Contact = new ContactControl($this->app);
        $data = $Contact->select(self::$contact_id);
        if ('POST' == $this->app['request']->getMethod()) {
            // form was submitted
            $form = $this->getForm();
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                $data = $form->getData();
                // check the data
                $this->clearMessage();
                if (!$Contact->validate($data)) {
                    self::$message = $Contact->getMessage();
                }
                if (!$this->isMessage()) {
                    // ok - insert or update the data
                    if (!$Contact->insert($data, self::$contact_id)) {
                        self::$message = $Contact->getMessage();
                    }
                    echo "HURRY";
                }
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setMessage('The form is not valid, please check your input and try again!');
            }
        }

        $form = $this->getForm($data);
        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'simple.person.contact.twig'),
            array(
                'message' => $this->getMessage(),
                'form' => $form->createView()
            ));
    }
}
<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Dialog\Simple;

use Silex\Application;
use Symfony\Component\Form\FormBuilder;
use phpManufaktur\Contact\Control\Helper\ContactTagType as TagTypeControl;

class TagType extends Dialog {

    protected $app = null;
    protected $TagTypeControl = null;
    protected static $tag_type_id = -1;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        parent::__construct($app);
        $this->TagTypeControl = new TagTypeControl($this->app);
    }

    public function setTagTypeID($tag_type_id)
    {
        self::$tag_type_id = $tag_type_id;
    }

    /**
     * Build the complete form with the form.factory
     *
     * @param array $contact flatten contact record
     * @return FormBuilder
     */
    protected function getForm($tag_type)
    {
       $form = $this->app['form.factory']->createBuilder('form', $tag_type)
            ->add('tag_type_id', 'hidden')
            ->add('tag_name', 'text', array(
                'required' => true,
                'read_only' => (isset($tag_type['tag_name']) && !empty($tag_type['tag_name'])) ? true : false,
                'label' => 'Name'
            ))
            ->add('tag_description', 'textarea', array(
                'required' => false,
                'label' => 'Description'
            ));
        return $form->getForm();
    }

    /**
     * Return the complete contact dialog and handle requests
     *
     * @return string contact dialog
     */
    public function exec()
    {
        // check if a TAG ID isset
        $form_request = $this->app['request']->request->get('form', array());
        if (isset($form_request['tag_type_id'])) {
            self::$tag_type_id = $form_request['tag_type_id'];
        }

        // get the tag record
        if (false === ($tag_type = $this->TagTypeControl->select(self::$tag_type_id))) {
            $tag_type = $this->TagTypeControl->getDefaultRecord();
        }

        // get the form
        $form = $this->getForm($tag_type);

        if ('POST' == $this->app['request']->getMethod()) {
            $delete = $this->app['request']->get('delete', null);
            if (!is_null($delete)) {
                // delete this tag
                $this->TagTypeControl->delete(self::$tag_type_id);
                if (!$this->TagTypeControl->isMessage()) {
                    $this->setMessage("The process has not returned a status message");
                }
                else {
                    // use the return status messages
                    self::$message = $this->TagTypeControl->getMessage();
                }
                self::$tag_type_id = -1;
                $tag_type = $this->TagTypeControl->getDefaultRecord();
                $form = $this->getForm($tag_type);
            }
            else {
                // the form was submitted, bind the request
                $form->bind($this->app['request']);
                if ($form->isValid()) {
                    // get the form data
                    $tag = $form->getData();
                    if (self::$tag_type_id < 1) {
                        // insert a new TAG
                        $this->TagTypeControl->insert($tag, self::$tag_type_id);
                    }
                    else {
                        // update an existing tag
                        $this->TagTypeControl->update($tag, self::$tag_type_id);
                    }
                    if (!$this->TagTypeControl->isMessage()) {
                        $this->setMessage("The process has not returned a status message");
                    }
                    else {
                        // use the return status messages
                        self::$message = $this->TagTypeControl->getMessage();
                    }
                    // get the changed tag record
                    if (false === ($tag_type = $this->TagTypeControl->select(self::$tag_type_id))) {
                        $tag_type = $this->TagTypeControl->getDefaultRecord();
                    }
                    // get the form
                    $form = $this->getForm($tag_type);
                }
                else {
                    // general error (timeout, CSFR ...)
                    $this->setMessage('The form is not valid, please check your input and try again!');
                }
            }
        }

        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'backend/simple/tag.type.twig'),
            array(
                'message' => $this->getMessage(),
                'form' => $form->createView(),
                'tag_type_id' => self::$tag_type_id
            ));
    }
}
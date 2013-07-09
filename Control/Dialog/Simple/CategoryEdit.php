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
use phpManufaktur\Contact\Data\Contact\CategoryType;

class CategoryEdit extends Dialog {

    protected $CategoryTypeData = null;
    protected static $category_id = -1;

    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app, $options=null)
    {
        parent::__construct($app);

        $this->setOptions(array(
            'template' => array(
                'namespace' => isset($options['template']['namespace']) ? $options['template']['namespace'] : '@phpManufaktur/Contact/Template',
                'message' => isset($options['template']['message']) ? $options['template']['message'] : 'backend/message.twig',
                'edit' => isset($options['template']['list']) ? $options['template']['list'] : 'backend/simple/category.edit.twig'
            ),
            'route' => array(
                'action' => isset($options['route']['edit']) ? $options['route']['edit'] : '/admin/contact/simple/category/edit'
            )
        ));
        $this->CategoryTypeData = new CategoryType($this->app);
    }


    /**
     * @return the $category_id
     */
    public static function getCategoryID()
    {
        return self::$category_id;
    }

  	/**
     * @param number $category_id
     */
    public static function setCategoryID($category_id)
    {
        self::$category_id = $category_id;
    }

    /**
     * Use form.factory to create the form for the categories
     *
     * @param form $category
     */
    protected function getForm($category)
    {
        return $this->app['form.factory']->createBuilder('form', $category)
            ->add('category_type_id', 'hidden')
            ->add('category_type_name', 'text', array(
                'label' => 'Category name',
                'read_only' => ($category['category_type_id'] > 0) ? true : false
            ))
            ->add('category_type_description', 'textarea', array(
                'label' => 'Category description',
                'required' => false
            ))
            ->getForm();
    }

    /**
     * Return the category record for the actual category ID or a default record
     *
     * @return multitype:number string
     */
    protected function getCategory()
    {
        if (self::$category_id > 0) {
            if (false === ($category = $this->CategoryTypeData->select(self::$category_id))) {
                $this->setMessage('The category type with the ID %category_id% does not exists!',
                    array('%category_id%' => self::$category_id));
                self::$category_id = -1;
            }
        }

        if (self::$category_id < 1) {
            // set default values
            $category = array(
                'category_type_id' => -1,
                'category_type_name' => '',
                'category_type_description' => ''
            );
        }
        return $category;
    }

	  /**
     * Return the Categroy edit dialog
     *
     * @return string category list
     */
    public function exec($extra=null)
    {
        // check if a contact ID isset
        $form_request = $this->app['request']->request->get('form', array());
        if (isset($form_request['category_type_id'])) {
            self::$category_id = $form_request['category_type_id'];
        }

        // get the form with the actual category ID
        $form = $this->getForm($this->getCategory());

        if ('POST' == $this->app['request']->getMethod()) {
            // the form was submitted, bind the request
            $form->bind($this->app['request']);
            if ($form->isValid()) {
                $category = $form->getData();
                if (!is_null($this->app['request']->request->get('delete', null))) {
                    // delete the category
                    $this->CategoryTypeData->delete($category['category_type_id']);
                    $this->setMessage('The category %category_name% was successfull deleted.',
                        array('%category_name%' => $category['category_type_name']));
                    self::$category_id = -1;
                }
                else {
                    // insert or edit a category
                    if ($category['category_type_id'] > 0) {
                        // update the record
                        $data = array(
                            'category_type_description' => !is_null($category['category_type_description']) ? $category['category_type_description'] : ''
                        );
                        $this->CategoryTypeData->update($data, self::$category_id);
                        $this->setMessage('The category %category_name% was successfull updated',
                            array('%category_name%' => $category['category_type_name']));
                    }
                    else {
                        // insert a new record
                        $category_name = str_replace(' ', '_', strtoupper(trim($category['category_type_name'])));
                        $matches = array();
                        if (preg_match_all('/[^A-Z0-9_$]/', $category_name, $matches)) {
                            // name check fail
                            $this->setMessage('Allowed characters for the category name are only A-Z, 0-9 and the Underscore. The name will be always converted to uppercase.');
                        }
                        else {
                            // insert the record
                            $data = array(
                                'category_type_name' => $category_name,
                                'category_type_description' => !is_null($category['category_type_description']) ? $category['category_type_description'] : ''
                            );
                            $this->CategoryTypeData->insert($data, self::$category_id);
                            $this->setMessage('The category %category_name% was successfull inserted.',
                                array('%category_name%' => $category_name));
                        }
                    }
                }
                // get the form with the actual category ID
                $form = $this->getForm($this->getCategory());
            }
            else {
                // general error (timeout, CSFR ...)
                $this->setMessage('The form is not valid, please check your input and try again!');
            }
        }

        return $this->app['twig']->render($this->app['utils']->templateFile(self::$options['template']['namespace'], self::$options['template']['edit']),
            array(
                'message' => $this->getMessage(),
                'form' => $form->createView(),
                'route' => self::$options['route'],
                'extra' => $extra
            ));
    }
}
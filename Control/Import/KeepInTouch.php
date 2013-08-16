<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/FacebookGallery
 * @copyright 2013 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Import;

use Silex\Application;
use phpManufaktur\Contact\Data\Import\KeepInTouch\KeepInTouch as Data;

class KeepInTouch extends Dialog {

    protected static $kit_release = null;
    protected static $import_is_possible = false;
    protected $Data = null;

    /**
     * Initialize the class
     *
     * @see \phpManufaktur\Contact\Control\Import\Dialog::initialize()
     */
    protected function initialize(Application $app)
    {
        parent::initialize($app);
        $this->Data = new Data($app);
        if ($this->Data->existsKIT()) {
            // KIT exists, check the version
            self::$kit_release = $this->Data->getKITrelease();
            if (!is_null(self::$kit_release)) {
                self::$import_is_possible = version_compare(self::$kit_release, '0.72', '>=');
            }
        }
    }

    /**
     * First step to import data from a KIT1 installation
     *
     * @param Application $app
     * @return string rendered dialog
     */
    public function start(Application $app)
    {
        // initialize the class
        $this->initialize($app);

        if (self::$import_is_possible) {
            $this->setMessage('Detected a KeepInTouch installation (Release: %release%) with %count% active or locked contacts.',
                array('%release%' => self::$kit_release, '%count%' => $this->Data->countKITrecords()));
        }
        else {
            $this->setMessage('There exists no KeepInTouch installation at the parent CMS!');
        }

        $output = "Clean123 äöu--/()this copy of invalid non ASCII äócharacters.";
        $output = preg_replace('/[^(\x20-\x7F)]*/','', $output);
        echo($output);

        $this->Data->getKITrecord(18);
        return $this->app['twig']->render($this->app['utils']->templateFile('@phpManufaktur/Contact/Template', 'backend/import/start.keepintouch.twig'),
            array(
                'message' => $this->getMessage()
            ));
    }
}

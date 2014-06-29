<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://addons.phpmanufaktur.de/event
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control\Export;

use Silex\Application;
use phpManufaktur\Basic\Control\Pattern\Alert;

class Controller extends Alert
{
    /**
     * Controller to select the type and mode for export
     *
     * @param Application $app
     */
    public function Controller(Application $app)
    {
        $this->initialize($app);

        $this->setAlert('Please select the target file format to export the kitFramework Contact records: <a href="%xlsx%">XLSX (Excel)</a> or <a href="%csv%">CSV (Text)</a>.',
            array('%xlsx%' => FRAMEWORK_URL.'/admin/contact/export/excel', '%csv%' => FRAMEWORK_URL.'/admin/contact/export/csv'),
            self::ALERT_TYPE_INFO);
        return $this->promptAlertFramework();
    }
}

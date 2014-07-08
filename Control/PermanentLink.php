<?php

/**
 * Contact
 *
 * @author Team phpManufaktur <team@phpmanufaktur.de>
 * @link https://kit2.phpmanufaktur.de/contact
 * @copyright 2014 Ralf Hertsch <ralf.hertsch@phpmanufaktur.de>
 * @license MIT License (MIT) http://www.opensource.org/licenses/MIT
 */

namespace phpManufaktur\Contact\Control;

use Silex\Application;
use phpManufaktur\Basic\Data\CMS\Page;
use phpManufaktur\Basic\Data\kitCommandParameter;
use phpManufaktur\Basic\Data\CMS\Users;

class PermanentLink
{
    protected $app = null;

    /**
     * Execute cURL to catch the CMS content into the permanent link
     *
     * @param string $url
     * @return mixed
     */
    protected function cURLexec($url)
    {
        // init cURL
        $ch = curl_init();

        // set the general cURL options
        $options = array(
            CURLOPT_HEADER => false,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'kitFramework::Contact',
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        );

        if (is_null($this->app['session']->get('CONTACT_COOKIE_FILE')) ||
            !$this->app['filesystem']->exists($this->app['session']->get('CONTACT_COOKIE_FILE'))) {
            // this is the first call of this cURL session, create a cookie file
            $this->app['session']->set('CONTACT_COOKIE_FILE', FRAMEWORK_TEMP_PATH.'/session/'.uniqid('contact_'));
            $options[CURLOPT_COOKIEJAR] = $this->app['session']->get('CONTACT_COOKIE_FILE');
        }
        else {
            // load the existing cookie file
            $options[CURLOPT_COOKIEFILE] = $this->app['session']->get('CONTACT_COOKIE_FILE');
        }

        $PageData = new Page($this->app);

        // get the CMS page link from the $url
        $link = substr($url, strlen(CMS_URL));
        $link = substr($link, strlen($PageData->getPageDirectory()));
        $link = substr($link, 0, strpos($link, $PageData->getPageExtension()));

        // get the PAGE_ID
        if (false === ($page_id = $PageData->getPageIDbyPageLink($link))) {
            $this->app['monolog']->addError("Can't get the PAGE_ID from the $url!", array(__METHOD__, __LINE__));
            $this->app->abort(403, 'Access denied');
        }

        // get the visibility of the target page
        $visibility = $PageData->getPageVisibilityByPageID($page_id);
        if ($visibility == 'none') {
            // page can not be shown!
            $error = 'The visibility of the requested page is "none", can not show the content!';
            $this->app['monolog']->addError($error, array(__METHOD__, __LINE__));
            return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                '@phpManufaktur/Basic/Template', 'kitcommand/bootstrap/noframe/alert.twig'),
                array(
                    'content' => $this->app['translator']->trans($error),
                    'type' => 'alert-danger'));
        }
        elseif (in_array($visibility, array('registered', 'private'))) {
            // user must be authenticated!
            if (is_null($this->app['session']->get('CMS_USERNAME')) && !is_null($this->app['request']->query->get('pid'))) {
                $kitCommandParameter = new kitCommandParameter($this->app);
                if (false !== ($parameter = $kitCommandParameter->selectParameter($this->app['request']->query->get('pid')))) {
                    if (isset($parameter['cms']['user']['name']) && !empty($parameter['cms']['user']['name'])) {
                        // set the session username from the PID
                        $Users = new Users($this->app);
                        if (false !== ($user = $Users->selectUser($parameter['cms']['user']['name']))) {
                            // authenticate the user
                            $options[CURLOPT_URL] = MANUFAKTUR_URL.'/Basic/Control/CMS/Authenticate.php';
                            $options[CURLOPT_POST] = true;
                            $options[CURLOPT_POSTFIELDS] = array('username' => $user['username'], 'password' => $user['password']);

                            curl_setopt_array($ch, $options);

                            // set proxy if needed
                            $this->app['utils']->setCURLproxy($ch);

                            if (false === ($result = curl_exec($ch))) {
                                // cURL error
                                $error = 'cURL error: '.curl_error($ch);
                                $this->app['monolog']->addError($error, array(__METHOD__, __LINE__));
                                return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                                    '@phpManufaktur/Basic/Template', 'kitcommand/bootstrap/noframe/alert.twig'),
                                    array(
                                        'content' => $error,
                                        'type' => 'alert-danger'));
                            }
                            if ($result == $user['username']) {
                                $this->app['session']->set('CMS_USERNAME', $parameter['cms']['user']['name']);
                            }
                        }
                    }
                }
            }

            if (is_null($this->app['session']->get('CMS_USERNAME'))) {
                // user is not logged in
                $options[CURLOPT_URL] = CMS_URL.'/account/login.php';
                $options[CURLOPT_FOLLOWLOCATION] = true;
                // follow the location to show the content
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = array('redirect' => $url);

                curl_setopt_array($ch, $options);

                // set proxy if needed
                $this->app['utils']->setCURLproxy($ch);

                if (false === ($result = curl_exec($ch))) {
                    // cURL error
                    $error = 'cURL error: '.curl_error($ch);
                    $this->app['monolog']->addError($error, array(__METHOD__, __LINE__));
                    return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                        '@phpManufaktur/Basic/Template', 'kitcommand/bootstrap/noframe/alert.twig'),
                        array(
                            'content' => $error,
                            'type' => 'alert-danger'));
                }
                curl_close($ch);
                return $result;
            }
            else {
                // authenticate the user by the saved name in session
                $Users = new Users($this->app);
                if (false !== ($user = $Users->selectUser($this->app['session']->get('CMS_USERNAME')))) {
                    // authenticate the user
                    $options[CURLOPT_URL] = MANUFAKTUR_URL.'/Basic/Control/CMS/Authenticate.php';
                    $options[CURLOPT_POST] = true;
                    $options[CURLOPT_POSTFIELDS] = array('username' => $user['username'], 'password' => $user['password']);

                    curl_setopt_array($ch, $options);

                    // set proxy if needed
                    $this->app['utils']->setCURLproxy($ch);

                    if (false === ($result = curl_exec($ch))) {
                        // cURL error
                        $error = 'cURL error: '.curl_error($ch);
                        $this->app['monolog']->addError($error, array(__METHOD__, __LINE__));
                        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                            '@phpManufaktur/Basic/Template', 'kitcommand/bootstrap/noframe/alert.twig'),
                            array(
                                'content' => $error,
                                'type' => 'alert-danger'));
                    }
                }
            }
        }

        // add the URL to the options
        $options[CURLOPT_URL] = $url;

        curl_setopt_array($ch, $options);

        // set proxy if needed
        $this->app['utils']->setCURLproxy($ch);

        if (false === ($result = curl_exec($ch))) {
            // cURL error
            $error = 'cURL error: '.curl_error($ch);
            $this->app['monolog']->addError($error, array(__METHOD__, __LINE__));
            return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                '@phpManufaktur/Basic/Template', 'kitcommand/bootstrap/noframe/alert.twig'),
                array(
                    'content' => $error,
                    'type' => 'alert-danger'));
        }

        if (!curl_errno($ch)) {
            $info = curl_getinfo($ch);
            if ($info['http_code'] > 299) {
                // bad request
                $error = 'Error - HTTP Status Code: '.$info['http_code'].' - '.$url;
                $this->app['monolog']->addError($error, array(__METHOD__, __LINE__));
                return $this->app['twig']->render($this->app['utils']->getTemplateFile(
                    '@phpManufaktur/Basic/Template', 'kitcommand/bootstrap/noframe/alert.twig'),
                    array(
                        'content' => $error,
                        'type' => 'alert-danger'));
            }
        }

        curl_close($ch);
        return $result;
    }


    public function ControllerPublicViewID(Application $app, $contact_id=null)
    {
        $this->app = $app;

        if (!filter_var($contact_id, FILTER_VALIDATE_INT, array('options' => array('min_range' => 1)))) {
            $app['monolog']->addDebug('Access denied, contact_id is no integer value', array(__METHOD__));
            $app->abort(403, 'Access denied');
        }

        if (!$app['contact']->isActive($contact_id) || !$app['contact']->isPublic($contact_id)) {
            $app['monolog']->addDebug("Access denied, contact_id $contact_id is not active or not public.", array(__METHOD__));
            $app->abort(403, 'Access denied');
        }

        if (false === ($target_url = $app['contact']->getCategoryTargetURL($contact_id))) {
            $app['monolog']->addError("Missing the Category Target URL for the Contact ID $contact_id - access denied for the user!",
                array(__METHOD__));
            $app->abort(403, 'Access denied');
        }

        // create the parameter array
        $parameter = array(
            'command' => 'contact',
            'action' => 'view',
            'contact_id' => $contact_id,
            'canonical' => FRAMEWORK_URL."/contact/public/view/id/$contact_id",
        );

        // gather all GET parameter
        $gets = $app['request']->query->all();
        if (is_array($gets)) {
            foreach ($gets as $key => $value) {
                if (!in_array($key, array('pid'))) {
                    $parameter[$key] = $value;
                }
            }
        }

        // create the target URL and set the needed parameters
        $target_url = $target_url.'?'.http_build_query($parameter, '', '&');

        return $this->cURLexec($target_url);
    }
}

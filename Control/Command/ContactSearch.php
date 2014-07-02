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
use phpManufaktur\Contact\Data\Contact\Contact;
use phpManufaktur\Contact\Data\Contact\TagType;

class ContactSearch extends Basic
{
    protected static $list_configuration = null;

    protected function initParameters(Application $app, $parameter_id=-1)
    {
        parent::initParameters($app, $parameter_id);

        // read the list.contact.json
        $config_path = $app['utils']->getTemplateFile('@phpManufaktur/Contact/Template',
            'command/list.contact.json', $this->getPreferredTemplateStyle(), true);
        self::$list_configuration = $app['utils']->readJSON($config_path);
    }

    public function ControllerSearch(Application $app)
    {
        $this->initParameters($app);

        $parameter = $this->getCommandParameters();
        $tags = array();
        $use_tags = false;
        if (isset($parameter['tags'])) {
            $use_tags = true;
            if (!empty(trim($parameter['tags']))) {
                $TagTypeData = new TagType($app);
                $tag_array = strpos($parameter['tags'], ',') ? explode(',', $parameter['tags']) : array($parameter['tags']);
                if (!empty($tag_array)) {
                    foreach ($tag_array as $tag) {
                        $tag = strtoupper(trim($tag));
                        if ($TagTypeData->existsTag($tag)) {
                            $tags[] = $tag;
                        }
                    }
                }
            }
        }
        $contacts = array();

        if ('POST' == $this->app['request']->getMethod()) {
            $search = $app['request']->get('search');
            $search_tags = $tags;
            if ($use_tags && empty($search_tags)) {
                if ((null !== ($tag = $app['request']->get('tag'))) && ($tag != -1)) {
                    $search_tags = array($tag);
                }
            }
            $Contact = new Contact($app);
            $contacts = $Contact->searchPublicContact($search, $search_tags);
        }

        $tag_select = $app['contact']->getTagArrayForTwig();

        // no extra space for the iframe
        $this->setFrameAdd(300);

        return $this->app['twig']->render($this->app['utils']->getTemplateFile(
            '@phpManufaktur/Contact/Template', 'command/search.contact.twig',
            $this->getPreferredTemplateStyle()),
            array(
                'basic' => $this->getBasicSettings(),
                'use_tags' => $use_tags,
                'tags' => $tags,
                'tag_select' => $tag_select,
                'contacts' => $contacts,
                'columns' => self::$list_configuration['columns']
            ));
    }
}

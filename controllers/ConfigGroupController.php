<?php namespace Kitrix\Config;

use Kitrix\Config\Admin\Field;
use Kitrix\Config\API\Register;
use Kitrix\Config\ORM\ValuesTable;
use Kitrix\MVC\Admin\Controller;

class ConfigGroupController extends Controller
{

    public function edit($id) {

        $registry = Register::getInstance();
        $groups = $registry->getGroups();


        // not found group
        if (!in_array($id, array_keys($groups))) {
            $this->not_found();
        }

        $group = $groups[$id];
        $fields = $group->getFields();
        $dbValues = $registry->loadFields();

        // prepare
        $result = [];
        $result['title'] = $group->getTitle();
        $result['widgets'] = [];

        foreach ($fields as $field) {

            /** @var Field $field */

            $uniqId = $registry->getUniqueIdFromField($group, $field);
            $setting = $dbValues[$uniqId] ?: 0;

            $widget = $field->render($setting[ValuesTable::VALUE]);
            $result['widgets'][] = $widget;
        }

        $this->set('result', $result);
    }

}
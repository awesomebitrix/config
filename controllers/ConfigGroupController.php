<?php namespace Kitrix\Config;

use Kitrix\Entities\Admin\KitrixController;

class ConfigGroupController extends KitrixController
{

    public function index($id) {

        /** @var Config $plug */
        $plug = $this->getContext()->getCurrentPlugin();
        $groups = $plug->getRegisteredGroups();
        $config = $plug->getRegisteredFields();

        $fields = $config[$id];
        $group = $groups[$id];

        $this->set('id', $id);
        $this->set('fields', $fields);
        $this->set('group', $group);
    }

}
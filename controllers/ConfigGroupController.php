<?php namespace Kitrix\Config;

use Kitrix\MVC\Admin\Controller;

class ConfigGroupController extends Controller
{

    public function edit($id) {

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
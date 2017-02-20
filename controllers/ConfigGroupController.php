<?php namespace Kitrix\Config;

use Kitrix\Entities\Admin\Controller;

class ConfigGroupController extends Controller
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
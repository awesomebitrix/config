<?php namespace Kitrix\Config;

use Kitrix\Config\API\Register;
use Kitrix\MVC\Admin\Controller;

class ConfigGroupController extends Controller
{

    public function edit($id) {

        $registry = Register::getInstance();
        $this->set('groups', $registry->getGroups());
    }

}
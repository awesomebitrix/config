<?php namespace Kitrix\Config;

use Kitrix\Entities\Admin\KitrixController;

class ConfigGroupController extends KitrixController
{

    // todo bind params to action
    // todo add context object (with plugin and routing params)

    public function index($id) {

        $this->set('id', $id);
    }

}
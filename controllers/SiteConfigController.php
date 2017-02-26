<?php namespace Kitrix\Config;

use Kitrix\MVC\Admin\Controller;

class SiteConfigController extends Controller
{
    public function configList()
    {
        $helloWorld = ConfRegistry::getValue(Config::class, 'text');
        $this->set('message', $helloWorld);
    }

}
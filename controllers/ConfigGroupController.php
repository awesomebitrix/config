<?php namespace Kitrix\Config;

use Kitrix\Config\Admin\Field;
use Kitrix\Config\Admin\FormHelper;
use Kitrix\Config\API\Register;
use Kitrix\Config\Fields\Checkbox;
use Kitrix\Config\ORM\ValuesTable;
use Kitrix\MVC\Admin\Controller;
use Kitrix\MVC\Router;

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

        // prepare
        // ---------------
        $result = [];
        $result['title'] = $group->getTitle();
        $result['widgets'] = [];

        // process save
        // ---------------
        /** @var array $messages */
        $messages = [];
        $errorMessage = null;

        if ($_REQUEST['save'])
        {
            $status = $registry->updateValues($group, $_REQUEST);
            if (!$status->isSuccess()) {
                $messages = $status->getErrorMessages();
            }
        }

        if (count($messages))
        {
            $errorMessage = new \CAdminMessage($messages);
            $this->set('error', $errorMessage);
        }

        // render
        // ---------------
        $result['widgets'] = FormHelper::getWidgets($group);

        $this->set('result', $result);
        $this->set('post_url', Router::getInstance()->generateLinkTo('kitrix_config_edit_{id}', [
            'id' => $id
        ]));
    }

}
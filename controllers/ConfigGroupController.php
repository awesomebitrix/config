<?php namespace Kitrix\Config;

use Kitrix\Config\Admin\Field;
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
        $fields = $group->getFields();

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
            foreach ($fields as $field)
            {
                $uniqId = $registry->getUniqueIdFromField($group, $field);

                if (in_array($uniqId, array_keys($_REQUEST))) {

                    $value = $_REQUEST[$uniqId];
                    if ($field->getType() instanceof Checkbox)
                    {
                        $value = (bool)($value === 'Y' ? true : false);
                    }
                }
                else
                {
                    $value = 0;
                }

                // update row in db
                $status = ValuesTable::update($uniqId, [
                    ValuesTable::VALUE => $value
                ]);
                if (!$status->isSuccess())
                {
                    $messages = $status->getErrorMessages();
                    break;
                }
            }
        }

        if (count($messages))
        {
            $errorMessage = new \CAdminMessage($messages);
            $this->set('error', $errorMessage);
        }

        // render
        // ---------------
        $dbValues = $registry->loadFields(false);

        foreach ($fields as $field) {

            /** @var Field $field */

            $uniqId = $registry->getUniqueIdFromField($group, $field);
            $setting = $dbValues[$uniqId] ?: 0;

            $widget = $field->render($setting[ValuesTable::VALUE], $uniqId);
            $result['widgets'][] = $widget;
        }

        $this->set('result', $result);
        $this->set('post_url', Router::getInstance()->generateLinkTo('kitrix_config_edit_{id}', [
            'id' => $id
        ]));
    }

}
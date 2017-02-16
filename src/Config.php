<?php namespace Kitrix\Config;

use Kitrix\Config\Admin\Field;
use Kitrix\Config\Admin\FieldType;
use Kitrix\Config\Fields\Input;
use Kitrix\Entities\Admin\Route;
use Kitrix\Plugins\Plugin;

class Config extends Plugin
{
    private $_autoIncGroupId;
    private $_autoIncFieldId;

    private $registeredGroups;
    private $registeredFields;

    /**
     * Add config group
     *
     * @param $title
     * @return bool|int - return groupId if success, false - otherwise
     */
    public function addConfigGroup($title) {

        if (!$title) {
            return false;
        }

        $newGroupId = ++$this->_autoIncGroupId;
        $this->registeredGroups[$newGroupId] = $title;
        return $newGroupId;
    }

    /**
     * Add field to config group
     *
     * @param $groupId
     * @param FieldType $fieldType
     * @return bool|Field
     */
    public function addConfigField($groupId, FieldType $fieldType) {

        if (!$groupId) {
            return false;
        }

        $inputId = ++$this->_autoIncFieldId;
        $field = new Field($fieldType, $groupId, $inputId);
        $this->registeredFields[$groupId][$inputId] = $field;

        return $field;
    }

    public function run()
    {
        $coreGroup = $this->addConfigGroup('Основные настройки');
        if ($coreGroup) {

            $this
                ->addConfigField($coreGroup, new Input())
                ->setTitle("Пример поля");

        }
    }

    public function registerRoutes(): array
    {
        $configRoutes = [];

        foreach ($this->registeredGroups as $groupId => $title) {

            $tmp = new Route('/edit/{id}', [
                '_controller' => "ConfigGroup",
                'id' => $groupId
            ]);
            $tmp
                ->setTitle($title)
                ->setIcon('fa-cog');
            $configRoutes[] = $tmp;
        }

        return $configRoutes;
    }

    public function useAlias()
    {
        return "Конфигуратор";
    }

    public function useIcon()
    {
        return "fa-cogs";
    }

}
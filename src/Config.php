<?php namespace Kitrix\Config;

use Kitrix\Config\Admin\Field;
use Kitrix\Config\Admin\FieldType;
use Kitrix\Config\Fields\Checkbox;
use Kitrix\Config\Fields\Input;
use Kitrix\MVC\Admin\RouteFactory;
use Kitrix\Plugins\Plugin;

final class Config extends Plugin
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

    /**
     * Return all fields by group and id
     * @return mixed
     */
    public function getRegisteredFields()
    {
        return $this->registeredFields;
    }

    /**
     * Return all groups
     * @return mixed
     */
    public function getRegisteredGroups()
    {
        return $this->registeredGroups;
    }

    public function run()
    {
        if (false !== $coreGroup = $this->addConfigGroup('Основные настройки')) {

            $this
                ->addConfigField($coreGroup, new Input())
                ->setTitle("Пример поля");

            $this
                ->addConfigField($coreGroup, new Checkbox())
                ->setTitle("Использовать энергию молодости и хардкора?");
        }

        if (false !== $coreGroup2 = $this->addConfigGroup('Еще одни настройки')) {

            $this
                ->addConfigField($coreGroup2, new Input())
                ->setTitle("Очередное поле");
        }
    }

    public function registerRoutes(): array
    {
        $configRoutes = [];

        foreach ($this->registeredGroups as $groupId => $title) {

            $configRoutes[] = RouteFactory::makeRoute('/edit/{id}', ConfigGroupController::class, "edit", [
                'id' => (int)$groupId
            ])
                ->setTitle($title)
                ->setIcon('fa-cog');
        }

        $configRoutes[] = RouteFactory::makeRoute('/list/', SiteConfigController::class, 'configList')
            ->setTitle('Собственные настройки сайта')
            ->setIcon('fa-tags');

        return $configRoutes;
    }
}
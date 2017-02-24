<?php namespace Kitrix\Config;

use Kitrix\Config\Admin\Field;
use Kitrix\Config\Admin\Group;
use Kitrix\Config\API\Register;
use Kitrix\Config\Fields\Checkbox;
use Kitrix\Config\Fields\Input;
use Kitrix\Config\ORM\ValuesTable;
use Kitrix\MVC\Admin\RouteFactory;
use Kitrix\Plugins\Plugin;

final class Config extends Plugin
{

    /**
     * Make new config field and return it
     * All fields should be added to fieldGroup
     * And fieldGroup should be finally registered
     *
     * Field -> FieldGroup -> Config::RegisterGroup
     *
     * @param string $type  - type should be className
     *                        of FieldType instance, example:
     *                        Input::class,
     *                        Checkbox::class,
     *                        Textarea::class
     *
     * @param $code         - code for API. Late you can get
     *                        field valud by this code
     *
     * @param string $title - title of field
     *
     * @return Field
     */
    public function makeField($type, $code, $title = "")
    {
        $registry = Register::getInstance();
        $field = $registry->makeField($type, $code, $title);
        return $field;
    }

    /**
     * Make new config group and return it
     * Fields should be added to new group.
     * Group finally should be registered.
     *
     * Field -> FieldGroup -> Config::RegisterGroup
     *
     * @param $title        - Title of group
     * @param $pluginId     - Class name of plugin who register
     *                        this group. This should be
     *                        name like this
     *                        'MyPlugin::class',
     *                        'Core::class',
     * @return Group
     */
    public function makeGroup($title, $pluginId)
    {
        $group = new Group($title, $pluginId);
        return $group;
    }

    /**
     * Register config group
     *
     * @param Group $group
     * @return $this
     */
    public function registerGroup(Group $group)
    {
        Register::getInstance()->registerGroup($group);
        return $this;
    }

    public function run()
    {
        $test = $this->makeField(Input::class, 'test')
            ->setTitle('Тестовое поле')
            ->setDefaultValue("default test");

        $cb = $this->makeField(Checkbox::class, 'cb')
            ->setTitle('Чек бокс');

        $anotherTest = $this->makeField(Input::class, 'second')
            ->setTitle('Еще одно поле для теста');

        $group = $this->makeGroup('Основные настройки', Config::class)
            ->addField($test)
            ->addField($cb)
            ->addField($anotherTest);

        $this->registerGroup($group);

        // load db fields
        Register::getInstance()->loadFields();
    }

    public function registerRoutes(): array
    {
        $configRoutes = [];

        // get registered groups
        $registry = Register::getInstance();
        foreach ($registry->getGroups() as $group) {

            $configRoutes[] = RouteFactory::makeRoute(
                '/edit/{id}/',
                ConfigGroupController::class,
                'edit', [
                    'id' => $group->getGroupId()
                ]
            )
                ->setTitle($group->getTitle())
                ->setIcon('fa-tag');
        }

        $configRoutes[] = RouteFactory::makeRoute('/list/', SiteConfigController::class, 'configList')
            ->setTitle('Собственные настройки сайта')
            ->setIcon('fa-tags');

        return $configRoutes;
    }

    public static function onInstall()
    {
        ValuesTable::getEntity()->createDbTable();
    }

    public static function onUninstall()
    {
        global $DB;

        $dropValuesTableQuery = vsprintf("DROP TABLE %s", [ValuesTable::getTableName()]);
        $DB->Query($dropValuesTableQuery);
    }
}
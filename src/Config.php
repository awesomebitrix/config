<?php
/******************************************************************************
 * Copyright (c) 2017. Kitrix Team                                            *
 * Kitrix is open source project, available under MIT license.                *
 *                                                                            *
 * @author: Konstantin Perov <fe3dback@yandex.ru>                             *
 * Documentation:                                                             *
 * @see https://kitrix-org.github.io/docs                                     *
 *                                                                            *
 *                                                                            *
 ******************************************************************************/

namespace Kitrix\Config;

use Kitrix\Config\API\Register;
use Kitrix\Config\Fields\Checkbox;
use Kitrix\Config\Fields\Input;
use Kitrix\Config\Fields\Select;
use Kitrix\Config\Fields\Textarea;
use Kitrix\Config\ORM\ValuesTable;
use Kitrix\MVC\Admin\RouteFactory;
use Kitrix\Plugins\Plugin;

final class Config extends Plugin
{
    public function run2()
    {
        $test = ConfRegistry::makeField(Input::class, 'test')
            ->setTitle('Тестовое поле')
            ->setDefaultValue("default test")
            ->setDisabled(true);

        $cb = ConfRegistry::makeField(Checkbox::class, 'cb')
            ->setTitle('Чек бокс')
            ->setHelpText("<b>Important!</b>: this is example of help message..");

        $anotherTest = ConfRegistry::makeField(Input::class, 'second')
            ->setTitle('Еще одно поле для теста')
            ->setHidden(true);

        $someMessage = ConfRegistry::makeField(Input::class, 'LOLTEST1')
            ->setTitle('lol test')
            ->setDefaultValue('hello!')
            ->setReadonly(true);

        $group = ConfRegistry::makeGroup('Основные настройки', Config::class)
            ->addField($test)
            ->addField($cb)
            ->addField($anotherTest)
            ->addField($someMessage)
            ->addField(
                ConfRegistry::makeField(Textarea::class, 'text')
                    ->setTitle("text area")
            )
            ->addField(
                ConfRegistry::makeField(Select::class, 'select_test')
                    ->setTitle("Пример селекта")
                    ->setDefaultValue(0)
                    ->setOptions(["world" => "Hello world", "shit" => "Bitrix"])
            );

        $group2 = ConfRegistry::makeGroup('2 настройки', Config::class)
            ->addField($test);

        ConfRegistry::registerGroup($group);
        ConfRegistry::registerGroup($group2);

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
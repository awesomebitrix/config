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
use Kitrix\Config\ORM\ValuesTable;
use Kitrix\MVC\Admin\RouteFactory;
use Kitrix\Plugins\Plugin;

final class Config extends Plugin
{
    public function run()
    {
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
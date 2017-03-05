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

use Kitrix\Config\Admin\Field;
use Kitrix\Config\Admin\Group;
use Kitrix\Config\API\Register;

/**
 * This is clear API wrapper of registry
 *
 * Class ConfRegistry
 * @package Kitrix\Config
 */
final class ConfRegistry
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
    public static function makeField($type, $code, $title = "")
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
    public static function makeGroup($title, $pluginId)
    {
        $group = new Group($title, $pluginId);
        return $group;
    }

    /**
     * Register config group
     *
     * @param Group $group
     */
    public static function registerGroup(Group $group)
    {
        Register::getInstance()->registerGroup($group);
    }

    /**
     * Get field value from DB by pluginId and fieldCode
     * pluginId should be same as registered, example:
     * 'MyNamespace/MySuperPlugin::class'
     *
     * Method use cache, so you can free to use
     * function in any loops. This is fast.
     *
     * Return null, if value not exist
     *
     * @param $pluginId
     * @param $fieldCode
     * @return mixed|null
     */
    public static function getValue($pluginId, $fieldCode)
    {
        return Register::getInstance()->getValue($pluginId, $fieldCode);
    }

    /**
     * Set field value programmatically
     * to any mixed data. This data
     * will be serialized by field method
     * and finally saved to DB
     *
     * @param $pluginId
     * @param $fieldCode
     * @param $value
     * @return bool - true if success, false otherwise
     */
    public static function setValue($pluginId, $fieldCode, $value)
    {
        return Register::getInstance()->setValue($pluginId, $fieldCode, $value);
    }
}
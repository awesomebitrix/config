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

namespace Kitrix\Config\API;

use Bitrix\Main\Result;
use Kitrix\Common\Kitx;
use Kitrix\Common\SingletonClass;
use Kitrix\Config\Admin\Field;
use Kitrix\Config\Admin\FieldType;
use Kitrix\Config\Admin\Group;
use Kitrix\Config\ORM\ValuesTable;

class Register
{
    use SingletonClass;

    const EXPECTED_FIELD_TYPE = "Kitrix\\Config\\Admin\\FieldType";

    /** @var bool  */
    private $isInitialized = false;

    /** @var Group[] */
    private $groups = [];

    /** @var array  */
    private $_fieldsCache = [];

    /** @var array - fast values access */
    private $_valuesCache = [];

    protected function init()
    {
        if ($this->isInitialized) {
            return;
        }
        $this->isInitialized = true;
    }

    /**
     * Register config group
     *
     * @param Group $group
     * @return $this
     */
    public function registerGroup(Group $group)
    {
        $dbCurrentStamp = $this->loadFields();

        // register fields in DB with default values
        foreach ($group->getFields() as $field) {

            // if field already in DB, skip
            $uniqId = $this->getUniqueIdFromField($group, $field);
            if (in_array($uniqId, array_keys($dbCurrentStamp))) {
                continue;
            }

            $defValue = $field->getType()->serialize($field->getDefaultValue());

            // and field to database
            ValuesTable::add([
                ValuesTable::UNIQUE_ID => $uniqId,
                ValuesTable::CODE => $field->getCode(),
                ValuesTable::PID => $group->getGroupId(),
                ValuesTable::VALUE => $defValue,
                ValuesTable::DEFAULT => $defValue
            ]);
        }

        // register group
        $this->groups[$group->getGroupId()] = $group;

        return $this;
    }

    /**
     * Register list of config groups
     *
     * @param array $groups
     * @return $this
     * @throws \Exception
     */
    public function registerGroups(array $groups)
    {
        foreach ($groups as $group)
        {
            if (!($group instanceof Group)) {
                throw new \Exception(Kitx::frmt("
                    Cannot register config group, provided
                    group is not valid Group object
                ",[]));
            }

            $this->registerGroup($group);
        }

        return $this;
    }

    /**
     * Make new field and return it
     *
     * @param string $type
     * @param $code
     * @param $title
     * @return Field
     * @throws \Exception
     */
    public function makeField($type, $code, $title)
    {
        if (!class_exists($type)) {
            throw new \Exception(Kitx::frmt("
                Cannot make new config field. You should
                provide valid field type, expected value like this 
                'Checkbox::class', given '%s'
            ", [$type]));
        }

        $ref = new \ReflectionClass($type);

        if ($ref->getParentClass()->getName() !== self::EXPECTED_FIELD_TYPE) {
            throw new \Exception(Kitx::frmt("
                Cannot make new config field. You should
                provide valid type and this type should be
                instance of FieldType. Expected values like
                'Checkbox::class', given '%s'
            ", [$type]));
        }

        /** @var FieldType $fieldType */
        $fieldType = new $type();
        $field = new Field($fieldType, $code);
        $field->setTitle($title ?: $code);

        return $field;
    }

    /**
     * Return config groups
     *
     * @return Group[]
     */
    public function getGroups(): array
    {
        return $this->groups;
    }

    /**
     * Return field values from DB
     *
     * @param bool $useCache
     * @return array
     */
    public function loadFields($useCache = true): array
    {
        if (!count($this->_fieldsCache) OR !$useCache)
        {
            $this->_fieldsCache = [];
            $fields = ValuesTable::getList();

            while ($f = $fields->fetch())
            {
                $this->_fieldsCache[$f[ValuesTable::UNIQUE_ID]] = $f;
            }
        }

        return $this->_fieldsCache;
    }

    /**
     * Get unique field identifier
     *
     * @param Group $g
     * @param Field $f
     * @return string
     */
    public function getUniqueIdFromField(Group $g, Field $f)
    {
        return sha1($g->getGroupId() . $f->getCode());
    }

    /**
     * Update values of all fields in group
     *
     * @param Group $group
     * @param array $updateData
     * @return Result
     */
    public function updateValues(Group $group, $updateData = [])
    {
        $fields = $group->getFields();
        $result = new Result();

        foreach ($fields as $field)
        {
            if ($field->isDisabled() or $field->isReadonly())
            {
                continue;
            }

            $uniqId = $this->getUniqueIdFromField($group, $field);

            if (in_array($uniqId, array_keys($updateData)))
            {
                $value = $updateData[$uniqId];
            }
            else
            {
                $value = 0;
            }

            // prepare value
            $value = $field->getType()->serialize($value);

            // update row in db
            $status = $this->updateValueField($uniqId, [
                ValuesTable::VALUE => $value
            ]);
            if (!$status->isSuccess()) {
                $result->addErrors($status->getErrors());
                break;
            }
        }

        return $result;
    }

    /**
     * Update value field and return status
     *
     * @param $uniqId
     * @param array $data
     * @return \Bitrix\Main\Entity\UpdateResult
     */
    public function updateValueField($uniqId, $data = [])
    {
        $status = ValuesTable::update($uniqId, $data);
        return $status;
    }

    /**
     * Get field value from DB by pluginId and fieldCode
     * Method use cache
     *
     * @param $pluginId
     * @param $fieldCode
     * @return mixed|null
     */
    public function getValue($pluginId, $fieldCode)
    {
        // fast cache
        // -------------
        $_cacheKey = sha1($pluginId . $fieldCode);
        if (in_array($_cacheKey, array_keys($this->_valuesCache)))
        {
            return $this->_valuesCache[$_cacheKey];
        }

        // find in store
        // -------------
        $searchableValue = null;
        $values = $this->loadFields(true);

        $valueUniqId = $this->getUniqueKey($pluginId, $fieldCode);
        $field = $this->getField($pluginId, $fieldCode);

        if (!($valueUniqId && $field))
        {
            return null;
        }

        if (in_array($valueUniqId, array_keys($values)))
        {
            $_dbVal = $values[$valueUniqId][ValuesTable::VALUE];
            $searchableValue = $field->getType()->unserialize($_dbVal);
        }

        if (!is_null($searchableValue))
        {
            $this->_valuesCache[$_cacheKey] = $searchableValue;
        }

        return $searchableValue;
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
    public function setValue($pluginId, $fieldCode, $value)
    {
        $oldValue = $this->getValue($pluginId, $fieldCode);

        if ($oldValue === null)
        {
            return false;
        }

        if (false === ($field = $this->getField($pluginId, $fieldCode)))
        {
            return false;
        }

        $fieldUniqId = $this->getUniqueKey($pluginId, $fieldCode);
        $dbValue = $field->getType()->serialize($value);

        // update in db
        $result = $this->updateValueField($fieldUniqId, [
            ValuesTable::VALUE => $dbValue
        ]);

        // clear cache
        $this->_valuesCache = [];
        $this->_fieldsCache = [];

        return $result->isSuccess();
    }

    /**
     * Get unique field key by plugin and fieldCode
     * Actual key used in database as primary key
     *
     * @param $pluginId
     * @param $fieldCode
     * @return bool|string - return false if field not found
     */
    public function getUniqueKey($pluginId, $fieldCode)
    {
        $key = false;

        foreach ($this->getGroups() as $group)
        {
            if ($group->getPluginId() !== $pluginId)
            {
                continue;
            }

            foreach ($group->getFields() as $field)
            {
                if ($field->getCode() !== $fieldCode)
                {
                    continue;
                }

                $key = $this->getUniqueIdFromField($group, $field);
            }
        }

        return $key;
    }

    /**
     * Get field object by pluginId and fieldCode
     * or false if field not found
     *
     * @param $pluginId
     * @param $fieldCode
     * @return bool|Field
     */
    public function getField($pluginId, $fieldCode)
    {
        foreach ($this->getGroups() as $group)
        {
            if ($group->getPluginId() !== $pluginId)
            {
                continue;
            }

            foreach ($group->getFields() as $field)
            {
                if ($field->getCode() !== $fieldCode)
                {
                    continue;
                }

                return $field;
            }
        }

        return false;
    }
}
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
     * @return \Bitrix\Main\Entity\UpdateResult|bool
     */
    public function updateValues(Group $group, $updateData = [])
    {
        $fields = $group->getFields();
        $status = true;

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
                break;
            }
        }

        return $status;
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

                $valueUniqId = $this->getUniqueIdFromField($group, $field);
                if (in_array($valueUniqId, array_keys($values)))
                {
                    $_dbVal = $values[$valueUniqId][ValuesTable::VALUE];
                    $searchableValue = $field->getType()->unserialize($_dbVal);
                }
            }
        }

        if (!is_null($searchableValue))
        {
            $this->_valuesCache[$_cacheKey] = $searchableValue;
        }

        return $searchableValue;
    }
}
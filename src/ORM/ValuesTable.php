<?php namespace Kitrix\Config\ORM;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;

class ValuesTable extends DataManager
{
    const UNIQUE_ID = 'unique_id';
    const PID = 'pid';
    const CODE = 'code';
    const VALUE = 'value';
    const DEFAULT = 'default';

    public static function getTableName()
    {
        return 'kitrix_config_values';
    }

    public static function getUfId()
    {
        return "KITRIX_CONFIG_VALUES";
    }

    public static function getMap()
    {
        return [
            new StringField(self::UNIQUE_ID, [
                'primary' => true,
            ]),
            new StringField(self::PID, [
                'required' => true
            ]),
            new StringField(self::CODE, [
                'required' => true
            ]),
            new StringField(self::VALUE, [
                'required' => true
            ]),
            new StringField(self::DEFAULT, [
                'required' => true
            ])
        ];
    }
}
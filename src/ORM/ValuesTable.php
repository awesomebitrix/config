<?php namespace Kitrix\Config\ORM;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Entity\IntegerField;
use Bitrix\Main\Entity\StringField;

class ValuesTable extends DataManager
{
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
            new IntegerField('id', [
                'primary' => true,
                'autocomplete' => true
            ]),
            new StringField('pid', [
                'required' => true
            ]),
            new StringField('code', [
                'required' => true
            ]),
            new StringField('value', [
                'required' => true
            ])
        ];
    }
}
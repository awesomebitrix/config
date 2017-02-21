<?php namespace Kitrix\Config\Fields;

use Kitrix\Config\Admin\FieldType;

final class Input extends FieldType
{
    public function serialize($value)
    {
        return $value/2;
    }

    public function unserialize($value)
    {
        return $value*2;
    }
}
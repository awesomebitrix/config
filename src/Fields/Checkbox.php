<?php namespace Kitrix\Config\Fields;

use Kitrix\Config\Admin\FieldType;

class Checkbox extends FieldType
{
    public function serialize($value)
    {
        return (bool)($value === 'Y' OR $value === "yes" OR $value) ? true : false;
    }

    public function unserialize($value)
    {
        return (bool)$value;
    }

    public function renderWidget($value)
    {
        ob_start();
        ?>
            <input id="{id}" name="{name}" type="checkbox" <?=$value ? "checked" : ""?> title="{title}">
        <?
        return ob_get_clean();
    }
}
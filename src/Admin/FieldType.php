<?php namespace Kitrix\Config\Admin;

abstract class FieldType
{

    /**
     * Function should return valid html code of input.
     * You should use variable placeholder for input:
     *
     * {id} - id for field (can be used in field,label,etc..)
     * {name} - system name for field
     * {title} - title for field
     *
     * example:
     * <label for="{id}">{title}</label>
     * <input id="{id}" name="{name}">
     *
     * @param $value - value from db after unserialize
     * @return string
     */
    public function renderWidget($value) {

        ob_start();
        ?>
        <input type="text" id="{id}" value="<?=$value?>">
        <?
        return ob_get_clean();
    }

    /**
     * Function should return any
     * template of widget title
     *
     * You can use template:
     * {id} - id for field (can be used in field,label,etc..)
     * {name} - system name for field
     * {title} - title for field
     *
     * @return string
     */
    public function renderLabel() {

        ob_start();
        ?>
        <label for="{id}">{title}</label>
        <?
        return ob_get_clean();
    }

    /**
     * At this point we can prepare
     * value to render in input widget
     *
     * @param $value - value from db
     * @return mixed - this result will be given to render method
     */
    public function unserialize($value) {

        return $value;
    }

    /**
     * At this point we can prepare
     * value to store in db
     *
     * DB can resolve int, str, date
     * types. Custom types should be
     * transformed to string.
     *
     * ex. return serialize($value)
     *
     * @param $value
     * @return mixed
     */
    public function serialize($value) {

        return $value;
    }
}
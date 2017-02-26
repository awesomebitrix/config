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

namespace Kitrix\Config\Admin;

abstract class FieldType
{

    /**
     * Function should return valid html code of input.
     * You can use vars with primary field attributes, like id, name, class and etc..
     *
     * @param $value - value from db after unserialize
     * @param $vars - other field attributes
     * @return string
     */
    public function renderWidget($value, $vars) {

        ob_start();
        ?>
        <input
            <?=$vars[FieldRepresentation::ATTR_ATTRIBUTES_LINE]?>
            type="text"
            value="<?=$value?>"
        >
        <?
        return ob_get_clean();
    }

    /**
     * Function should return valid html code of input.
     * You can use vars with primary field attributes, like id, name, class and etc..
     *
     * @param $value - value from db after unserialize
     * @param $vars - other field attributes
     * @return string
     */
    public function renderLabel($value, $vars) {

        ob_start();
        ?>
        <label for="<?=$vars['id']?>">
            <?=$vars['title']?>
        </label>
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
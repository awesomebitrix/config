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

namespace Kitrix\Config\Fields;

use Kitrix\Config\Admin\FieldRepresentation;
use Kitrix\Config\Admin\FieldType;

final class Checkbox extends FieldType
{
    public function serialize($value)
    {
        return ($value === 'Y' OR $value === "yes" OR $value) ? 1 : 0;
    }

    public function unserialize($value)
    {
        return (bool)$value >= 1;
    }

    public function renderWidget($value, $vars)
    {
        ob_start();
        ?>
            <input
                <?=$vars[FieldRepresentation::ATTR_ATTRIBUTES_LINE]?>
                type="checkbox"
                value="Y" <?=$value ? "checked" : ""?>
            >
        <?
        return ob_get_clean();
    }
}
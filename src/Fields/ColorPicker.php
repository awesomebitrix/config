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

class ColorPicker extends FieldType
{
    public function renderWidget($value, $vars)
    {
        ob_start();
        ?>
        <input
            <?=$vars[FieldRepresentation::ATTR_ATTRIBUTES_LINE]?>
            type="color"
            value="<?=$value?>"
        >
        <?
        return ob_get_clean();
    }
}
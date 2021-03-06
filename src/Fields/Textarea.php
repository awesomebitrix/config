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

final class Textarea extends FieldType
{
    public function renderWidget($value, $vars)
    {
        ob_start();
        ?>
        <textarea
            <?=$vars[FieldRepresentation::ATTR_ATTRIBUTES_LINE]?>
            style="min-height: 100px;max-height: 500px;width: 100%;max-width: 600px"
            ><?=$value ? $value : ""?> </textarea>
        <?
        return ob_get_clean();
    }
}
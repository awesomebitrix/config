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

final class Select extends FieldType
{
    public function renderWidget($value, $vars)
    {
        $options = $vars[FieldRepresentation::ATTR_OPTIONS];
        if (!count($options)) {
            $options[] = "Нет вариантов для выбора";
        }

        ob_start();
        ?>
            <select <?=$vars[FieldRepresentation::ATTR_ATTRIBUTES_LINE]?>>
                <?foreach ($options as $id => $title):?>
                    <option value="<?=$id?>" <?=($value === $id) ? "selected" : ""?>><?=$title?></option>
                <?endforeach;?>
            </select>
        <?
        return ob_get_clean();
    }
}
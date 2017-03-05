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

use Kitrix\Config\API\Register;
use Kitrix\Config\ORM\ValuesTable;

final class FormHelper
{
    /**
     * Get list of widgets from
     * group
     *
     * @param Group $group
     * @return FieldRepresentation[]
     */
    public static function getWidgets(Group $group)
    {
        $registry = Register::getInstance();

        $fields = $group->getFields();
        $dbValues = $registry->loadFields(false);

        $result = [];
        foreach ($fields as $field)
        {
            /** @var Field $field */

            $uniqId = $registry->getUniqueIdFromField($group, $field);
            $setting = $dbValues[$uniqId] ?: false;

            if (!$setting) {
                continue;
            }

            $widget = $field->render($setting[ValuesTable::VALUE], $uniqId);
            $result[] = $widget;
        }

        return $result;
    }

    /**
     * @param Field[] $fields
     * @return FieldRepresentation[]
     */
    public static function getWidgetsFromFields($fields)
    {
        $result = [];
        foreach ($fields as $field)
        {
            $widget = $field->render($field->getDefaultValue(), $field->getCode());
            $result[] = $widget;
        }

        return $result;
    }

    /**
     * Render form fields from widgets
     * array
     *
     * @param FieldRepresentation[] $widgets
     */
    public static function renderForm($widgets)
    {
        foreach ($widgets as $widget)
        {
            if (!($widget instanceof FieldRepresentation))
            {
                continue;
            }
            $hidden = !!$widget->getVar(FieldRepresentation::ATTR_HIDDEN);
            $help = $widget->getVar(FieldRepresentation::ATTR_HELP);
            $readOnly = !!$widget->getVar(FieldRepresentation::ATTR_READ_ONLY);
            $value = $widget->getVar(FieldRepresentation::ATTR_VALUE);

            ?>
            <tr <?=$hidden ? "style='display:none;'" : ""?>>
                <td style="vertical-align: top;">
                    <?=$widget->getLabel()?>
                </td>
                <td>
                    <?if(!$readOnly):?>
                        <?=$widget->getWidget()?>
                    <?else:?>
                        <?=$value?>
                    <?endif;?>

                    <?if($help):?>
                        <div class="adm-info-message-wrap">
                            <div class="adm-info-message">
                                <?=$help?>
                            </div>
                        </div>
                    <?endif;?>
                </td>
            </tr>
            <?
        }
    }
}
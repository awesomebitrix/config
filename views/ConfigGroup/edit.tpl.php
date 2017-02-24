<?
/**
 * @var array $result
 */

$title = $result['title'];
/** @var \Kitrix\Config\Admin\FieldRepresentation[] $widgets */
$widgets = $result['widgets'];

?>

<?if(count($widgets)):?>

    <div class="adm-detail-content">

        <div class="adm-detail-title">
            <?=$title?>
        </div>

        <div class="adm-detail-content-item-block">
            <table class="adm-detail-content-table edit-table">
                <tbody>
                    <?foreach ($widgets as $widget):?>

                        <tr class="kitrix-config-field">
                            <td class="adm-detail-content-cell-l">
                                <?=$widget->getLabel()?>
                            </td>
                            <td class="adm-detail-content-cell-r">
                                <?=$widget->getWidget()?>
                            </td>
                        </tr>

                    <?endforeach;?>
                </tbody>
            </table>
        </div>

    </div>

<?else:?>

    <div class="kitrix-config-warning-msg">
        Никаких настроек в группе "<?=$title?>" не найдено.
    </div>

<?endif;?>

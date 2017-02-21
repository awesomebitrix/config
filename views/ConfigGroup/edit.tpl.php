<?
/**
 * @var string $group
 * @var \Kitrix\Config\Admin\Field[] $fields
 */
?>

<?if(!$group):?>

    Нет такой группы настроек :(
    <?return;?>
<?endif;?>

<?if(count($fields)):?>

    <div class="adm-detail-content">

        <div class="adm-detail-title">
            <?=$group?>
        </div>

        <div class="adm-detail-content-item-block">
            <table class="adm-detail-content-table edit-table">
                <tbody>
                    <?foreach ($fields as $field):?>

                        <?
                            $widget = $field->render(1);
                        ?>
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
        Никаких настроек в группе "<?=$group?>" не найдено.
    </div>

<?endif;?>

<?
/**
 * @var array $result
 * @var string $post_url
 * @var \CAdminMessage $error
 */

use Kitrix\Config\Admin\FieldRepresentation;

$title = $result['title'];
/** @var \Kitrix\Config\Admin\FieldRepresentation[] $widgets */
$widgets = $result['widgets'];
?>

<?/** =============== BITRIX CODE ==================== */?>

<form method="POST" Action="<?=$post_url?>" ENCTYPE="multipart/form-data" name="post_form">

    <?=bitrix_sessid_post();?>

    <?
    // prepare tabs
    $tabControl = new CAdminTabControl("tabControl", [
        [
            "DIV" => "edit",
            "TAB" => $title,
            "TITLE"=>$title,
            "ICON"=>"main_user_edit",
        ]
    ]);

    $tabControl->Begin();
    $tabControl->BeginNextTab();
    ?>

    <?if(count($widgets)):?>
        <?foreach ($widgets as $widget):?>
            <?
            $hidden = !!$widget->getVar(FieldRepresentation::ATTR_HIDDEN);
            $help = $widget->getVar(FieldRepresentation::ATTR_HELP);
            $readOnly = !!$widget->getVar(FieldRepresentation::ATTR_READ_ONLY);
            $value = $widget->getVar(FieldRepresentation::ATTR_VALUE)
            ?>
            <tr <?=$hidden ? "style='display:none;'" : ""?>>
                <td style="vertical-align: top;">
                    <?=$widget->getLabel()?>
                </td>
                <td>
                    <?if(!$readOnly):?>
                        <?=$widget->getWidget()?>

                        <?if($help):?>
                            <div class="adm-info-message-wrap">
                                <div class="adm-info-message">
                                    <?=$help?>
                                </div>
                            </div>
                        <?endif;?>
                    <?else:?>
                        <?=$value?>
                    <?endif;?>

                </td>
            </tr>
        <?endforeach;?>
    <?else:?>
        <tr>
            <td>
                <div class="kitrix-config-warning-msg">
                    Никаких настроек в группе "<?=$title?>" не найдено.
                </div>
            </td>
        </tr>
    <?endif;?>

    <?
    $tabControl->Buttons(
        array(
            "btnCancel" => false,
            "btnApply" => false
        )
    );
    $tabControl->End();
    $tabControl->ShowWarnings("post_form", $error);

    ?>

</form>


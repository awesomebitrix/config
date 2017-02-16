<?php namespace Kitrix\Config\Admin;

abstract class FieldType
{
    public function render(Field $f) {

        ob_start();
        ?>
        <label for="ktrx-config-field-<?=$f->getGroupId()?>-<?=$f->getId()?>"><?=$f->getTitle()?></label>
        <input type="text" id="ktrx-config-field-<?=$f->getGroupId()?>-<?=$f->getId()?>">
        <?
        return ob_get_clean();
    }
}
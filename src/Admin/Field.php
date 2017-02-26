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

final class Field
{
    /** @var FieldType */
    private $type;

    /** @var string  */
    private $code = "";

    /** @var string */
    private $title = "Default input";

    /**@var bool - widget can be edited? */
    private $disabled = false;

    /** @var bool - widget is hidden? */
    private $hidden = false;

    /** @var string - help text, will be rendered after widget (html allowed) */
    private $helpText = "";

    /** @var bool - widget is read only? */
    private $readonly = false;

    /** @var mixed */
    private $defaultValue = 0;

    function __construct(FieldType $type, $code)
    {
        $this->type = $type;
        $this->code = $code;
    }

    /**
     * Render automatic replace this variables in html:
     * {id} - id for field (can be used in field,label,etc..)
     * {name} - system name for field
     * {title} - title for field
     *
     * @param $dbValue
     * @param $uniqId
     * @return FieldRepresentation
     */
    final public function render($dbValue, $uniqId) {

        $value = $this->type->unserialize($dbValue);

        $vars = [
            FieldRepresentation::ATTR_ID => "ktrx_field_" . $uniqId,
            FieldRepresentation::ATTR_NAME => $uniqId,
            FieldRepresentation::ATTR_TITLE => $this->getTitle(),
            FieldRepresentation::ATTR_HELP => $this->getHelpText(),
            FieldRepresentation::ATTR_DISABLED => $this->isDisabled(),
            FieldRepresentation::ATTR_HIDDEN => $this->isHidden(),
            FieldRepresentation::ATTR_READ_ONLY => $this->isReadonly(),
            FieldRepresentation::ATTR_VALUE => $value,
            FieldRepresentation::ATTR_VALUE_ORIGINAL => $dbValue
        ];

        $attributes = [
            [
                'attr' => 'disabled="disabled"',
                'allow' => $vars[FieldRepresentation::ATTR_DISABLED]
            ],
            [
                'attr' => "id='{$vars[FieldRepresentation::ATTR_ID]}'",
                'allow' => true
            ],
            [
                'attr' => "name='{$vars[FieldRepresentation::ATTR_NAME]}'",
                'allow' => true
            ],
            [
                'attr' => "title='{$vars[FieldRepresentation::ATTR_TITLE]}'",
                'allow' => true
            ],
        ];

        $attrLine = "";
        foreach ($attributes as $attribute)
        {
            if ($attribute['allow'])
            {
                $attrLine .= " {$attribute['attr']}";
            }
        }

        $vars[FieldRepresentation::ATTR_ATTRIBUTES_LINE] = $attrLine;

        $htmlWidget = $this->type->renderWidget($value, $vars);
        $htmlLabel = $this->type->renderLabel($value, $vars);

        return new FieldRepresentation($htmlWidget, $htmlLabel, $vars);
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return FieldType
     */
    public function getType(): FieldType
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * @return string
     */
    public function getHelpText(): string
    {
        return $this->helpText;
    }

    /**
     * @return bool
     */
    public function isReadonly(): bool
    {
        return $this->readonly;
    }

    /**
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @param mixed $defaultValue
     * @return $this
     */
    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    /**
     * @param bool $disabled
     * @return $this
     */
    public function setDisabled(bool $disabled)
    {
        $this->disabled = $disabled;
        return $this;
    }

    /**
     * @param bool $hidden
     * @return $this
     */
    public function setHidden(bool $hidden)
    {
        $this->hidden = $hidden;
        return $this;
    }

    /**
     * @param string $helpText
     * @return $this
     */
    public function setHelpText(string $helpText)
    {
        $this->helpText = $helpText;
        return $this;
    }

    /**
     * @param bool $readonly
     * @return $this
     */
    public function setReadonly(bool $readonly)
    {
        $this->readonly = $readonly;
        return $this;
    }


}
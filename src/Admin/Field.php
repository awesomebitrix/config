<?php namespace Kitrix\Config\Admin;

final class Field
{
    /** @var FieldType */
    private $type;

    /** @var string  */
    private $code = "";

    /** @var string */
    private $title = "Default input";

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
     * @return FieldRepresentation
     */
    final public function render($dbValue) {

        $value = $this->type->unserialize($dbValue);
        $htmlWidget = $this->type->renderWidget($value);
        $htmlLabel = $this->type->renderLabel();

        $inputId = "ktrx_config_" .$this->getPluginId() . "_" . $this->getCode();

        $template = [
            "{id}" => $inputId,
            "{name}" => $inputId,
            "{title}" => $this->getTitle()
        ];

        $htmlWidget = str_replace(array_keys($template), array_values($template), $htmlWidget);
        $htmlLabel = str_replace(array_keys($template), array_values($template), $htmlLabel);

        return new FieldRepresentation($htmlWidget, $htmlLabel);
    }

    /**
     * @return int
     */
    public function getCode(): int
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
     * @param string $title
     * @return $this
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
        return $this;
    }
}
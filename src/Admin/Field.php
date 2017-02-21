<?php namespace Kitrix\Config\Admin;

final class Field
{
    /** @var FieldType */
    private $type;

    /** @var int  */
    private $id = 0;

    /** @var int  */
    private $groupId = 0;

    /** @var string */
    private $title = "Default input";

    function __construct(FieldType $type, $groupId, $id)
    {
        $this->type = $type;
        $this->groupId = $groupId;
        $this->id = $id;
    }

    /**
     * Render automatic replace this variables in html:
     * {id} - id for field (can be used in field,label,etc..)
     * {name} - system name for field
     * {title} - title for field
     *
     * @return FieldRepresentation
     */
    final public function render($dbValue) {

        $value = $this->type->unserialize($dbValue);
        $htmlWidget = $this->type->renderWidget($value);
        $htmlLabel = $this->type->renderLabel();

        $inputId = "ktrx_config_" .$this->getGroupId() . "_" . $this->getId();

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
    public function getGroupId(): int
    {
        return $this->groupId;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }
}
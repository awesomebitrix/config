<?php namespace Kitrix\Config\Admin;

class Field
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
        $this->groupId = $groupId;
        $this->id = $id;
    }

    final public function render() {
        $this->type->render($this);
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
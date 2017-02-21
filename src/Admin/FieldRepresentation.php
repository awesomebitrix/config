<?php namespace Kitrix\Config\Admin;

final class FieldRepresentation
{
    /** @var string */
    private $widget = "";

    /** @var string */
    private $label = "";

    function __construct($widgetHtml, $labelHtml)
    {
        $this->widget = $widgetHtml;
        $this->label = $labelHtml;
    }

    /**
     * @return string
     */
    public function getWidget(): string
    {
        return $this->widget;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }
}
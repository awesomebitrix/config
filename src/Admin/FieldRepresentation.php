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

final class FieldRepresentation
{

    const ATTR_ID = "id";
    const ATTR_NAME = "name";
    const ATTR_TITLE = "title";
    const ATTR_HELP = "help";
    const ATTR_DISABLED = "disabled";
    const ATTR_HIDDEN = "hidden";
    const ATTR_READ_ONLY = "read_only";
    const ATTR_VALUE = "value";
    const ATTR_VALUE_ORIGINAL = "value_original";
    const ATTR_ATTRIBUTES_LINE = "attributes";

    /** @var string */
    private $widget = "";

    /** @var string */
    private $label = "";

    /** @var array - field attributes */
    private $vars = [];

    function __construct($widgetHtml, $labelHtml, $vars = [])
    {
        $this->widget = $widgetHtml;
        $this->label = $labelHtml;
        $this->vars = $vars;
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

    /**
     * @return array
     */
    public function getVars(): array
    {
        return $this->vars;
    }

    /**
     * Get attribute by name
     *
     * @param $name
     * @return bool|mixed
     */
    public function getVar($name) {
        if (in_array($name, array_keys($this->vars))) {
            return $this->vars[$name];
        }

        return false;
    }
}
<?php namespace Kitrix\Config\Admin;

use Kitrix\Common\Kitx;
use Kitrix\Plugins\PluginsManager;

class Group
{
    /** @var string */
    private $title;

    /** @var string  */
    private $pluginId = "";

    /** @var string  */
    private $groupId = "";

    /** @var Field[] */
    private $fields = [];

    function __construct($title, $pluginId)
    {

        // validate plugin
        if (!class_exists($pluginId)) {
            throw new \Exception(Kitx::frmt("
                Cannot make new config group. You should
                provide valid plugin class, expected value like this 
                'SomePlugin::class', given '%s'
            ", [$pluginId]));
        }

        $ref = new \ReflectionClass($pluginId);
        $pluginClassName = $ref->getName();
        $namespaces = explode('\\', $ref->getNamespaceName());
        $vendor = array_shift($namespaces);


        $this->title = $title ?: $ref->getShortName();
        $this->pluginId = $pluginClassName;
        $this->groupId = strtolower($vendor."_".$ref->getShortName());
    }


    /**
     * @return string
     */
    public function getPluginId(): string
    {
        return $this->pluginId;
    }

    /**
     * @return string
     */
    public function getGroupId(): string
    {
        return $this->groupId;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Field[]
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    /**
     * Add field to group
     *
     * @param Field $field
     * @return $this
     */
    public function addField(Field $field)
    {
        $this->fields[] = $field;
        return $this;
    }
}
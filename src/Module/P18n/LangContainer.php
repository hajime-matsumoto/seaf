<?php
namespace Seaf\Module\P18n;

use Seaf\Data\Container\ObjectiveContainer as Base;
use Seaf\Kernel\Module\FileSystemFile;


/**
 * LangContainer
 */
class LangContainer  extends Base
{
    private $p18n;
    private $parent;
    private $name;
    private $locale;

    /**
     *
     * @param FileSystemFile|array|string|null
     * @param P18n
     */
    public function __construct ($data, $locale, P18n $p18n, $parent = false, $name = null)
    {
        $this->p18n = $p18n;

        $this->locale = $locale;

        $this->name = $name;

        if ($parent instanceof self) {
            $this->parent = $parent;
        }
        if ($data instanceof FileSystemFile) {
            $this->loadArray($data->parse());
        } elseif (is_array($data)) {
            $this->loadArray($data);
        } elseif (is_string($data)) {
            $this->setString($data);
        }
    }

    /**
     * @param string
     */
    public function translate ($key)
    {
        return  $this->get($key);
    }

    /**
     * @param mixed
     * @param string
     */
    public function factory ($data, $key)
    {
        return new LangContainer($data, $this->locale, $this->p18n, $this, $key);
    }

    public function getParent ( )
    {
        return $this->parent;
    }

    public function getName ( )
    {
        return $this->name;
    }

    public function getFallBack($key)
    {
        $parent_keys = array();
        while ($parent = $this->getParent()) {
            $parent_keys[] = $parent->getName();
        }
        if ($this->locale !== $this->p18n->getDefaultLocale()) {
            return $this->p18n->getContainer(
                $this->p18n->getDefaultLocale()
            )->get($key);
        }
        return '[['.$key.']]';
    }

}

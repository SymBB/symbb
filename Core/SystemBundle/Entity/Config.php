<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="configs", uniqueConstraints={@ORM\UniqueConstraint(name="key_section", columns={"config_key", "config_section"})})
 * @ORM\Entity()
 */
class Config
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="config_key", type="string", length=30)
     */
    protected $key;

    /**
     * @ORM\Column(name="config_section", type="string", length=30, unique=false)
     */
    protected $section = 'default';

    /**
     * @ORM\Column(name="config_type", type="string", length=30, unique=false)
     */
    protected $type = 'string';

    /**
     * @ORM\Column(name="config_value_string",type="string", length=255, nullable=true)
     */
    protected $stringValue;

    /**
     * @ORM\Column(name="config_value_text",type="text", nullable=true)
     */
    protected $textValue;

    /**
     * @ORM\Column(name="config_value_datetime",type="datetime", nullable=true)
     */
    protected $datetimeValue;

    /**
     * @ORM\Column(name="config_value_int",type="integer", nullable=true)
     */
    protected $integerValue;

    /**
     * @ORM\Column(name="config_value_boolean", type="boolean", nullable=true)
     */
    protected $booleanValue;

    public function getKey()
    {
        return $this->key;
    }

    public function setKey($value)
    {
        $this->key = $value;
    }

    public function setValue($value, $type = 'string')
    {
        $this->resetValues();
        $attribut = $type . 'Value';
        $this->$attribut = $value;
        $this->type = $type;
    }

    public function resetValues()
    {
        $this->intValue = null;
        $this->datetimeValue = null;
        $this->stringValue = null;
        $this->textValue = null;
    }

    public function getValue()
    {
        $attribut = $this->type . 'Value';
        $value = $this->$attribut;

        switch ($this->type) {
            case 'integer':
                $value = (int) $value;
                break;
            case 'boolean':
                $value = (boolean) $value;
                break;
            case 'text':
            case 'string':
            default:
                $value = (string) $value;
                break;
        }
        
        return $value;
    }

    public function setSection($section)
    {
        $this->section = $section;
    }

    public function getSection()
    {
        return $this->section;
    }
}
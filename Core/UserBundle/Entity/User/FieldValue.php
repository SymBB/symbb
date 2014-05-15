<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_field_values")
 */
class FieldValue
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="SymBB\Core\UserBundle\Entity\User", inversedBy="symbbFieldValues")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="SymBB\Core\UserBundle\Entity\Field")
     * @ORM\JoinColumn(onDelete="cascade")
     */
    private $field;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $stringValue;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $textValue;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $booleanValue;

    /**
     * @ORM\Column(type="timestamp", nullable=false)
     */
    private $timestampValue = 0;

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser($value)
    {
        $this->user = $value;
    }

    public function getField()
    {
        return $this->field;
    }

    public function setField($value)
    {
        $this->field = $value;
    }

    public function setValue($value)
    {
        $field = $this->getField();
        switch ($field->getDataType()) {
            case 'string':
                $this->stringValue = (string) $value;
                break;
            case 'text':
                $this->textValue = (string) $value;
                break;
            case 'boolean':
                $this->booleanValue = (boolean) $value;
                break;
            case 'timestamp':
                $this->timestampValue = (int) $value;
                break;
        }
    }

    public function getValue()
    {
        $field = $this->getField();
        switch ($field->getDataType()) {
            case 'string':
                return (string) $this->stringValue;
                break;
            case 'text':
                return (string) $this->textValue;
                break;
            case 'boolean':
                return (boolean) $this->booleanValue;
                break;
            case 'timestamp':
                return (int) $this->timestampValue;
                break;
        }
        return null;
    }
}
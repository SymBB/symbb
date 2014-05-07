<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user_fields")
 */
class Field
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=10)
     */
    protected $dataType = 'string';

    /**
     * @ORM\Column(type="string")
     */
    protected $label = '';

    /**
     * @ORM\Column(type="boolean")
     */
    protected $displayInForum = false;

    /**
     * @ORM\Column(type="boolean")
     */
    protected $displayInMemberlist = false;

    /**
     * @ORM\Column(type="integer")
     */
    protected $position = 999;

    public function getId()
    {
        return $this->id;
    }

    public function getDataType()
    {
        return $this->dataType;
    }

    public function setDataType($value)
    {
        $this->dataType = $value;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function setLabel($value)
    {
        $this->label = $value;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($value)
    {
        $this->position = $value;
    }

    public function getFormType()
    {
        $dataType = $this->getDataType();
        $formType = 'text';
        if ($dataType == 'text') {
            $formType = 'textarea';
        } else if ($dataType == 'boolean') {
            $formType = 'checkbox';
        }
        return $formType;
    }

    public function getDisplayInForum()
    {
        return $this->displayInForum;
    }

    public function setDisplayInForum($value)
    {
        $this->displayInForum = $value;
    }

    public function getDisplayInMemberlist()
    {
        return $this->displayInMemberlist;
    }

    public function setDisplayInMemberlist($value)
    {
        $this->displayInMemberlist = $value;
    }
}
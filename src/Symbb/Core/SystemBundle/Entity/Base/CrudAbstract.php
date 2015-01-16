<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Entity\Base;

abstract class CrudAbstract
{

    public function getId()
    {
        return $this->id;
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($value)
    {
        $this->name = $value;
    }

    public function setPosition($value)
    {
        $this->position = $value;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function getExtendName()
    {
        $names = $this->getExtendNameArray();
        $name = implode(' - ', $names);
        return $name;
    }

    public function getExtendNameArray()
    {
        $names = array();
        $names[$this->getId()] = (string)$this->getName();
        $parent = $this->getParent();
        if (is_object($parent)) {
            $parentNames = $parent->getExtendNameArray();
            $names = $names + $parentNames;
        }
        $names = array_reverse($names, true);
        return $names;
    }

    public function __toString()
    {
        return $this->getExtendName();
    }

    public function getSeoName()
    {
        $name = $this->getName();
        $name = preg_replace('/\W+/', '-', $name);
        $name = strtolower(trim($name, '-'));
        return $name;
    }

}
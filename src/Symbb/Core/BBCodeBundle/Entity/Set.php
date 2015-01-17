<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\BBCodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bbcode_sets")
 * @ORM\Entity()
 */
class Set
{

    /**
     * @ORM\Column(type="string", unique=true, length=20)
     * @ORM\Id()
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\ManyToMany(targetEntity="BBCode", inversedBy="sets", cascade={"persist","remove"})
     * @ORM\JoinTable(name="bbcode_set_to_bbcode",
     *      joinColumns={@ORM\JoinColumn(name="set_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="bbcode_id", referencedColumnName="id")}
     *      )
     * @var ArrayCollection
     */
    protected $codes;

    public function __construct()
    {
        $this->codes = new ArrayCollection();
    }

    public function setId($value)
    {
        $this->id = $value;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCodes()
    {
        $finalList = array();
        $codes = $this->codes;
        foreach ($codes as $code) {
            $finalList[$code->getPosition()] = $code;
        }
        ksort($finalList);
        return $finalList;
    }

    public function addCode($code)
    {
        $this->codes->add($code);
    }

    public function setCodes($codes)
    {
        $this->codes = $codes;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
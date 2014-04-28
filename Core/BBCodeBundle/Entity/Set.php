<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\BBCodeBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bbcode_sets")
 * @ORM\Entity()
 */
class Set
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\OneToMany(targetEntity="BBCode", mappedBy="set", cascade={"persist", "remove"})
     * @ORM\OrderBy({"position" = "ASC", "id" = "ASC"})
     * @var ArrayCollection 
     */
    protected $codes;

    public function __construct()
    {
        $this->codes = new ArrayCollection();
    }

    public function getCodes()
    {
        return $this->codes;
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
}
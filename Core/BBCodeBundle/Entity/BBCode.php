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
 * @ORM\Table(name="bbcode_set_codes")
 * @ORM\Entity()
 */
class BBCode
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Set", inversedBy="children")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    protected $set;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $searchRegex;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $replaceRegex;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $image;


    public function getSet()
    {
        return $this->set;
    }

    public function setSet($set)
    {
        $this->set = $set;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getSearchRegex()
    {
        return $this->searchRegex;
    }

    public function setSearchRegex($searchRegex)
    {
        $this->searchRegex = $searchRegex;
    }

    public function getReplaceRegex()
    {
        return $this->replaceRegex;
    }

    public function setReplaceRegex($replaceRegex)
    {
        $this->replaceRegex = $replaceRegex;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }
}
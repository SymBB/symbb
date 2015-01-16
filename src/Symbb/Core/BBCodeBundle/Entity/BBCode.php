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
use JMS\Serializer\Annotation\Exclude;

/**
 * @ORM\Table(name="bbcode_codes")
 * @ORM\Entity()
 */
class BBCode extends \Symbb\Core\SystemBundle\Entity\Base\CrudAbstract
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToMany(targetEntity="Set", mappedBy="codes")
     * @Exclude
     * @var ArrayCollection
     */
    protected $sets;

    /**
     * @ORM\Column(type="string", length=255)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $jsFunction = 'BBCodeEditor.prepareDefaultBtn';

    /**
     * @ORM\Column(type="boolean")
     */
    protected $removeNewLines = false;

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
    protected $buttonRegex;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position = 999;

    public function __construct()
    {
        $this->sets = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getSets()
    {
        return $this->sets;
    }

    public function addSet($set)
    {
        $this->sets->add($set);
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

    public function getRemoveNewLines()
    {
        return $this->removeNewLines;
    }

    public function setRemoveNewLines($value)
    {
        $this->removeNewLines = $value;
    }

    public function getReplaceRegex()
    {
        return $this->replaceRegex;
    }

    public function setReplaceRegex($replaceRegex)
    {
        $this->replaceRegex = $replaceRegex;
    }

    public function getButtonRegex()
    {
        return $this->buttonRegex;
    }

    public function setButtonRegex($buttonRegex)
    {
        $this->buttonRegex = $buttonRegex;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setJsFunction($value)
    {
        $this->jsFunction = $value;
    }

    public function getJsFunction()
    {
        return $this->jsFunction;
    }
}
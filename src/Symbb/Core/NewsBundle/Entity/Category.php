<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symbb\Core\NewsBundle\Entity\Category\Entry;
use Symbb\Core\NewsBundle\Entity\Category\Source;

/**
 * @ORM\Entity
 * @ORM\Table(name="news_categories")
 */
class Category
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="\Symbb\Core\ForumBundle\Entity\Forum")
     * @ORM\JoinColumn(nullable=false)
     * @var \Symbb\Core\ForumBundle\Entity\Forum
     */
    protected $targetForum;

    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\NewsBundle\Entity\Category\Source", mappedBy="category",cascade={"persist"})
     * @var ArrayCollection
     */
    protected $sources;

    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\NewsBundle\Entity\Category\Entry", mappedBy="category",cascade={"persist"})
     * @ORM\OrderBy({"created" = "DESC"})
     * @var ArrayCollection
     */
    protected $entries;

    public function __construct(){
        $this->sources = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return \Symbb\Core\ForumBundle\Entity\Forum
     */
    public function getTargetForum()
    {
        return $this->targetForum;
    }

    /**
     * @param \Symbb\Core\ForumBundle\Entity\Forum $targetForum
     */
    public function setTargetForum($targetForum)
    {
        $this->targetForum = $targetForum;
    }

    /**
     * @return Source[]
     */
    public function getSources()
    {
        return $this->sources;
    }

    /**
     * @param ArrayCollection $sources
     */
    public function setSources($sources)
    {
        $this->sources = $sources;
    }

    /**
     * @param Source $source
     */
    public function addSource(Source $source){
        $this->sources->add($source);
    }

    /**
     * @return Entry
     */
    public function getEntries()
    {
        return $this->entries;
    }

    /**
     * @param Entry[] $entries
     */
    public function setEntries($entries)
    {
        $this->entries = $entries;
    }
}
<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Entity\Category;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symbb\Core\NewsBundle\Entity\Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="news_category_sources")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"email" = "Symbb\Core\NewsBundle\Entity\Category\Source\Email", "feed" = "Symbb\Core\NewsBundle\Entity\Category\Source\Feed"})
 */
class Source
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $lastCall;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\NewsBundle\Entity\Category", inversedBy="sources")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     * @var Category
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\NewsBundle\Entity\Category\Entry", mappedBy="source")
     * @ORM\OrderBy({"created" = "DESC"})
     * @var ArrayCollection
     */
    protected $entries;

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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return \DateTime
     */
    public function getLastCall()
    {
        return $this->lastCall;
    }

    /**
     * @param \DateTime $lastCall
     */
    public function setLastCall(\DateTime $lastCall)
    {
        $this->lastCall = $lastCall;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
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
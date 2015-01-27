<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Entity\Category;

use Doctrine\ORM\Mapping as ORM;
use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\ForumBundle\Entity\Topic;
use Symbb\Core\NewsBundle\Entity\Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="news_category_entries")
 */
class Entry
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     * @var integer
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\NewsBundle\Entity\Category", inversedBy="entries")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     * @var Category
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\NewsBundle\Entity\Category\Source", inversedBy="entries")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     * @var Category\Source
     */
    protected $source;

    /**
     * @ORM\OneToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Topic")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     * @var Topic
     */
    protected $topic;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $created;

    /**
     * @return string
     */
    public function getTitle(){
        return $this->topic->getName();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return Source
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * @param Source $source
     */
    public function setSource($source)
    {
        $this->source = $source;
    }

    /**
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @param Topic $topic
     */
    public function setTopic($topic)
    {
        $this->topic = $topic;
    }

    /**
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * @param \DateTime $created
     */
    public function setCreated($created)
    {
        $this->created = $created;
    }


}
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
     * @ORM\JoinColumn(nullable=true)
     * @var Topic
     */
    protected $topic;

    /**
     * @ORM\Column(type="datetime", nullable=false)
     * @var \DateTime
     */
    protected $date;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    protected $type;

    /**
     * @ORM\Column(type="string", nullable=false)
     * @var string
     */
    protected $title;


    /**
     * @ORM\Column(type="text", nullable=false)
     * @var string
     */
    protected $text;

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
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param string $text
     */
    public function setText($text)
    {
        $this->text = $text;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTime $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }


}
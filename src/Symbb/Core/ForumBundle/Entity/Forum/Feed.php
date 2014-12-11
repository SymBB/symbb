<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Entity\Forum;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="forum_feeds")
 * @ORM\Entity()
 */
class Feed
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $url = "";

    /**
     * @ORM\Column(type="string")
     */
    private $filterRegex = "";

    /**
     * @ORM\Column(type="string")
     */
    private $linkRegex = "";

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Forum", inversedBy="feeds")
     * @ORM\JoinColumn(name="forum_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $forum;

    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\ForumBundle\Entity\Forum\Feed\Entry", mappedBy="feed", orphanRemoval=true, cascade={"persist"})
     * @var ArrayCollection
     */
    protected $entries;

    public function __construct(){
        $this->entries = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getForum()
    {
        return $this->forum;
    }

    /**
     * @param mixed $forum
     */
    public function setForum($forum)
    {
        $this->forum = $forum;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getFilterRegex()
    {
        return $this->filterRegex;
    }

    /**
     * @param mixed $filterRegex
     */
    public function setFilterRegex($filterRegex)
    {
        $this->filterRegex = $filterRegex;
    }

    /**
     * @return mixed
     */
    public function getLinkRegex()
    {
        return $this->linkRegex;
    }

    /**
     * @param mixed $linkRegex
     */
    public function setLinkRegex($linkRegex)
    {
        $this->linkRegex = $linkRegex;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @param mixed $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function __toString(){
        return "FeedId ( ".$this->id." )";
    }

}
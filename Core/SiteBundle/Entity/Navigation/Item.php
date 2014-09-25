<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Entity\Navigation;

use Doctrine\ORM\Mapping as ORM;
use SymBB\Core\SiteBundle\Entity\Navigation;

/**
 * @ORM\Table(name="site_navigation_items")
 * @ORM\Entity()
 */
class Item
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="SymBB\Core\SiteBundle\Entity\Navigation", inversedBy="items", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    protected $navigation;

    /**
     * @ORM\ManyToOne(targetEntity="SymBB\Core\SiteBundle\Entity\Navigation\Item", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id")
     */
    protected $parentItem;

    /**
     * @ORM\OneToMany(targetEntity="SymBB\Core\SiteBundle\Entity\Navigation\Item", mappedBy="parentItem", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $children;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @ORM\Column(type="string")
     */
    protected $type = '';

    /**
     * @ORM\Column(type="string")
     */
    protected $symfonyRoute = "";

    /**
     * @ORM\Column(type="string")
     */
    protected $symfonyRouteParams = "";

    /**
     * @ORM\Column(type="string")
     */
    protected $fixUrl = "";

    /**
     * @ORM\Column(type="integer")
     */
    protected $position = 999;

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $fixUrl
     */
    public function setFixUrl($fixUrl)
    {
        $this->fixUrl = $fixUrl;
    }

    /**
     * @return string
     */
    public function getFixUrl()
    {
        return $this->fixUrl;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Navigation $navigation
     */
    public function setNavigation(Navigation $navigation)
    {
        $this->navigation = $navigation;
    }

    /**
     * @return Navigation
     */
    public function getNavigation()
    {
        return $this->navigation;
    }

    /**
     * @param integer $position
     */
    public function setPosition($position)
    {
        $this->position = $position;
    }

    /**
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param string $symfonyRoute
     */
    public function setSymfonyRoute($symfonyRoute)
    {
        $this->symfonyRoute = $symfonyRoute;
    }

    /**
     * @return string
     */
    public function getSymfonyRoute()
    {
        return $this->symfonyRoute;
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
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param array Item[]
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return Item[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param mixed Item
     */
    public function setParentItem(Item $parentItem)
    {
        $this->parentItem = $parentItem;
    }

    /**
     * @return Item
     */
    public function getParentItem()
    {
        return $this->parentItem;
    }

    /**
     * @return bool
     */
    public function hasChildren(){
        $childs = $this->getChildren();
        if(!empty($childs)){
            return true;
        }
        return false;
    }

    /**
     * @param mixed $symfonyRouteParams
     */
    public function setSymfonyRouteParams($symfonyRouteParams)
    {
        $symfonyRouteParams = json_encode($symfonyRouteParams);
        $this->symfonyRouteParams = $symfonyRouteParams;
    }

    /**
     * @return mixed
     */
    public function getSymfonyRouteParams()
    {
        $params = $this->symfonyRouteParams;
        if(empty($params)){
            $params = array();
        } else {
            $params = json_decode($params, true);
        }
        if(!is_array($params)){
            $params = array();
        }

        return $params;
    }

}
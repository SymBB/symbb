<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="site_navigations", uniqueConstraints={@ORM\UniqueConstraint(name="main_identifier", columns={"nav_key", "site_id"})})
 * @ORM\Entity()
 */
class Navigation
{


    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="nav_key", type="string")
     */
    protected $navKey = 'main';

    /**
     * @ORM\ManyToOne(targetEntity="Symbb\Core\SiteBundle\Entity\Site", inversedBy="navigations", cascade={"persist"})
     * @ORM\JoinColumn(referencedColumnName="id", nullable=false)
     */
    protected $site;

    /**
     * @ORM\OneToMany(targetEntity="Symbb\Core\SiteBundle\Entity\Navigation\Item", mappedBy="navigation", cascade={"all"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $items;

    /**
     * @ORM\Column(type="string")
     */
    protected $title;

    /**
     * @param mixed $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return mixed
     */
    public function getItems()
    {
        $items = $this->items;
        $itemFinal = array();
        foreach ($items as $item) {
            if (!$item->getParentItem()) {
                $itemFinal[] = $item;
            }
        }

        return $itemFinal;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return mixed
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $naviKey
     */
    public function setNavKey($naviKey)
    {
        $this->navKey = $naviKey;
    }

    /**
     * @return mixed
     */
    public function getNavKey()
    {
        return $this->navKey;
    }


}
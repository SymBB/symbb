<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="site_navigations")
 * @ORM\Entity()
 */
class Navigation
{

    /**
     * @ORM\Column(type="string", unique=true)
     * @ORM\Id()
     */
    protected $id = 'main';

    /**
     * @ORM\ManyToOne(targetEntity="SymBB\Core\SiteBundle\Entity\Site", inversedBy="navigations")
     * @ORM\JoinColumn(referencedColumnName="id", onDelete="CASCADE")
     */
    protected $site;

    /**
     * @ORM\OneToMany(targetEntity="SymBB\Core\SiteBundle\Entity\Navigation\Item", mappedBy="navigation")
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
        foreach($items as $item){
            if(!$item->getParentItem()){
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

}
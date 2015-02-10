<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\NewsBundle\Entity\Category\Source;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symbb\Core\NewsBundle\Entity\Category;

/**
 * @ORM\Entity
 * @ORM\Table(name="news_category_sources_feed")
 */
 class Feed extends Category\Source
{

    /**
    * @ORM\Column(type="string", nullable=false)
    * @var string
    */
    protected $url;

     /**
      * @return string
      */
     public function getUrl()
     {
         return $this->url;
     }

     /**
      * @param string $url
      */
     public function setUrl($url)
     {
         $this->url = $url;
     }



}

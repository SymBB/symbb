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
     * @ORM\OneToOne(targetEntity="Symbb\Core\ForumBundle\Entity\Post")
     * @ORM\JoinColumn(onDelete="cascade", nullable=false)
     * @var Post
     */
    protected $post;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var \DateTime
     */
    protected $created;


}
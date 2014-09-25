<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Template\DefaultBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="template_default_slider")
 * @ORM\Entity()
 */
class Slider
{

    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", nullable=false)
     */
    private $text;

    public function getId()
    {
        return $this->id;

    }

    public function setId($value)
    {
        $this->id = $value;

    }

    public function setText($value)
    {
        $this->text = $value;

    }

    public function getText()
    {
        return $this->text;

    }

}
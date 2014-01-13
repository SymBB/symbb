<?php
/**
*
* @package symBB
* @copyright (c) 2013 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\SystemBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="configs")
 * @ORM\Entity()
 */
class Config 
{
    /**
     * @ORM\Column(type="integer", unique=true)
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="config_key", type="string", length=30, unique=true)
     */
    protected $key;

    /**
     * @ORM\Column(name="config_value",type="string", length=255)
     */
    protected $value;


    ############################################################################
    # Default Get and Set
    ############################################################################
    public function getKey(){return $this->key;}
    public function setKey($value){$this->key = $value;}
    public function getValue(){return $this->value;}
    public function setValue($value){$this->value = $value;}
    ############################################################################
}
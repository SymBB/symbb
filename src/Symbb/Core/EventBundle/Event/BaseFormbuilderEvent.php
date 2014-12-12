<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace Symbb\Core\EventBundle\Event;

use Symfony\Component\EventDispatcher\Event;

class BaseFormbuilderEvent extends Event
{
    
    protected $builder;


    public function __construct($builder) {
        $this->builder = $builder;
    }
    
    public function getBuilder(){
        return $this->builder;
    }
}

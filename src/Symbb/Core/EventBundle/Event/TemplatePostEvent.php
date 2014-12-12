<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace Symbb\Core\EventBundle\Event;

use \Symbb\Core\ForumBundle\Entity\Post;

class TemplatePostEvent extends BaseTemplateEvent
{


    public function __construct($env) {
        $this->env = $env;
    }
}

<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Core\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;


class DisplayController extends Controller 
{
    
	public function indexAction(){
        return $this->render(
            'SymBBTemplateAcpBundle:Forum:list.html.twig',
            array()
        );
	}
    
}
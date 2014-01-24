<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\UserBundle\Controller;

use \SymBB\Core\UserBundle\Form\Type\Option;
use \SymBB\Core\UserBundle\Form\Type\SecurityOption;

class InfoController extends \SymBB\Core\SystemBundle\Controller\AbstractController 
{

    public function userlistAction(){
        
        $users = $this->get('symbb.core.user.manager')->findAll();
        
        return $this->render(
            $this->getTemplateBundleName('forum').':User:userlist.html.twig',
            array('users' => $users)
        );
        
	}
    
}
<?
/**
*
* @package symBB
* @copyright (c) 2013 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\SystemBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class AbstractController extends Controller 
{
    protected $templateBundle;
   

    protected function getTemplateBundleName($for = 'forum')
    {
        if ($this->templateBundle === null) {
            $this->templateBundle = $this->container->get('symbb.core.config.manager')->get('template.'.$for);
        }
        return $this->templateBundle;

    }
    
}
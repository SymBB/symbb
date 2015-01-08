<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace Symbb\Extension\RatingBundle\Twig;

class UtilExtension extends \Twig_Extension
{
    protected $em;
    
    protected $securityContect;
    
    public function __construct($em, $securityContext) {
        $this->em               = $em;
        $this->securityContect  = $securityContext;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('getSymbbLikeCount', array($this, 'getSymbbLikeCount')),
            new \Twig_SimpleFunction('getSymbbDislikeCount', array($this, 'getSymbbDislikeCount')),
            new \Twig_SimpleFunction('checkSymbbExtensionRatingLike', array($this, 'checkForLike')),
            new \Twig_SimpleFunction('checkSymbbExtensionRatingDislike', array($this, 'checkForDislike'))
        );
    }

    public function getSymbbLikeCount(\Symbb\Core\ForumBundle\Entity\Post $post)
    {
        $likes = $this->em->getRepository('SymbbExtensionRatingBundle:Like', 'symbb')
            ->findBy(array('post' => $post));
        $count = count($likes);
        return $count;
    }

    public function getSymbbDislikeCount(\Symbb\Core\ForumBundle\Entity\Post $post)
    {
        $likes      = $this->em->getRepository('SymbbExtensionRatingBundle:Dislike', 'symbb')
            ->findBy(array('post' => $post));
        $count = count($likes);
        return $count;
    }
    
    public function checkForLike(\Symbb\Core\ForumBundle\Entity\Post $post){
        $check = false;
        if(is_object($this->getCurrentUser())){
            $found = $this->em->getRepository('SymbbExtensionRatingBundle:Like', 'symbb')
            ->findOneBy(array('user' => $this->getCurrentUser(), 'post' => $post));
            if(is_object($found)){
                $check = true;
            }
        }
        return $check;
    }

    public function checkForDislike(\Symbb\Core\ForumBundle\Entity\Post $post){
        $check = false;
        if(is_object($this->getCurrentUser())){
            $found = $this->em->getRepository('SymbbExtensionRatingBundle:Dislike', 'symbb')
                ->findOneBy(array('user' => $this->getCurrentUser(), 'post' => $post));
            if(is_object($found)){
                $check = true;
            }
        }
        return $check;
    }
    
    public function getCurrentUser(){
        $user   = null;
        $token  = $this->securityContect->getToken();
        if(is_object($token)){
            $user  = $token->getUser();
        }
        return $user;
    }
    
    public function getName()
    {
        return 'symbb_post_rating_util';
    }
}
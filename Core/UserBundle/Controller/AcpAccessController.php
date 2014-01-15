<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/
namespace SymBB\Core\UserBundle\Controller;

use Symfony\Component\Validator\Constraints\NotBlank;

class AcpAccessController extends \SymBB\Core\SystemBundle\Controller\AbstractController 
{
    
    public function groupAction($step){
        $name = "groupStep".(int)$step.'Action';
        return $this->$name($step);
    }
    
    public function groupStep1Action(){
        
        $groups      = $this->get('doctrine')->getRepository('SymBBCoreUserBundle:Group', 'symbb')->findAll();
        $forumList   = $this->get('symbb.core.forum.manager')->getSelectList();
        $groupList   = array();
        foreach($groups as $group){
            $groupList[$group->getId()] = $group->getName();
        }
        
        $defaultData = array('name' => 'step1');
        $form = $this->get('form.factory')->createNamedBuilder('step1', 'form', $defaultData, array('translation_domain' => 'symbb_backend'))
            ->setAction($this->generateUrl('_symbbcoreuserbundle_group_access', array('step' => 1)))
            ->add('group', 'choice', array('choices' => $groupList, 'constraints' => array(
                    new NotBlank()
                )))
            ->add('forum', 'choice', array(
                'choices' => $forumList, 
                'multiple' => true,
                'constraints' => array(
                    new NotBlank()
                )))
            ->add('subforum', 'checkbox', array('required' => false))
            ->add('next', 'submit', array('attr' => array('class' => 'btn-success')))
            ->add('back', 'submit', array('attr' => array('class' => 'btn-danger')))
            ->getForm();
        
        $form->handleRequest($this->get('request'));
        
        if ($form->isValid()) {
            return $this->groupStep2Action();
        } else {
            return $this->render(
                $this->getTemplateBundleName('acp').':Acp:Group\accessS1.html.twig',
                array('form' => $form->createView())
            );
        }
	}
    
    protected function groupStep2Action(){
        
        $request    = $this->get('request');
        $step1Data  = $request->get('step1');
        $step2Data  = $request->get('step2');
        $groupId    = 0;
        $forumIds   = array();
        $subforum   = false;
        
        if(!empty($step1Data)){
            $groupId    = $step1Data['group'];
            $forumIds   = $step1Data['forum'];
            if(isset($step1Data['subforum'])){
                $subforum   = $step1Data['subforum'];
            }
        } else if (!empty($step2Data)) {
            $groupId    = $step2Data['group'];
            $forumIds   = \explode(',', $step2Data['forum']);
            $subforum   = $step2Data['subforum'];
        }
        
        if(is_array($forumIds) && !empty($forumIds) && $groupId > 0){
            
            $group = $this->get('doctrine')->getRepository('SymBBCoreUserBundle:Group', 'symbb')
                ->find($groupId);
            
            $defaultData = array();
            $form = $this->get('form.factory')->createNamedBuilder('step2', 'form', $defaultData, array('translation_domain' => 'symbb_backend'))
                ->setAction($this->generateUrl('_symbbcoreuserbundle_group_access', array('step' => 2)))
                ->add('save', 'submit', array('attr' => array('class' => 'btn-success')))
                ->add('back', 'submit', array('attr' => array('class' => 'btn-danger')))
                ->add('subforum', 'hidden', array('data' => (int)$subforum))
                ->add('group', 'hidden', array('data' => (int)$groupId))
                ->add('forum', 'hidden', array('data' => implode(',', $forumIds)))
                ->getForm();

            $form->handleRequest($this->get('request'));

            if($form->get('back')->isClicked()){
                return $this->redirect($this->generateUrl('_symbbcoreuserbundle_group_access', array('step' => 1)));
            }
            
            if ($form->isValid()) {

                foreach($forumIds as $forumId){
                    $access = $request->get('access_'.$forumId);
                    $forum = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
                        ->find($forumId);
                    $this->get('symbb.core.access.manager')->removeAllAccess($forum, $group);
                    foreach((array)$access as $permission => $value){
                        $this->get('symbb.core.access.manager')->grantAccess($permission, $forum, $group);
                    }
                }
                
            }
            
            $forumList = $this->get('doctrine')->getRepository('SymBBCoreForumBundle:Forum', 'symbb')
                ->findBy(array('id' =>$forumIds));
            
            return $this->render(
                $this->getTemplateBundleName('acp').':Acp:Group\accessS2.html.twig',
                array(
                    'group' => $group, 
                    'forumList' => $forumList,
                    'subforum' => $subforum ,
                    'form' => $form->createView(), 
                    'accessManager' => $this->get('symbb.core.access.manager')
                    )
            );
            
        } else {
            return $this->redirect($this->generateUrl('_symbbcoreuserbundle_group_access', array('step' => 1)));
        }
        
    }
    
}
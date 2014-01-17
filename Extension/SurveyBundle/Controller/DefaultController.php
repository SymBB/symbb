<?
/**
*
* @package symBB
* @copyright (c) 2013-2014 Christian Wielath
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace SymBB\Extension\SurveyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;


class DefaultController  extends Controller 
{
    
    public function voteAction($name, $post){
        
        
        $em = $this->get('doctrine')->getManager('symbb');
        
        $post = $em->getRepository('SymBBCoreForumBundle:Post')
            ->find($post);
        
        $user       = $this->getUser();
        $request    = $this->get('request');
        $answers    = (array)$request->get('surveyAnswer');
        $error      = '';
        
        if(!empty($answers) && is_object($user) && $user->getId() > 0){
            
            $survey = $em->getRepository('SymBBExtensionSurveyBundle:Survey')
            ->findOneBy(array('post' => $post));
            
            if(is_object($survey) && $survey->checkIfVoteable($user)){
                
                $votes = $em->getRepository('SymBBExtensionSurveyBundle:Vote')
                ->findBy(array('survey' => $survey, 'user' => $user));
                
                $currentVotes = array();
                
                if(count($answers) <= $survey->getChoices()){
                
                    foreach($answers as $answer){

                        $voteFound = null;

                        foreach($votes as $vote){
                            if($vote->getAnswer() == (int)$answer){
                                $currentVotes[] = $vote->getId();
                                $voteFound = $vote;
                                break;
                            }
                        }

                        if(!is_object($voteFound)){
                            $voteFound = new \SymBB\Extension\SurveyBundle\Entity\Vote();
                            $voteFound->setSurvey($survey);
                            $voteFound->setAnswer($answer);
                            $voteFound->setUser($user);
                            $survey->addVote($voteFound);
                            $em->persist($voteFound);
                        }
                    }

                    foreach($votes as $vote){
                        if(!in_array($vote->getId(), $currentVotes)){
                            $em->remove($vote);
                        }
                    }
                    
                } else {
                    $error = $this->get('translator')->trans('You can vote for a maximum of %count% answers.', array('%count%' => $survey->getChoices()), 'symbb_frontend');
                    $this->get('session')->getFlashBag()->add(
                        'symbb-extension-survey-error',
                        $error
                    );
                }
            }
            
            $em->persist($survey);

            $em->flush();
            
        }
        
        $response = $this->forward('SymBBCoreForumBundle:FrontendTopic:show', array(
            'name'  => '',
            'id' => $post->getTopic()->getId(),
        ));
        
        return $response;
    }
    
}
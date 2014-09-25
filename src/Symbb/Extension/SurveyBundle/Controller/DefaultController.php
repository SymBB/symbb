<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends \Symbb\Core\SystemBundle\Controller\AbstractApiController
{

    public function voteAction()
    {


        $votedItems = $this->get('request')->get('items');
        $votedItems = \str_replace(array('[', ']', '"'), '', $votedItems);
        $answers = \explode(',', $votedItems);

        $postId = (int) $this->get('request')->get('post');

        if ($postId > 0) {

            $em = $this->get('doctrine')->getManager('symbb');

            $post = $em->getRepository('SymbbCoreForumBundle:Post')
                ->find($postId);

            $user = $this->getUser();

            if (!empty($answers) && is_object($user) && $user->getId() > 0) {

                $survey = $em->getRepository('SymbbExtensionSurveyBundle:Survey')
                    ->findOneBy(array('post' => $post));

                if (is_object($survey) && $survey->checkIfVoteable($user)) {

                    $votes = $em->getRepository('SymbbExtensionSurveyBundle:Vote')
                        ->findBy(array('survey' => $survey, 'user' => $user));

                    $currentVotes = array();

                    if($survey->getChoices() > 1){
                        foreach ($answers as $key => $answer) {
                            if ((int) $answer !== 1) {
                                unset($answers[$key]);
                            }
                        }
                    } else {
                        $answers = array_flip($answers);
                    }

                    if (count($answers) <= $survey->getChoices()) {

                        foreach ($answers as $key => $answer) {

                            $voteFound = null;

                            foreach ($votes as $vote) {
                                if ($vote->getAnswer() === (int) $key) {
                                    $currentVotes[] = $vote->getId();
                                    $voteFound = $vote;
                                    break;
                                }
                            }

                            if (!is_object($voteFound)) {
                                $voteFound = new \Symbb\Extension\SurveyBundle\Entity\Vote();
                                $voteFound->setSurvey($survey);
                                $voteFound->setAnswer($key);
                                $voteFound->setUser($user);
                                $survey->addVote($voteFound);
                                $em->persist($voteFound);
                            }
                        }

                        foreach ($votes as $vote) {
                            if (!in_array($vote->getId(), $currentVotes)) {
                                $em->remove($vote);
                            }
                        }

                        $em->persist($survey);
                        $em->flush();

                        $this->addCallback('refresh');
                    } else {
                        $this->addErrorMessage("The answer count do not match");
                    }
                } else {
                    $this->addErrorMessage("You can not vote");
                }
            } else {
                $this->addErrorMessage("No response");
            }
        } else {
            $this->addErrorMessage("Post was not detected.");
        }

        return $this->getJsonResponse(array());
    }
}
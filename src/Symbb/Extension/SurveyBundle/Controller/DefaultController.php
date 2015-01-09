<?
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\Controller;

use Symbb\Core\SystemBundle\Controller\AbstractController;
use Symbb\Extension\SurveyBundle\Security\Authorization\SurveyVoter;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractController
{

    public function voteAction(Request $request)
    {
        $postId = $request->get("post");
        $answers = $request->get("answers");
        $user = $this->getUser();
        $post = $this->get('symbb.core.post.manager')->find($postId);

        if($post !== null){
            if (!$this->get('security.authorization_checker')->isGranted(SurveyVoter::VIEW_SURVEY, $post->getTopic()->getForum())) {
                throw $this->createAccessDeniedException();
            }

            $em = $this->get("doctrine")->getEntityManager();

            $survey = $em->getRepository('SymbbExtensionSurveyBundle:Survey')
                ->findOneBy(array('post' => $post));

            if (is_object($survey) && $survey->checkIfVoteable($user)) {
                $votes = $this->get("doctrine")->getRepository('SymbbExtensionSurveyBundle:Vote')
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

                    $this->addSuccess("you have voted successful.", $request);

                } else {
                    $this->addError("The answer count do not match.", $request);
                }
            } else {
                $this->addError("you cannot vote.", $request);
            }
        } else {
            $this->addError("post not found.", $request);
        }

        return $this->returnToLastPage($request);
    }

}
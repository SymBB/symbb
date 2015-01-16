<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\RatingBundle\Controller;

use Symbb\Core\SystemBundle\Controller\AbstractApiController;
use Symbb\Extension\RatingBundle\Security\Authorization\RatingVoter;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends AbstractApiController
{

    public function ratePostApiAction($id, $like)
    {

        $post = $this->get('doctrine')->getRepository('SymbbCoreForumBundle:Post', 'symbb')
            ->find($id);

        if (is_object($post) && $post->getId() > 0) {
            $topic = $post->getTopic();
            if (is_object($topic) && $topic->getId() > 0) {
                $forum = $topic->getForum();
                if (is_object($forum) && $forum->getId() > 0) {
                    $user = $this->getUser();
                    if (is_object($user) && $user->getId() > 0 && $user->getSymbbType() === 'user') {
                        $createSurvey = $this->get('security.context')->isGranted(RatingVoter::CREATE_RATING, $forum);
                        if ($createSurvey) {
                            if ($like === 'like') {
                                $this->addPostLike($post, $user);
                            } else {
                                $this->addPostLike($post, $user, true);
                            }
                            $this->addCallback('refresh');
                        } else {
                            $this->addErrorMessage("no access");
                        }
                    } else {
                        $this->addErrorMessage("user not found");
                    }
                } else {
                    $this->addErrorMessage("forum not found");
                }
            } else {
                $this->addErrorMessage("topic not found");
            }
        } else {
            $this->addErrorMessage("post not found");
        }

        return $this->getJsonResponse(array());
    }

    protected function addPostLike(
        \Symbb\Core\ForumBundle\Entity\Post $post, \Symbb\Core\UserBundle\Entity\UserInterface $user, $asDislike = false
    )
    {

        if (!$this->get('security.authorization_checker')->isGranted(RatingVoter::CREATE_RATING, $post->getTopic()->getForum(), $user)) {
            throw $this->createAccessDeniedException();
        }

        $likes = $this->get('doctrine')->getRepository('SymbbExtensionRatingBundle:Like', 'symbb')
            ->findBy(array('post' => $post, 'user' => $user));

        $dislikes = $this->get('doctrine')->getRepository('SymbbExtensionRatingBundle:Dislike', 'symbb')
            ->findBy(array('post' => $post, 'user' => $user));

        $myLikes = array();
        $myDislikes = array();

        $em = $this->get('doctrine')->getManager('symbb');

        foreach ($likes as $like) {
            if ($like->getUser()->getId() === $user->getId()) {
                $myLikes[] = $like;
            }
        }

        foreach ($dislikes as $dislike) {
            if ($dislike->getUser()->getId() === $user->getId()) {
                $myDislikes[] = $dislike;
            }
        }

        // if the user "like" it
        if (!$asDislike) {

            // remove "dislikes"
            foreach ($myDislikes as $myDislike) {
                //$post->removeDislike($myDislike);
                $em->remove($myDislike);
            }

            // create a new "like" if no one exist
            if (empty($myLikes)) {
                $myLike = new \Symbb\Extension\RatingBundle\Entity\Like();
                $myLike->setUser($user);
                $myLike->setPost($post);
                $em->persist($myLike);
                // i again then delete
            } else {
                foreach ($myLikes as $myLike) {
                    $em->remove($myLike);
                }
            }

        } else {
            // remove "likes"
            foreach ($myLikes as $myLike) {
                //$post->removeLike($myLike);
                $em->remove($myLike);
            }

            // create a new "dislike" if no one exist
            if (empty($myDislikes)) {
                $myDislike = new \Symbb\Extension\RatingBundle\Entity\Dislike();
                $myDislike->setUser($user);
                $myDislike->setPost($post);
                $em->persist($myDislike);
                // i again then delete
            } else {
                foreach ($myDislikes as $myDislike) {
                    $em->remove($myDislike);
                }
            }
        }

        $em->flush();
    }

    public function ratePostAction(Request $request, $id, $like)
    {
        $this->ratePostApiAction($id, $like);
        return $this->returnToLastPage($request);
    }
}
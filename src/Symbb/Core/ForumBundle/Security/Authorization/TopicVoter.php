<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Security\Authorization;

use Symbb\Core\SystemBundle\Security\Authorization\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TopicVoter extends AbstractVoter implements VoterInterface
{
    protected $supportedClasses = array('Symbb\Core\ForumBundle\Entity\Topic');

    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const MOVE = 'move';
    const SPLIT = 'split';
    const REPLY = 'reply';

    public function getGroupedAttributes()
    {
        return array(
            AbstractVoter::GROUP_DEFAULT => array(
                self::VIEW,
                self::EDIT,
                self::DELETE,
                self::MOVE,
                self::SPLIT,
                self::REPLY,
            )
        );
    }

    /**
     * @param TokenInterface $token
     * @param object $object
     * @param array $attributes
     * @return int
     */
    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $check = parent::vote($token, $object, $attributes);
        if($check !== null){
            return $check;
        }

        // get current logged in user
        $user = $token->getUser();

        // set the attribute to check against
        $attribute = strtolower($attributes[0]);

        switch($attribute) {
            case self::VIEW:
                $forum = $object->getForum();
                $this->accessManager->addVoterAccessCheck(ForumVoter::VIEW, $forum);
                // the data object could have for example a method isPrivate()
                // which checks the Boolean attribute $private
                if ($this->accessManager->hasVoterAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::EDIT:
                if ($user->getId() === $object->getAuthor()->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                $forum = $object->getForum();
                $this->accessManager->addVoterAccessCheck(ForumVoter::EDIT_TOPIC, $forum);
                if ($this->accessManager->hasVoterAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::DELETE:
                if ($user->getId() === $object->getAuthor()->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                $forum = $object->getForum();
                $this->accessManager->addVoterAccessCheck(ForumVoter::DELETE_TOPIC, $forum);
                if ($this->accessManager->hasVoterAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::MOVE:
                $forum = $object->getForum();
                $this->accessManager->addVoterAccessCheck(ForumVoter::MOVE_TOPIC, $forum);
                if ($this->accessManager->hasVoterAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::SPLIT:
                $forum = $object->getForum();
                $this->accessManager->addVoterAccessCheck(ForumVoter::SPLIT_TOPIC, $forum);
                if ($this->accessManager->hasVoterAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case self::REPLY:
                $forum = $object->getForum();
                $this->accessManager->addVoterAccessCheck(ForumVoter::CREATE_POST, $forum);
                if ($this->accessManager->hasVoterAccess() && !$object->isLocked()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
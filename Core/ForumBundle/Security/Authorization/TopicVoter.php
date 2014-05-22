<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\ForumBundle\Security\Authorization;

use SymBB\Core\SystemBundle\DependencyInjection\AccessManager;
use SymBB\Core\SystemBundle\Security\Authorization\AbstractVoter;
use SymBB\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class TopicVoter extends AbstractVoter implements VoterInterface
{
    protected $supportedClasses = array('SymBB\Core\ForumBundle\Entity\Topic');

    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    const MOVE = 'move';
    const SPLIT = 'split';

    public function getGroupedAttributes()
    {
        return array(
            AbstractVoter::GROUP_DEFAULT => array(
                self::VIEW,
                self::EDIT,
                self::DELETE,
                self::MOVE,
                self::SPLIT,
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
        if($check === self::ACCESS_GRANTED){
            return $check;
        }

        // get current logged in user
        $user = $token->getUser();

        // set the attribute to check against
        $attribute = $attributes[0];

        switch($attribute) {
            case 'view':
                $forum = $object->getForum();
                $this->accessManager->addAccessCheck('VIEW', $forum);
                // the data object could have for example a method isPrivate()
                // which checks the Boolean attribute $private
                if ($this->accessManager->hasAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case 'edit':
                if ($user->getId() === $object->getAuthor()->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                $forum = $object->getForum();
                $this->accessManager->addAccessCheck('EDIT_TOPIC', $forum);
                if ($this->accessManager->hasAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case 'delete':
                if ($user->getId() === $object->getAuthor()->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                $forum = $object->getForum();
                $this->accessManager->addAccessCheck('DELETE_TOPIC', $forum);
                if ($this->accessManager->hasAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case 'move':
                $forum = $object->getForum();
                $this->accessManager->addAccessCheck('MOVE_TOPIC', $forum);
                if ($this->accessManager->hasAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;

            case 'split':
                $forum = $object->getForum();
                $this->accessManager->addAccessCheck('SPLIT_TOPIC', $forum);
                if ($this->accessManager->hasAccess()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_ABSTAIN;
    }
}
<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\SurveyBundle\Security\Authorization;

use Symbb\Core\ForumBundle\Entity\Post;
use Symbb\Core\ForumBundle\Entity\Topic;
use Symbb\Core\SystemBundle\Security\Authorization\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class SurveyVoter extends AbstractVoter implements VoterInterface
{
    protected $supportedClasses = array('Symbb\Core\ForumBundle\Entity\Forum');

    const CREATE_SURVEY = 'create_survey';
    const VIEW_SURVEY = 'view_survey';

    public function getGroupedAttributes()
    {
        return array(
            AbstractVoter::GROUP_USER => array(
                self::CREATE_SURVEY,
                self::VIEW_SURVEY,
            )
        );
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        $check = parent::vote($token, $object, $attributes);
        if($check){
            return $check;
        }

        // get current logged in user
        $user = $token->getUser();

        // set the attribute to check against
        $attribute = $attributes[0];

        if($object instanceof Post){
            $forum = $object->getTopic()->getForum();
            $this->accessManager->addAccessCheck($attribute, $forum);
            if ($this->accessManager->hasAccess()) {
                return VoterInterface::ACCESS_GRANTED;
            }
        } else if($object instanceof Topic){
            $forum = $object->getForum();
            $this->accessManager->addAccessCheck($attribute, $forum);
            if ($this->accessManager->hasAccess()) {
                return VoterInterface::ACCESS_GRANTED;
            }
        }
    }
}
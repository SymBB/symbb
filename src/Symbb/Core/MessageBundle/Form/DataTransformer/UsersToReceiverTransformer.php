<?php
namespace Symbb\Core\MessageBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symbb\Core\MessageBundle\Entity\Message;
use Symbb\Core\MessageBundle\Entity\Message\Receiver;
use Symbb\Core\UserBundle\Entity\User;
use Symbb\Core\UserBundle\Entity\UserInterface;
use Symbb\Core\UserBundle\Manager\UserManager;
use Symfony\Component\Form\DataTransformerInterface;

class UsersToReceiverTransformer implements DataTransformerInterface
{

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var UserManager
     */
    protected $userManager;

    public function __construct(Message $message, UserManager $userManager){
        $this->message = $message;
        $this->userManager = $userManager;
    }

    public function reverseTransform($users)
    {
        if (null === $users) {
            return null;
        }

        $data = new ArrayCollection();
        foreach($users as $user){
            $receiver = new Receiver();
            $receiver->setUserId($user);
            $receiver->setMessage($this->message);
            $data->add($receiver);
        }

        return $data;
    }

    public function transform($receivers)
    {
        if (!$receivers) {
            return null;
        }

        $data = new ArrayCollection();
        foreach($receivers as $receiver){
            $user = $receiver->getUserId();
            $user = $this->userManager->find($user);
            $data->add($user);
        }

        return $data;
    }
}
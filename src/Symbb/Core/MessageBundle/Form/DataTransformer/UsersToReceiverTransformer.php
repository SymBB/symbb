<?php
namespace Symbb\Core\MessageBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symbb\Core\MessageBundle\Entity\Message;
use Symbb\Core\MessageBundle\Entity\Message\Receiver;
use Symbb\Core\UserBundle\Entity\UserInterface;
use Symfony\Component\Form\DataTransformerInterface;

class UsersToReceiverTransformer implements DataTransformerInterface
{

    /**
     * @var Message
     */
    protected $message;

    public function __construct(Message $message){
        $this->message = $message;
    }

    public function reverseTransform($users)
    {
        if (null === $users) {
            return null;
        }

        $data = new ArrayCollection();
        foreach($users as $user){
            $receiver = new Receiver();
            $receiver->setUser($user);
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
            $user = $receiver->getUser();
            $data->add($user);
        }

        return $data;
    }
}
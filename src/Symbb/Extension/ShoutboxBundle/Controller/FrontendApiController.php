<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Extension\ShoutboxBundle\Controller;

use Symbb\Core\SystemBundle\Controller\AbstractApiController;
use Symbb\Extension\ShoutboxBundle\Entity\Message;
use Symfony\Component\HttpFoundation\Request;

class FrontendApiController extends AbstractApiController
{

    public function listAction(Request $request)
    {

        $qb = $this->get('doctrine')->getRepository('SymbbExtensionShoutboxBundle:Message', 'symbb')->createQueryBuilder('m');
        $qb->select("m");
        $qb->orderBy("m.date", "DESC");
        $query = $qb->getQuery();
        $pagination = $this->get('knp_paginator')->paginate(
            $query, $request->query->get('page', 1), $request->query->get('limit', 20)
        );

        $data = array();
        $data['shoutboxEntries'] = array();

        foreach($pagination as $result){
            $author = $result->getAuthor();
            $data['shoutboxEntries'][] = array(
                'id' => $result->getId(),
                'message' => $result->getMessage(),
                'date' => $this->getISO8601ForUser($result->getDate()),
                'author' => array(
                    'id' => $author->getId(),
                    'username' => $author->getUsername(),
                    'avatar' => $this->get('symbb.core.user.manager')->getAvatar($author)
                )
            );
        }

        $data['shoutboxEntries'] = array_reverse($data['shoutboxEntries']);

        return $this->getJsonResponse($data);
    }

    public function saveAction(Request $request)
    {

        $messageBody = $request->get('message');

        if(!empty($messageBody)){
            $message = new Message();
            $message->setAuthor($this->getUser());
            $message->setMessage($messageBody);
            $em = $this->get('doctrine.orm.symbb_entity_manager');
            $em->persist($message);
            $em->flush();
            $this->addSuccessMessage('saved successfully');
        } else {
            $this->addErrorMessage('shoutbox message is empty');
        }

        return $this->getJsonResponse(array());
    }
}
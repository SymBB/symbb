<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\ForumBundle\Event;

use Symbb\Core\ForumBundle\Entity\Topic;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

class TopicFormSaveEvent extends Event
{

    /**
     * @var Topic
     */
    protected $topic;

    /**
     * @var Request
     */
    protected $request;

    protected $form;

    public function __construct(Topic $topic, Request $request, $form)
    {
        $this->topic = $topic;
        $this->request = $request;
        $this->form = $form;

    }

    /**
     * @return Topic
     */
    public function getTopic()
    {
        return $this->topic;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return mixed
     */
    public function getForm()
    {
        return $this->form;
    }


}

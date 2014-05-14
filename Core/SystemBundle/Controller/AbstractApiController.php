<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Controller;

abstract class AbstractApiController extends AbstractController
{

    protected $messages = array();

    protected $callbacks = array();

    protected $breadcrumbItems = array();

    protected $paginationData = array();

    protected $success = true;

    protected function addPaginationData(\Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination $pagination)
    {
        $this->paginationData = $pagination->getPaginationData();
    }

    protected function addCallback($callbackName)
    {
        $this->callbacks[] = $callbackName;
    }

    protected function addBreadcrumbItems($breadbrumb)
    {
        $this->breadcrumbItems = $breadbrumb;
    }

    protected function getJsonResponse($params)
    {
        $user = $this->getUser();
        $authenticated = false;
        if ($user->getSymbbType() === 'user') {
            $authenticated = true;
        }
        if (!isset($params['user'])) {
            $params['user'] = array();
        }
        $params['user']['id'] = $user->getId();
        $params['user']['username'] = $user->getUsername();
        $params['user']['type'] = $user->getSymbbType();
        $params['user']['authenticated'] = $authenticated;
        $params['messages'] = $this->messages;
        $params['callbacks'] = $this->callbacks;
        $params['breadcrumbItems'] = $this->breadcrumbItems;
        $params['success'] = $this->success;
        if (!empty($this->paginationData)) {
            $params['paginationData'] = $this->paginationData;
        }
        $response = new \Symfony\Component\HttpFoundation\Response(json_encode($params));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }

    protected function getISO8601ForUser(\DateTime $datetime = null)
    {
        if ($datetime) {
            $datetime->setTimezone($this->get('symbb.core.user.manager')->getTimezone());
            return $datetime->format(\DateTime::ISO8601);
        }

        return null;
    }

    protected function addErrorMessage($message)
    {
        $this->messages[] = array(
            'type' => 'error',
            'bootstrapType' => 'danger',
            'message' => $this->trans($message)
        );
        $this->success = false;
    }

    protected function addSuccessMessage($message)
    {
        $this->messages[] = array(
            'type' => 'success',
            'bootstrapType' => 'success',
            'message' => $this->trans($message)
        );
    }

    protected function addInfoMessage($message)
    {
        $this->messages[] = array(
            'type' => 'info',
            'bootstrapType' => 'info',
            'message' => $this->trans($message)
        );
    }

    protected function addWarningMessage($message)
    {
        $this->messages[] = array(
            'type' => 'warning',
            'bootstrapType' => 'warning',
            'message' => $this->trans($message)
        );
    }

    public function hasError()
    {
        foreach ($this->messages as $message) {
            if ($message['type'] === 'error') {
                return true;
            }
        }
        return false;
    }

    protected function trans($msg, $param = array())
    {
        return $this->get("translator")->trans($msg, $param, 'symbb_frontend');
    }
}
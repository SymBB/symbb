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
    protected $success = true;


    protected function addCallback($callbackName){
        $this->callbacks[] = $callbackName;
    }
    
    protected function addBreadcrumbItems($breadbrumb){
        $this->breadcrumbItems = $breadbrumb;
    }

    protected function getJsonResponse($params){
        $params['messages'] = $this->messages;
        $params['callbacks'] = $this->callbacks;
        $params['breadcrumbItems'] = $this->breadcrumbItems;
        $params['success'] = $this->success;
        $response = new \Symfony\Component\HttpFoundation\Response(json_encode($params));
        $response->headers->set('Content-Type', 'application/json');
        return $response;
    }


    protected function getCorrectTimestamp(\DateTime $datetime){
        $datetime->setTimezone($this->get('symbb.core.user.manager')->getTimezone());
        return $datetime->format(\DateTime::ISO8601);
    }
    
    protected function addErrorMessage($message){
        $this->messages[] = array(
            'type' => 'error',
            'bootstrapType' => 'danger',
            'message' => $this->get('translator')->trans($message)
        );
        $this->success = false;
    }
    
    protected function addSuccessMessage($message){
        $this->messages[] = array(
            'type' => 'success',
            'bootstrapType' => 'success',
            'message' => $this->get('translator')->trans($message)
        );
    }
    
    protected function addInfoMessage($message){
        $this->messages[] = array(
            'type' => 'info',
            'bootstrapType' => 'info',
            'message' => $this->get('translator')->trans($message)
        );
    }
    
    protected function addWarningMessage($message){
        $this->messages[] = array(
            'type' => 'warning',
            'bootstrapType' => 'warning',
            'message' => $this->get('translator')->trans($message)
        );
    }
}
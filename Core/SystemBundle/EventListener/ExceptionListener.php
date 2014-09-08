<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\EventListener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;

class ExceptionListener extends \Symfony\Component\HttpKernel\EventListener\ExceptionListener
{

    /**
     *
     * @var \SymBB\Core\SiteBundle\Manager\SiteManager
     */
    protected $siteManager;

    protected $templating;

    protected $env = "";

    public function __construct($siteManager, $templating, $env)
    {
        $this->siteManager = $siteManager;
        $this->templating = $templating;
        $this->env = $env;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        try {

            $code = $exception->getCode();
            if($exception instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException){
                $code = 404;
            }
            
            $file = $code;
            if ($this->env != "prod") {
                //$file = $file . '.' . $this->env;
            }
            $file = $file . '.html.twig';
            $template = $this->siteManager->getTemplate("portal");
            $template = "SymBBTemplateDefaultBundle:Exception:" . $file;

            $response = new Response($this->templating->render(
                $template, array(
                    'status_code' => $code,
                    'status_text' => $exception->getMessage(),
                    'exception' => $exception
                )
            ));
            // setup the Response object based on the caught exception
            $event->setResponse($response);
            
        } catch (\Exception $exc) {
            $event->setException($exception);
        }

        // you can alternatively set a new Exception
        // $exception = new \Exception('Some special exception');
        // $event->setException($exception);
    }
}
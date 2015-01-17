<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SystemBundle\Controller;

class AcpMaintenanceController extends \Symbb\Core\SystemBundle\Controller\AbstractController
{

    public function indexAction()
    {

        $manager = $this->get('symbb.core.update.manager');
        $data = $manager->collect();
        var_dump($manager->getSymbbData());

        return $this->render(
            $this->getTemplateBundleName('acp') . ':Acp:System\maintenance.html.twig', array()
        );
    }
}
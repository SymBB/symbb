<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SystemBundle\Controller;

class AcpConfigController extends \SymBB\Core\SystemBundle\Controller\AbstractController
{

    public function indexAction()
    {

        $configData = $this->get('symbb.core.config.manager')->getConfigListGroupBySection();

        $defaultData = array('name' => 'config');
        $form = $this->get('form.factory')->createNamedBuilder('config', 'form', $defaultData, array('translation_domain' => 'symbb_variables'))
            ->setAction($this->generateUrl('_symbbcoresystembundle_config'));

        foreach ($configData as $section => $configs) {
            foreach ($configs as $key => $value) {
                $type = $this->get('symbb.core.config.manager')->getType($key, $section);
        
                $options = array(
                    'data' => $this->get('symbb.core.config.manager')->get($key, $section)
                );
                if ($type == 'choice') {
                    $choices = $this->get('symbb.core.config.manager')->getChoices($key, $section);
                    $options['choices'] = $choices->toArray();
                }
                if ($type == 'bbcode') {
                    $type = "textarea";
                }
                if ($type == 'number' || $type == 'int') {
                    $type = 'integer';
                }
                $key = $section.':'.$key;
                $name = \str_replace('.', '_', $key);
                $label = \str_replace(':', '.', $key);
                $options['attr']['section'] = $section;
                $options['label'] = 'config.' . $label;
                $form->add($name, $type, $options);
            }
        }

        $form = $form->getForm();

        $form->handleRequest($this->get('request'));

        if ($form->isValid()) {
            foreach ($configData as $section => $configs) {
                foreach ($configs as $key => $value) {
                    $name = \str_replace('.', '_', $key);
                    $name = $section.':'.$name;
                    $newValue = $form->get($name)->getData();
                    $this->get('symbb.core.config.manager')->set($key, $section, $newValue);
                }
            }
            $this->get('symbb.core.config.manager')->save();
            $this->get('session')->getFlashBag()->add(
                'success', $this->get('translator')->trans('successfully saved', array(), 'symbb_backend')
            );
        }

        return $this->render(
            $this->getTemplateBundleName('acp') . ':Acp:System\config.html.twig', array('form' => $form->createView())
        );
    }
}
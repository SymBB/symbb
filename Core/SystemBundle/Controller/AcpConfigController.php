<?
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
    
    public function indexAction(){
        
        $configData = $this->get('symbb.core.config.manager')->getConfigListGroupBySection();
        
        $defaultData = array('name' => 'config');
        $form = $this->get('form.factory')->createNamedBuilder('config', 'form', $defaultData, array('translation_domain' => 'symbb_backend'))
            ->setAction($this->generateUrl('_symbbcoresystembundle_config'));
        
        foreach($configData as $section => $configs){
            foreach($configs as $key => $value){
                $type = $this->get('symbb.core.config.manager')->getType($key);
                $options = array(
                    'data' => $this->get('symbb.core.config.manager')->get($key)
                );
                if($type == 'choice'){
                    $choices = $this->get('symbb.core.config.manager')->getChoices($key);
                    $options['choices'] = $choices->toArray();
                }
                if($type == 'bbcode'){
                    $type = new \SymBB\Extension\BBCodeBundle\Form\Type\BBEditorType();
                }
                $name = \str_replace('.', '_', $key);
                $options['attr']['section'] = $section;
                $options['label'] = 'config.'.$section.'.'.$key;
                $form->add($name, $type, $options);
            }
        }
        
        $form = $form->getForm();
        
        $form->handleRequest($this->get('request'));
        
        if ($form->isValid()) {
            foreach($configData as $section => $configs){
                foreach($configs as $key => $value){
                    $name = \str_replace('.', '_', $key);
                    $newValue = $form->get($name)->getData();
                    $this->get('symbb.core.config.manager')->set($key, $newValue);
                }
            }
            $this->get('symbb.core.config.manager')->save();
            $this->get('session')->getFlashBag()->add(
                'success',
                $this->get('translator')->trans('successfully saved', array(), 'symbb_backend')
            );
        }
        
        return $this->render(
            $this->getTemplateBundleName('acp').':Acp:System\config.html.twig',
            array('form' => $form->createView())
        );
        
    }
}
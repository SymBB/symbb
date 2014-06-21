<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Core\SiteBundle\Form\Type;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class NavigationItem extends AbstractType
{

    /**
     * @var Router
     */
    protected $router;

    public function __construct($router){
        $this->router = $router;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\SymBB\Core\SiteBundle\Entity\Navigation\Item',
            'translation_domain' => 'symbb_backend'
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('title', "text", array('required' => true, 'constraints' => array(new NotBlank())))
            ->add('type', "choice",
                array(
                    'required' => true,
                    'choices' => array(
                        null => '',
                        'symfony' => 'System Link',
                        'url' => 'URL'
                    ),
                    'attr' => array(
                        'onChange' => 'submit();'
                    ),
                    'constraints' => array(
                        new NotBlank()
                    )
                )
            );

            $myEventFunc = function (FormEvent $event) {
                $item = $event->getData();
                $form = $event->getForm();

                if(is_object($item)){
                    $item = array(
                        'type' => $item->getType(),
                        'symfonyRoute' => $item->getSymfonyRoute()
                    );
                }

                if($item['type'] == "symfony"){
                    $collection = $this->router->getRouteCollection();
                    $list = array(
                        null => ''
                    );
                    foreach($collection as $key => $route){
                        $params = $route->getRequirements();
                        if(strpos($key, '_') !== 0 && strpos($key, '_api_') === false && empty($params)){
                            $list[$key] = $route->getPath();
                        }
                    }

                    $form->remove("fixUrl");
                    $form->add('symfonyRoute', 'choice', array('choices' => $list, 'attr' => array(
                            'onChange' => 'submit();'
                        ),'required' => true, 'constraints' => array(
                            new NotBlank()
                        )));

                    $currentRoute = '';
                    if(isset($item['symfonyRoute'])){
                        $currentRoute = $item['symfonyRoute'];
                    }

                    if(!empty($currentRoute)){
                        foreach($collection as $key => $route){
                            if($key === $currentRoute){
                                $path = $route->getPath();
                                $matches = array();
                                preg_match_all("#\{(.*)\}#iUs", $path, $matches);
                                if(isset($matches[1]) && !empty($matches[1])){
                                    foreach($matches[1] as $param){
                                        if($param != "_locale"){
                                            $form->add('symfonyRouteParam_'.$param, "text", array('mapped' => false, 'label' => '{'.$param.'}', 'required' => true, 'constraints' => array(
                                                    new NotBlank()
                                                )));
                                        }
                                    }
                                }
                            }
                        }
                    }

                } else{
                    $form->remove("symfonyRoute");
                    $form->add('fixUrl', "url", array('required' => true, 'constraints' => array(
                            new NotBlank()
                        )));
                }
            };

            $builder->addEventListener(FormEvents::PRE_SUBMIT, $myEventFunc);
            $builder->addEventListener(FormEvents::PRE_SET_DATA, $myEventFunc);


    }


    public function getName()
    {
        return 'navigation_item';

    }
}
<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace Symbb\Core\SiteBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class Site extends AbstractType
{

    protected $dispatcher;
        
    public function __construct($dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => '\Symbb\Core\SiteBundle\Entity\Site',
            'translation_domain' => 'symbb_backend'
        ));

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $templateEvent = new \Symbb\Core\SiteBundle\Event\TemplateChoicesEvent();
        $this->dispatcher->dispatch('symbb.core.site.acp.template_choices', $templateEvent);
        $templateChoices = $templateEvent->getChoices();
        $templateChoices = $templateChoices->toArray();
        $builder
            ->add('name', "text", array('required' => true))
            ->add('metaDataDescription', "textarea", array('required' => true))
            ->add('metaDataKeywords', "textarea", array('required' => true))
            ->add('domains', "textarea", array('required' => true))
            ->add('googleAnalyticsCode', "text", array('required' => false))
            ->add('templateAcp', "choice", array('choices' => $templateChoices))
            ->add('templateForum', "choice", array('choices' => $templateChoices))
            ->add('templatePortal', "choice", array('choices' => $templateChoices))
            ->add('templateEmail', "choice", array('choices' => $templateChoices))
            ->add('announcement', "textarea", array('required' => false));

    }


    public function getName()
    {
        return 'site';

    }
}
<?php
/**
 *
 * @package symBB
 * @copyright (c) 2013-2014 Christian Wielath
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace SymBB\Extension\SurveyBundle\EventListener;
use \Symfony\Component\Validator\Constraints\Length;
use \Symfony\Component\Validator\Constraints\Range;

class FormListener
{

    public function addPostFormPart($event)
    {
        $builder = $event->getBuilder();
        $builder->add('surveyQuestion', 'text', array(
            'mapped' => false,
            'required' => false,
            'label' => 'The question',
            'attr' => array(),
            'constraints' => array(
                new Length(array(
                    'min'        => 5
                ))
            )
        ));
        $builder->add('surveyAnswers', 'textarea', array(
            'mapped' => false,
            'required' => false,
            'label' => 'The answers',
            'constraints' => array(
                new Length(array(
                    'min'        => 1
                ))
            ),
            'attr' => array(
                'placeholder' => 'Give each answer on a separate line'
            )
        ));
        $builder->add('surveyChoices', 'integer', array(
            'mapped' => false, 
            'required' => false, 
            'label' => 'Options per user', 
            'attr' => array(),
            'constraints' => array(
                new Range(array(
                    'min'        => 1,
                    'max'        => 50
                ))
            )
        ));
        $builder->add('surveyChoicesChangeable', 'checkbox', array('mapped' => false, 'required' => false, 'label' => 'Allow changing the voting', 'attr' => array()));
        $builder->add('surveyEnd', 'date', array(
            'mapped' => false,
            'required' => false,
            'label' => 'End date',
            'input' => 'datetime',
            'widget' => 'single_text'
        ));

    }
}
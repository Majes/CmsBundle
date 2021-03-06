<?php

namespace Majes\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\HttpFoundation\Session\Session;

class RedirectType extends AbstractType
{

	public function __construct(){}

	public function configureOptions(OptionsResolver $resolver)
	{
    	$resolver->setDefaults(array(
    	    'data_class' => 'Majes\CmsBundle\Entity\Redirect',
    	    'csrf_protection' => false,
    	));
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('url', 'text', array(
        	'required' => true,
        	'constraints' => array(
       		    new NotBlank()
       		)));

        $builder->add('redirectUrl', 'text', array(
            'required' => true,
            'constraints' => array(
                new NotBlank()
            )));

        $builder->add('host', 'entity', array(
            'required' => true,
            'class' => 'MajesCoreBundle:Host',
            'choice_label' => 'url',
            'constraints' => array(
                new NotBlank()
            )));

        $builder->add('permanent', 'checkbox', array(
            'required' => false,
            ));


    }

    public function getName()
    {
        return 'redirect';
    }
}

<?php 
// src/Majes/CoreBundle/Form/User/Myaccount.php
namespace Majes\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\HttpFoundation\Session\Session;

class PageLangType extends AbstractType
{
	public function __construct(){}

	public function setDefaultOptions(OptionsResolverInterface $resolver)
	{
    	$resolver->setDefaults(array(
    	    'data_class' => 'Majes\CmsBundle\Entity\PageLang',
    	    'csrf_protection' => false,
    	));
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {


        $builder->add('title', 'text', array(
        	'required' => true,
        	'constraints' => array(
       		    new NotBlank()
       		)));

        $builder->add('url', 'text', array(
        	'required' => true,
        	'label' => 'Url of your page',
        	'constraints' => array(
       		    new NotBlank()
       		)));
        

        $builder->add('meta_title', 'text', array(
            'required' => false));

        $builder->add('meta_description', 'textarea', array(
        	'required' => false));

        $builder->add('meta_keywords', 'text', array(
        	'required' => false));
       
    }

    public function getName()
    {
        return 'pagelangtype';
    }
}
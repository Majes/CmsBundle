<?php
// src/Majes/CoreBundle/Form/User/Myaccount.php
namespace Majes\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\HttpFoundation\Session\Session;

class TemplateType extends AbstractType
{

	public function __construct(){}

	public function configureOptions(OptionsResolver $resolver)
	{
    	$resolver->setDefaults(array(
    	    'data_class' => 'Majes\CmsBundle\Entity\Template',
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

        $builder->add('ref', 'text', array(
            'required' => true,
            'constraints' => array(
                new NotBlank()
            )));


    }

    public function getName()
    {
        return 'template';
    }
}

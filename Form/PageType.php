<?php 

namespace Majes\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\HttpFoundation\Session\Session;

class PageType extends AbstractType
{

    protected $em;
    protected $lang;

    public function __construct($em = null, $lang = 'en'){
        $this->em = $em;
        $this->lang = $lang;
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Majes\CmsBundle\Entity\Page',
            'csrf_protection' => false,
        ));
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->add('host', 'entity', array(
            'required' => true,
            'class' => 'MajesCoreBundle:Host',
            'property' => 'url'));

        $builder->add('template', 'entity', array(
            'required' => true,
            'class' => 'MajesCmsBundle:Template',
            'property' => 'title'));


        $routes = $this->em->getRepository('MajesCmsBundle:Route')
                    ->findBy(array('locale' => $this->lang));

        $values = array();
        foreach ($routes as $route) {
            $values[$route->getUrl()] = $route->getTitle();
        }

        $builder->add('link_url', 'choice', array(
            'required' => false, 
            'select2' => array(
                'url' => 'ajaxurl',
                'label' => 'title',
                'value' => 'url'
            ),
            'choices' => $values));
        
        $builder->add('target_url', 'choice', array(
            'required' => false,
            'choices' => array(
                '_self' => 'Current window',
                '_blank' => 'New window'
            )));

        $builder->add('is_folder', 'checkbox', array(
            'required' => false));

        $builder->add('is_inmenu', 'checkbox', array(
            'required' => false));

        $builder->add('enable_comments', 'checkbox', array(
            'required' => false));

        $builder->add('is_active', 'checkbox', array(
            'required' => false));

        $builder->add('lang', new PageLangType());

    }

    public function getName()
    {
        return 'pagetype';
    }
}
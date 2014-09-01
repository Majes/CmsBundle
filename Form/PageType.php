<?php 

namespace Majes\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityRepository;

class PageType extends AbstractType
{

    protected $em;
    protected $lang;
    protected $host_id;

    public function __construct($em = null, $lang = 'en', $host_id = 1){
        $this->em = $em;
        $this->lang = $lang;
        $this->host_id = $host_id;
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

        $host = $this->em->getRepository('MajesCoreBundle:Host')
            ->findOneById($this->host_id);

        $builder->add('host', 'entity', array(
            'required' => true,
            'class' => 'MajesCoreBundle:Host',
            'property' => 'url',
            'data' => $host));

        $builder->add('template', 'entity', array(
            'required' => true,
            'class' => 'MajesCmsBundle:Template',
            'query_builder' => function(EntityRepository $er) {
                return $er->createQueryBuilder('u')
                            ->where('u.deleted = 0');
            },
            'property' => 'title'));


        $routes = $this->em->getRepository('MajesCmsBundle:Route')
                    ->findBy(array('locale' => $this->lang));

        $values = array();
        foreach ($routes as $route) {
            $values[$route->getUrl()] = $route->getTitle();
        }

        $builder->add('link_url', 'choice', array(
            'label' => 'Internal link',
            'required' => false, 
            'select2' => array(
                'url' => 'ajaxurl',
                'label' => 'title',
                'value' => 'url'
            ),
            'choices' => $values));

        $builder->add('link_url2', 'text', array(
            'label' => 'Or external',
            'mapped' => false,
            'required' => false));
        
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


        $builder->addEventListener(
            FormEvents::PRE_SET_DATA, function(FormEvent $event)
            {
                $form = $event->getForm();
                $data = $event->getData();

                $link_url = empty($data) ? '' : $data->getLinkUrl();

                $form->add('link_url2', 'text', array(
                    'label' => 'Or external',
                    'mapped' => false,
                    'required' => false,
                    'data' => $link_url));
            }
        );

    }

    public function getName()
    {
        return 'pagetype';
    }
}
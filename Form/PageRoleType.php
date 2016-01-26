<?php
// src/Acme/TaskBundle/Form/Type/TagType.php
namespace Majes\CmsBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Doctrine\ORM\EntityManager;


class PageRoleType extends AbstractType
{

	protected $em;
    protected $page;

	public function __construct(EntityManager $em, $page){
        $this->em = $em;
        $this->page = $page;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

    	$roles = $this->em->getRepository('MajesCoreBundle:User\Role')
            ->findBy(array('deleted' => false));

        $roles_array = array();$roles_has = array();
        $bundle = null;
        foreach($roles as $role){
            if(!$role->getIsSystem() && $role->getBundle() == 'cms'){
                $roles_array[$role->getBundle()][$role->getId()] = $role->getName();

                if(empty($this->page)) $roles_has[$role->getBundle()][$role->getId()] = true;
                else $roles_has[$role->getBundle()][$role->getId()] = $this->page->hasRole($role->getId()) ? $role->getId() : false;
            }
        }

        foreach ($roles_array as $bundle => $role) {

                $builder->add(empty($bundle) ? 'general' : $bundle , 'choice', array(
                    'choices' => $role,
                    'multiple' => true,
                    'expanded' => true,
                    'data' => $roles_has[$bundle]
                    ));
        }
    }

    public function configureOptions(OptionsResolverInterface $resolver)
    {

    }

    public function getName()
    {
        return 'pageroletype';
    }
}

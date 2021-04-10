<?php

namespace App\Form;

use App\Entity\Empresa;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;


class EmpresaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, array('attr' => array('label' => 'Nombre', 'class' => 'form-control txt')))
            ->add('telefono', TelType::class, array('attr' => array('label' => 'Nombre', 'class' => 'form-control txt')))
            ->add('email', EmailType::class, array('attr' => array('label' => 'Email', 'class' => 'form-control txt')))
            // ->add('sector', EntityType::class, array('attr' => array('label' => 'Sector', 'class' => 'form-control')))
            ->add('sector', EntityType::class, array(
                'class'         => 'App\Entity\Sector',
                'query_builder' => function(EntityRepository $er) {
                    return $er->createQueryBuilder('c')->orderBy('c.nombre', 'ASC');
                },
                'label' => 'Sector',
                'required' => true
            ),
        )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Empresa::class,
        ]);
    }
}

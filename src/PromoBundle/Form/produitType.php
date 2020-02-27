<?php

namespace PromoBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class produitType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nom', TextType::class,[ 'required'   => false ,'attr'=>['placeholder'=>'search a product']]);
    }/**
     * {@inheritdoc}
     */


    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'produit_promotion';
    }


}

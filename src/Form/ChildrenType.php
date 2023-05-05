<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChildrenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class,[
                'label' => 'Prenom',
                'constraints' => new NotBlank(),
            ])
            ->add('birthdate', BirthdayType::class, [
                'label' => 'Date de naissance',
                'constraints' => new NotBlank(),
            ])
            ->add('weight', NumberType::class, [
                'label' => 'Poids'
            ])
            ->add('size', NumberType::class, [
                'label' => 'Taille'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

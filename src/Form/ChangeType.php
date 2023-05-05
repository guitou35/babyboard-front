<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ChangeType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $children = $options['children'];
        $builder
            ->add('children', ChoiceType::class, [
                'choices' => $children,
                'label' => 'Enfant',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un enfant',
                    ]),
                ],
            ])
            ->add('heure', DateTimeType::class, [
                'label' => 'Jour et heure du change',
                'required' => true,
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'couche' => 'COUCHE',
                    'Petit pot' => 'PETITPOT',
                    'Toilette' => 'TOILETTE'
                ],
                'label' => 'Type de change',
                'multiple' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un moment du repas',
                    ]),
                ],
            ])
            ->add('products', ChoiceType::class, [
                'choices' => [
                    'Liniment' => 'LINIMENT',
                    'Talc' => 'TALC',
                    'Lingettes' => 'LINGETTES'
                ],
                'label' => 'Contenu',
                'multiple' => true
            ])
            ->add('contenu', ChoiceType::class, [
                'choices' => [
                    'Selles' => 'SELLES',
                    'Urine' => 'URINE'
                ],
                'label' => 'Contenu',
                'multiple' => true
            ])
            ->add('problems', ChoiceType::class, [
                'choices' => [
                    'Irritation' => 'IRRITATION',
                    'Boutons' => 'BUTTON'
                ],
                'label' => 'Problèmes',
                'multiple' => true
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'children' => null,
        ]);
    }
}

<?php

namespace App\Form;

use DateTime;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;

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
                'data' => new DateTime()
            ])
            ->add('type', ChoiceType::class, [
                'choices' => [
                    'couche' => 'COUCHE',
                    'Petit pot' => 'PETITPOT',
                    'Toilette' => 'TOILETTE'
                ],
                'label' => 'Type de change',
                'multiple' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un moment du repas',
                    ]),
                ],
            ])
            ->add('contenu', ChoiceType::class, [
                'choices' => [
                    'Selles' => 'SELLES',
                    'Urine' => 'URINE'
                ],
                'label' => 'Contenu',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('products', ChoiceType::class, [
                'choices' => [
                    'Liniment' => 'LINIMENT',
                    'Talc' => 'TALC',
                    'Lingettes' => 'LINGETTES'
                ],
                'label' => 'Produits',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('problems', ChoiceType::class, [
                'choices' => [
                    'Irritation' => 'IRRITATION',
                    'Boutons' => 'BUTTON'
                ],
                'label' => 'Problèmes',
                'multiple' => true,
                'expanded' => true,
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

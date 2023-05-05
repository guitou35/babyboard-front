<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RepasType extends AbstractType
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
            ->add('repasTime', ChoiceType::class, [
                'choices' => [
                    'Petit déjeuner' => 'PETITDEJ',
                    'Déjeuner' => 'DEJ',
                    'Goûter' => 'GOUTER',
                    'Dîner' => 'DINER'
                ],
                'label' => 'Moment du repas',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez choisir un moment du repas',
                    ]),
                ],
            ])
            ->add('alimentName', TextType::class, [
                'label' => 'Nom de l\'aliment',
                'help' => 'Ex: Pomme, banane, biberon ...',
                'required' => true
            ])
            ->add('quantity', TextType::class, [
                'label' => 'Quantité',
                'required' => false
            ])
            ->add('commentaire', TextareaType::class, [
                'label' => 'Commentaire',
                'required' => false
            ])
            ->add('repasAt', DateType::class, [
                'label' => 'Jour du repas',
                'required' => true,
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

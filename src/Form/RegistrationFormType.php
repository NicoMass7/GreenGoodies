<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('lastName')
      ->add('firstName')
      ->add('mail')
      ->add('agreeTerms', CheckboxType::class, [
        'mapped' => false,
        'label' => false, // Désactive le label par défaut de Symfony
        'constraints' => [
          new IsTrue([
            'message' => 'Vous devez accepter les conditions générales.',
          ]),
        ],
        'attr' => ['class' => 'form-check-input'], // Ajoute le style Bootstrap
      ])
      ->add('password', RepeatedType::class, [
        'type' => PasswordType::class,
        'required' => true,
        'first_options'  => ['label' => 'Mot de passe'],
        'second_options' => ['label' => 'Confirmation'],
        'constraints' => [new NotBlank()]
      ])
    ;
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
    ]);
  }
}

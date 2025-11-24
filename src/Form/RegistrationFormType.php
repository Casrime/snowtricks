<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use App\Validator\UniqueEntity;
use Override;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    #[Override]
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Please enter a username'),
                    new Length(min: 3, max: 4096, minMessage: 'Your username should be at least {{ limit }} characters'),
                    new UniqueEntity(),
                ],
                'label' => 'app.username',
            ])
            ->add('email', TextType::class, [
                'constraints' => [
                    new NotBlank(message: 'Please enter an email'),
                    new Email(message: 'Please enter a valid email address'),
                    new Length(min: 3, max: 4096, minMessage: 'Your email should be at least {{ limit }} characters'),
                ],
                'label' => 'app.email',
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'app.password',
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank(message: 'Please enter a password'),
                    new Length(min: 6, max: 4096, minMessage: 'Your password should be at least {{ limit }} characters'),
                ],
            ])
        ;
    }

    #[Override]
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

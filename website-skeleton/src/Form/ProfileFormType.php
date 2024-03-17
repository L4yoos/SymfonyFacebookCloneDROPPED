<?php

namespace App\Form;

use App\Entity\User;
use App\Validator\Constraints\MinAge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class ProfileFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('firstname', null, [
            'label' => 'First Name',
            'attr' => [
                'placeholder' => 'Enter your First Name',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a First Name',
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'Your First Name should be at least {{ limit }} characters',
                    'max' => 16,
                ]),
            ],
        ])
        ->add('lastname', null, [
            'label' => 'Last Name',
            'attr' => [
                'placeholder' => 'Enter your Last Name',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter a First Name',
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'Your Last Name should be at least {{ limit }} characters',
                    'max' => 16,
                ]),
            ],
        ])
        ->add('email', null, [
            'label' => 'Email',
            'attr' => [
                'placeholder' => 'Enter your email address',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter your email address',
                ]),
                new Email([
                    'message' => 'The email "{{ value }}" is not a valid email.',
                ]),
            ],
        ])
        ->add('dateofBirth', DateType::class, [
            'label' => 'Date of Birth',
            'empty_data' => '',
            'constraints' => [
                new NotBlank([
                    'message' => 'Please enter your date of birth',
                ]),
                new MinAge([
                    'message' => 'You should be at least {{ limit }} years old.',
                ]),
            ],
        ])
        ->add('gender', ChoiceType::class, [
            'label' => 'Gender',
            'choices' => [
                'Female' => 'Female',
                'Male' => 'Male',
            ],
        ])
        ->add('job', ChoiceType::class, [
            'label' => 'Job',
            'required' => false,
            'choices' => [
                'Unemployed' => 'Unemployed',
                'Developer' => 'Developer',
            ],
        ])
        ->add('school', ChoiceType::class, [
            'label' => 'School',
            'required' => false,
            'choices' => [
                'Elementary School' => 'Elementary',
                'High school' => 'High',
            ],
        ])
        ->add('interests', ChoiceType::class, [
            'label' => 'Interests',
            'required' => false,
            'choices' => [
                'Hobby 1' => 'Hobby 1',
                'Hobby 2' => 'Hobby 2',
            ],
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

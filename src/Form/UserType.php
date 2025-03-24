<?php

namespace App\Form;

use App\Entity\User;
use App\Enum\Gender;
use App\Enum\UserRole;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Positive;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('first_name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'First Name is required',
                    ])
                ]
            ])
            ->add('last_name', TextType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Last Name is required'
                    ]),
                ]
            ])
            ->add('age', null, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Age is required'
                    ]),
                    new Positive([
                        'message' => 'Age must be a positive number'
                    ])
                ]
            ])
            ->add('hobby', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-check-input'
                ],
                'choices' => [
                    'Traveling' => '1',
                    'Foody' => '2',
                    'Singing' => '3',
                    'Dancing' => '4',
                ],
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('gender', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'choices' => [
                    'Select Gender' => null,
                    'Male' => Gender::MALE,
                    'Female' => Gender::FEMALE,
                    'Others' => Gender::OTHERS
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Gender is required'
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Email is required'
                    ]),
                    new Email([
                        'message' => 'Email is not valid'
                    ]),
                ]
            ])
            ->add('roles', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'choices' => [
                    'Select Role' => null,
                    'Admin' => UserRole::ADMIN,
                    'User' => UserRole::USER,
                    'Manager' => UserRole::MANAGER
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Role is required'
                    ]),
                ]
            ])
            ->add('status', ChoiceType::class, [
                'attr' => [
                    'class' => 'form-control'
                ],
                'label_attr' => [
                    'class' => 'form-label'
                ],
                'choices' => [
                    'Select Status' => null,
                    'Inactive' => false,
                    'Active' => true,
                ],
                'constraints' => [
                    new NotNull([
                        'message' => 'Status is required'
                    ]),
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'csrf_protection' => true,
            // the name of the hidden HTML field that stores the token
            'csrf_field_name' => '_token',
            // an arbitrary string used to generate the value of the token
            // using a different string for each form improves its security
            'csrf_token_id'   => 'task_item',
        ]);
    }
}

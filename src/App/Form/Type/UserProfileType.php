<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserProfileType extends AbstractType
{

  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('image_profile', FileType::class, [
              'label' => 'Profile image',
              'required' => false,
              'constraints' => [
                  new File([
                      'maxSize' => '1024k',
                      'mimeTypes' => [
                          'image/jpg',
                          'image/jpeg',
                          'image/png',
                          'image/webp'

                      ],
                      'mimeTypesMessage' => 'Please upload a valid image file (jpg, jpeg, png, webp)',
                  ])
              ],
          ])
          ->add('mail', EmailType::class)
          ->add('name', TextType::class)
          ->add('submit', SubmitType::class); 

    if (in_array('ROLE_ADMIN', $options['role']))
    {
      $builder->add('roles', ChoiceType::class, array(
        'label' => 'Roles',
        'choices' => ['Authenticate' => 'IS_AUTHENTICATED_FULLY', 'User' => 'ROLE_USER', 'Admin' => 'ROLE_ADMIN'],
        'choice_translation_domain' => 'user',
        'multiple'  => true,
        'expanded' => true,
        'required' => true,
      ));
    }    
  }

  public function configureOptions(OptionsResolver $resolver)
  {
      $resolver->setDefaults(array(
          'role' => ['ROLE_USER']
      ));
  }
  
}
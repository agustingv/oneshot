<?php

namespace App\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\Form\CallbackTransformer;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->setAction('single/post');
        $builder->add('uid', HiddenType::class)
                ->add('title', TextType::class, ['attr' => ['maxlength' => 256, 'placeholder' => 'Escribe un título o no']])
                ->add('body', TextareaType::class, ['sanitizer' => 'app.post_sanitizer', 'attr' => ['minlength' => 50, 'maxlength' => 256, 'rows' => 10]])
                ->add('tags', ChoiceType::class,['expanded' => false, 'multiple' => true, 'required' => false])
                ->add('submit', SubmitType::class);
                
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event){
            $form = $event->getForm();
            $data = $event->getData();
    
            if (isset($data['tags'])) 
            {
                $tags = $data['tags'];
                foreach ($tags as $tag)
                {
                    $choices[$tag] = $tag;
                }

                if($choices){
                    $form->add('tags', ChoiceType::class, ['multiple' => 'true', 'choices' => $tags, 'data' => array_values($choices)]);
                }
            }
        });
    
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event)
        {

            if (isset($event->getData()['tags']))
            {
                $tags = $event->getData()['tags'];
                $form = $event->getForm();
                $choices = [];
                foreach ($tags as $tag)
                {
                    $choices[$tag['name']] = $tag['id'];
                }
                $form->add('tags', ChoiceType::class, ['multiple' => 'true', 'choices' => $choices, 'data' => array_values($choices)]);
            }
        });
        
            
    }  

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'sanitize_html' => true,
            'sanitizer' => 'app.post_sanitizer',
        ]);
    }
    
}
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Form\Type\PostType;
use OneShot\Application\Post\Command\CreatedPostCommand;
use OneShot\Application\Tag\Query\LoadMultipleTagQuery;
use OneShot\Domain\Post\Post;
use OneShot\Domain\ValueObjects\EntityId;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

class PostController extends AbstractController
{
    private MessageBusInterface $messageBus;
    private TokenStorageInterface $security;
    private TagAwareCacheInterface $cache;

    public function __construct(MessageBusInterface $messageBus, TagAwareCacheInterface $cache)
    {
      $this->messageBus = $messageBus;
      $this->cache = $cache;
    }

   /**
    * @Route("/single/post")
    */
    public function postPost(Request $request) : Response
    {
        if (!$request->isMethod('POST')) {
            $this->addFlash('success', 'Method not allowed');
            return new RedirectResponse('/');
        }
        
        $user = $this->getUser();
        if (!isset($user))
        {
            $this->addFlash('success', 'You need sing in on app');
            return new RedirectResponse('/');
        }
        $form = $this->createForm(PostType::class);
        $form->handleRequest($request);

        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) 
        {
            $user = $this->getUser();
            $uid = $form->get('uid')->getData();
            if ($user->getUserIdentifier() !== $uid) {
              $this->addFlash('error', 'We have a problem!');
            } else {
                $post = new Post(); 
                $post->setId(EntityId::generate()->toString());
                $post->setUser($user);
                $post->setBody($form->get('body')->getData());
                $post->setTitle($form->get('title')->getData());
                $post->setExpiredAt();
                $post->setCreatedAt(time());

                $ids = $form->get('tags')->getData();
                if (!empty($ids))
                {
                  $query = new LoadMultipleTagQuery(ids: $ids);
                  $envelope = $this->messageBus->dispatch($query);
                  $handledStamp = $envelope->last(HandledStamp::class);
                  $tags = $handledStamp->getResult();
                  if (!empty($tags))
                  {
                    $post->setTags($tags);
                  }
                }
          
                try {
                  $command = new CreatedPostCommand(
                    post: $post
                  );
                  $this->messageBus->dispatch($command);
          
                  $this->addFlash('success', sprintf('Post send by "%s" created', $post->getUser()->getName()));
                } catch (\Exception $e) {
                  $errors[] = $e->getMessage();
                }
            }
        }

        $this->cache->invalidateTags(['date_lists']);

        return new RedirectResponse('/');
    }
}
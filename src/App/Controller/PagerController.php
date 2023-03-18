<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OneShot\Application\Post\Query\ViewPostByDateQuery;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use OneShot\Domain\Post\Post;
use Symfony\Component\HttpFoundation\JsonResponse;

class PagerController extends AbstractController
{
    private MessageBusInterface $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
      $this->messageBus = $messageBus;
    }
  
    /**
     * @Route("/posts/pager")
     */
    public function postPager(Request $request) : Response
    {
        $page = 0;
        if ($request->isXmlHttpRequest())
        {
          $page = $request->get('page');
        }

        try {
            $query = new ViewPostByDateQuery(
                page: $page,
                items_page: $this->getParameter('app.items_p_page'),
            );
      
            $envelope = $this->messageBus->dispatch($query);
            $handledStamp = $envelope->last(HandledStamp::class);
            $documents = $handledStamp->getResult();
            $posts = [];
            foreach ($documents as $document)
            {
                $post = new Post();
                $posts[] = $post->fromArray($document);
            }
      
            $last = end($posts);

        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }

        $contents = "";
        foreach ($posts as $post) 
        {
            $contents .= $this->renderView('oneshot/content/post.html.twig', ['post' => $post, 'last' => $last]);
        }
        return new Response($contents);
    }
}
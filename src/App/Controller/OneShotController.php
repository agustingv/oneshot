<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OneShot\Application\Post\Query\ViewPostByDateQuery;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class OneShotController extends AbstractController
{
  private MessageBusInterface $messageBus;

  public function __construct(MessageBusInterface $messageBus)
  {
    $this->messageBus = $messageBus;
  }

  /**
   * @Route("/", name="homepage")
   */
  public function index(Request $request) : Response
  {
    $page = 0;
    $posts = [];
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
      $posts = $handledStamp->getResult();

    } catch (\Exception $e) {
      throw new \Exception($e->getMessage());
    }

    return $this->render('oneshot/index.html.twig',['posts' => $posts]);
  }
 
}
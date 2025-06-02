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
      return $this->render('oneshot/pages/index.html.twig');
  }
 
  /**
   * @Route("/react")
   */
  public function react(Request $request) : Response
  {
    return $this->render('base2.html.twig');
  }
}
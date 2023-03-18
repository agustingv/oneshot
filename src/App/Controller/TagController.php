<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use OneShot\Domain\Tag\Tag;
use OneShot\Domain\ValueObjects\EntityId;
use OneShot\Application\Tag\Query\AutocompleteTagQuery;
use OneShot\Application\Tag\Command\CreateTagCommand;
use OneShot\Application\Tag\Query\LoadTagQuery;
use OneShot\Application\Post\Query\ViewPostByTagQuery;
use App\Form\Type\TagType;

class TagController extends AbstractController
{

    private MessageBusInterface $messageBus;
    
    public function __construct(MessageBusInterface $messageBus)
    {
      $this->messageBus = $messageBus;
    }

    /**
     * @Route("/tag/add")
     */
    public function tagAdd(Request $request) : Response
    {
        $form = $this->createForm(TagType::class);
        $form->handleRequest($request);

        $errors = [];

        if ($form->isSubmitted() && $form->isValid())
        {
           $tag = new Tag();
           $tag->setId(EntityId::generate()->toString());
           $tag->setName($form->get('name')->getData());

            try {
                $command = new CreateTagCommand($tag);
                $this->messageBus->dispatch($command);
                $this->addFlash('success', sprintf('Tag "%s" created', $tag->getName()));
            } catch (\Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        return $this->render('oneshot/forms/tag.add.form.html.twig',['form' => $form->createView(), 'errors' => $errors]);

    }

    /**
     * @Route("/tag/{uuid}")
     */
    public function tagView(Request $request, string $uuid) : Response
    {
        $errors = [];
        try {
            $query = new LoadTagQuery($uuid);
            $envelope = $this->messageBus->dispatch($query);
            $handledStamp = $envelope->last(HandledStamp::class);
            $tag = $handledStamp->getResult();
        } catch (\Exception $e) {
            $errors[] = $e->getMessage();
        }

        $query = new ViewPostByTagQuery(
            tag: $tag->toArray()
        );
        $envelope = $this->messageBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);
        $posts = $handledStamp->getResult();

        return $this->render('oneshot/pages/tags.html.twig',['posts' => $posts, 'tag' => $tag]);
    }


    /**
     * @Route("/tag/autocomplete/{name}")
     */
    public function tagAutocomplete(Request $request, string $name) : JsonResponse
    {
        $query = new AutocompleteTagQuery(name: $name);
        $envelope = $this->messageBus->dispatch($query);
        $handledStamp = $envelope->last(HandledStamp::class);
        $tags = $handledStamp->getResult();
        $chose = [];
        if (!empty($tags))
        {
            foreach ($tags as $tag)
            {
                $chose[$tag->getId()] = $tag->getName();
            }
        }
        return new JsonResponse(
            $chose
        );
    }

}
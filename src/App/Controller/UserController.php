<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use OneShot\Application\User\Query\SingInUserQuery;
use OneShot\Application\User\Query\LoadUserQuery;
use OneShot\Application\User\Query\VerifiedTokenUserQuery;
use OneShot\Application\User\Command\CreateUserCommand;
use OneShot\Application\User\Command\UpdateUserCommand;
use OneShot\Application\File\Command\CreateFileCommand;
use OneShot\Application\User\Query\FindUserByNameQuery;
use App\Form\Type\UserSingUpType;
use App\Form\Type\UserSingInType;
use OneShot\Domain\ValueObjects\EntityId;
use OneShot\Domain\User\User;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use App\Security\LoginAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use App\Form\Type\PostType;
use App\Form\Type\UserProfileType;
use App\Files\FileUpload;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    private MessageBusInterface $messageBus;
    private TokenStorageInterface $security;
    
    public function __construct(MessageBusInterface $messageBus, TokenStorageInterface $security)
    {
      $this->messageBus = $messageBus;
      $this->security = $security;
    }

  /**
   * @Route("/user/singup")
   */
    public function singUp(Request $request) : Response
    {
      
        $form = $this->createForm(UserSingUpType::class);
        $form->handleRequest($request);
    
        $errors = [];
        if ($form->isSubmitted() && $form->isValid()) 
        {

          try {
            $user = new User();
            $user->setName($form->get('name')->getData());
            $user->setMail($form->get('mail')->getData());
            $user->setPassword($form->get('password')->getData());

            $command = new CreateUserCommand(
              user: $user
            );
            $this->messageBus->dispatch($command);

            $this->addFlash('success', 'Register user is complete!. Please check your mail to validate account.');
            return new RedirectResponse('/');
          } catch (\Exception $e) {
            $errors[] = $e->getMessage();
          }
        }
            
        return $this->render('oneshot/forms/user.singup.form.html.twig',['form' => $form->createView(), 'errors' => $errors]);
    }

  /**
   * @Route("/user/singin", name="singin")
   */
    public function singIn(
    Request $request,
    UserAuthenticatorInterface $userAuthenticator,
    LoginAuthenticator $authenticator
    ) : Response 
    {

      $form = $this->createForm(UserSingInType::class);
      $form->handleRequest($request);
  
      $submittedToken = $request->request->get('token');
      $error = null;
      if ($form->isSubmitted() && $form->isValid()) 
      {
        $submittedToken = $request->request->get('token');
        if ($this->isCsrfTokenValid('user-singin', $submittedToken)) {

          try {

            $query = new SingInUserQuery(
              mail: $form->get('mail')->getData(),
              password: $form->get('password')->getData()
            );

            $envelope = $this->messageBus->dispatch($query);
            $handledStamp = $envelope->last(HandledStamp::class);
            $sessionCookieString = $handledStamp->getResult();

            $this->addFlash('success', 'Wellcome!');
            $response = new RedirectResponse('/');
            $response->headers->setCookie(new Cookie('idToken', $sessionCookieString));

            return $response;

          } catch (\Exception $e) {
            $error = $e->getMessage();
          }
          
        } else {
          return new RedirectResponse('/');
        }
      } 
      
      return $this->render('oneshot/forms/user.singin.form.html.twig',['form' => $form->createView(), 'error' => $error]);

    }

  /**
   * @Route("/user/singout", name="singout")
   */    
    public function singOut(Request $request) : Response
    {
      $response = new Response();
      $response->headers->clearCookie('idToken');
      $response->headers->clearCookie('REMEMBERME');

      $response->headers->set("Location","/");
      $response->send();
      $this->addFlash('success', 'Goodbye!');
      return $response;
    }


    public function userIn(Request $request) : Response
    {

      $user = $this->getUser();

      if (isset($user) AND in_array('IS_AUTHENTICATED_FULLY',$user->getRoles(), true)) 
      {
        $form = $this->createForm(PostType::class,['body' => " ", 'name' => 'lerelex', 'uid' => $this->getUser()->getUserIdentifier()]);
        $form->handleRequest($request);
        $errors = [];
        return $this->render('oneshot/forms/post.form.html.twig', ['form' => $form, 'errors' => $errors]);
      } else {
        return $this->render('oneshot/blocks/notin.html.twig');
      }
    }

  /**
   * @Route("/user/{uuid}/edit")
   */    
  public function profile(Request $request, string $uuid) : Response
  {

    try {
      EntityId::fromString($uuid);
    } catch (\Exception $e) {
      $this->addFlash('success', $e->getMessage());
      return new RedirectResponse('/');     
    }

    // check if profile edit is login user own
    $user = $this->security->getToken()->getUser();

    $user_id = $user->getUserIdentifier();
    if ($user_id !== $uuid) 
    {
      $this->addFlash('success', 'Unautorized access page');
      return new RedirectResponse('/');
    }

    $form = $this->createForm(UserProfileType::class,[
      'roles' => $user->getRoles()],
      [
        'role' => $user->getRoles()]
    );
    
    $form->handleRequest($request);

    $errors = [];
    if ($form->isSubmitted() && $form->isValid()) 
    {
      
      try {

        $user->setMail($form->get('mail')->getData());
        $user->setName($form->get('name')->getData());
        if ($form->has('roles'))
        {
          $user->setRoles($form->get('roles')->getData());
        }

        if ($image = $form->get('image_profile')->getData())
        {
          $newFile = FileUpload::prepareNewFiles($image, 'user/'.$user->getId().'/');

          $command = new CreateFileCommand(
            file: $newFile,
            upFile: $image
          );
          $this->messageBus->dispatch($command);
          $user->setImage($newFile);
        }
        $properties = ['email' => $user->getMail(), 'displayName' => $user->getName()];
        $updateCommand = new UpdateUserCommand(
          id: $user->getUserIdentifier(),
          properties: $properties,
          user: $user
        );
        $this->messageBus->dispatch($updateCommand);
        $this->addFlash('success', 'Profile updated');
      } catch (\Exception $e) {
        $errors[] = $e->getMessage();
      }
    }

    return $this->render('oneshot/forms/user.edit.form.html.twig',['form' => $form->createView(), 'errors' => $errors, 'user' => $user]);

  }

  /**
   * @Route("/find/user/{name}")
   */
  public function usersByName(Request $request, string $name) : Response
  {
    try {

      $query = new FindUserByNameQuery(
        name: $name
      );

      $envelope = $this->messageBus->dispatch($query);
      $handledStamp = $envelope->last(HandledStamp::class);
      $usersObj = $handledStamp->getResult();

      $users = [];
      foreach ($usersObj as $user)
      {
        $users[] = [
          'id' => '@'.$user->getName(), 
          'userId' => $user->getId(), 
          'name' => $user->getName(),
          'link' => '/user/'.$user->getId()
        ];
      }

      return new JsonResponse($users);

    } catch (\Exception $e) {
      $error = $e->getMessage();
    }   
    return new JsonResponse([]);
  }
}
<?php

// src/Security/ApiKeyAuthenticator.php
namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use OneShot\Application\User\Query\VerifiedTokenUserQuery;
use OneShot\Application\User\Query\LoadUserQuery;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\CustomCredentials;
use OneShot\Domain\User\User;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class LoginAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface
{

    private MessageBusInterface $messageBus;
    private RouterInterface $router;
    
    public function __construct(MessageBusInterface $messageBus, RouterInterface $router)
    {
      $this->messageBus = $messageBus;
      $this->router = $router;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new RedirectResponse("/user/singin");
    }


    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        $session = $request->cookies->get('idToken');
        if ($session) {
            return true;
        } else {
            return false;
        }
    }

    public function authenticate(Request $request): Passport
    {
        
        $idToken = $request->cookies->get('idToken');
        if (null !== $idToken) 
        {
          $verifyQuery = new VerifiedTokenUserQuery(idToken: $idToken);
          $verifiedIdToken = $this->messageBus->dispatch($verifyQuery);
          $handledStamp = $verifiedIdToken->last(HandledStamp::class);
          $verify = $handledStamp->getResult();
          $user_id = $verify->claims()->get('user_id');
          return new Passport(
            new UserBadge($user_id, function (string $userIdentifier) {
                if ($query = new LoadUserQuery($userIdentifier))
                {
                  $envelope = $this->messageBus->dispatch($query);
                  $handledStamp = $envelope->last(HandledStamp::class);
                  $user = $handledStamp->getResult();
                  return $user;
                }
                return null;
            }),
            new CustomCredentials(
                function ($credentials, User $user) {
                    return true;
                },
                $idToken
            )
            );
        }
        
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            // you may want to customize or obfuscate the message first
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }
}
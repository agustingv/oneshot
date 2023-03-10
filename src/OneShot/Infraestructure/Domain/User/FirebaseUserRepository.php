<?php

namespace OneShot\Infraestructure\Domain\User;

use OneShot\Domain\User\User;
use OneShot\Domain\ValueObjects\EntityId;
use OneShot\Domain\User\UserRepository;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FirebaseUserRepository implements UserRepository
{
    private $fireAuth;
    private $firestoreDb;
    

    public function __construct(ContainerInterface $container)
    {
        $this->fireAuth = $container->get('kreait_firebase.oneshot.auth');
        $this->firestoreDb = $container->get('kreait_firebase.oneshot.firestore')->database();
    }

    public function singIn(string $mail, string $password) : string
    {
        $fireAuthUser = $this->fireAuth->signInWithEmailAndPassword($mail, $password);
        $oneWeek = new \DateInterval('P7D');
        $sessionCookieString = $this->fireAuth->createSessionCookie($fireAuthUser->idToken(), $oneWeek);
        return $sessionCookieString;
        
    }

    public function verifyToken(string $idToken) : Object
    {
        $verify = $this->fireAuth->verifySessionCookie($idToken);
        return $verify;
    }
    
    public function create(User $user) : void
    {
        $userProperties = [
            'email' => $user->getMail(),
            'emailVerified' => true,
            'password' => $user->getPassword(),
            'displayName' => $user->getName(),
            'disabled' => true,
            "uid" => $user->getUserIdentifier()
        ];
        $user->unsetPassword();
        
        if ($this->fireAuth->createUser($userProperties))
        {
            $userArray =  $user->toArray();
            $this->firestoreDb->collection("users")->document($user->getUserIdentifier())->set($userArray);
        }
    }

    public function update(string $id, array $properties, User $user) : void
    {
        if ($this->fireAuth->updateUser($id, $properties)) 
        {
            $this->firestoreDb->collection("users")->document($id)->set($user->toArray());
        }
    }

    public function delete(User $user) : void
    {

    }

    public function load(string $id) : User
    {
        $data = $this->firestoreDb        
        ->collection('users')
        ->document($id)
        ->snapshot()
        ->data();

        try {
          
            $user = new User();
            return $user->fromArray($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }       
    }
    
    public function find_by_id(EntityId $id) : User
    {
        $data = $this->firestoreDb        
        ->collection('users')
        ->document($id)
        ->snapshot()
        ->data();

        try {
          
            $user = new User();
            return $user->fromArray($data);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }        
    }

    public function find_by_name(string $name) : array
    {
        $reference = $this->firestoreDb->collection('users');
        $query = $reference->orderBy('name', 'asc')
                    ->where('name', '>=', $name)
                    ->where('name', '<=', $name.'~');
        $documents = $query->documents();
        $users = [];
        foreach ($documents as $document)
        {
            $user = new User();
            $users[] = $user->fromArray($document->data());
        }
        return $users;
    }
}

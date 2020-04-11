<?php


namespace App\Security;


use App\Entity\User;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class GithubUserProvider
 * @package App\Security
 */
class GithubUserProvider implements UserProviderInterface
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Serializer
     */
    private $serializer;

    private $token;

    /**
     * GithubUserProvider constructor.
     * @param Client $client
     * @param SerializerInterface $serializer
     * @param $token
     */
    public function __construct(Client $client, SerializerInterface $serializer, $token)
    {
        $this->client = $client;
        $this->serializer = $serializer;
        $this->token = $token;
    }

    /**
     * @inheritDoc
     */
    public function loadUserByUsername($username)
    {
        $username = $this->token;
        $url = 'https://api.github.com/user?access_token='.$username;
        $response = $this->client->request('GET',$url);
        $result = $response->getBody()->getContents();

        if(!$result) {
            throw new \LogicException('Github couldn\'t return a user with the provided credentials' );
        }

        $userData = $this->serializer->deserialize($result, 'array', 'json');



        return new User($userData['roles'], $userData['username'], $userData['password']);
    }

    /**
     * @inheritDoc
     */
    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException();
        }
        return $user;
    }

    /**
     * @inheritDoc
     */
    public function supportsClass($class)
    {
        return 'App\Entity\User' === get_class($class);
    }
}
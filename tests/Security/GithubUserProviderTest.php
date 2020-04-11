<?php


namespace App\Tests\Security;


use App\Entity\User;
use App\Security\GithubUserProvider;
use GuzzleHttp\Client;

use JMS\Serializer\SerializerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class GithubUserProviderTest extends TestCase
{
    private $clientMock;

    private $responseInterfaceMock;

    private $streamInterfaceMock;

    private $serializerMock;

    public function setUp()
    {
        $this->streamInterfaceMock = $this
            ->createMock(StreamInterface::class)
        ;
        $this->responseInterfaceMock = $this
            ->createMock(ResponseInterface::class)
        ;
        $this->clientMock = $this
            ->createMock(Client::class)
        ;
        $this->serializerMock = $this
            ->createMock(SerializerInterface::class)
        ;
    }

    public function testLoadUserByUsernameReturnsAUser()
    {
        $this->streamInterfaceMock
            ->expects($this->once())
            ->method('getContents')
        ;
        $this->responseInterfaceMock
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamInterfaceMock)
        ;
        $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->responseInterfaceMock)
        ;
        $userData = ['roles' => 'ROLE_USER', 'username' => 'my_name', 'password' => 'my avatar url'];
        $this->serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($userData)
        ;

        $githubProvider = new GithubUserProvider($this->clientMock, $this->serializerMock, 'my_token');

        $user = $githubProvider->loadUserByUsername('my_token');
        $expectedUser = new User(
            $userData['roles'],
            $userData['username'],
            $userData['password']
        );
        $this->assertSame($expectedUser, $user);

    }

    /**
     * @expectedException \LogicException
     */
    public function testLoadUserByUsernameFailsReturningAUser()
    {
        $this->responseInterfaceMock
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->streamInterfaceMock)
        ;

        //dd($this->clientMock);

        $this->clientMock
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->responseInterfaceMock)
        ;
        /*$userData = [];
        $this->serializerMock
            ->expects($this->once())
            ->method('deserialize')
            ->willReturn($userData)
        ;*/

        $githubProvider = new GithubUserProvider($this->clientMock, $this->serializerMock, 'my_token');
        $githubProvider->loadUserByUsername('my_token');
    }
}
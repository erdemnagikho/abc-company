<?php


namespace App\Tests\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Order;
use App\Entity\User;
use App\EventSubscriber\AuthoredEntitySubscriber;
use phpDocumentor\Reflection\Types\This;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Csrf\TokenStorage\TokenStorageInterface;

class AuthoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration()
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();
        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            ['getAuthenticatedUser', EventPriorities::PRE_WRITE],
            $result[KernelEvents::VIEW]
        );
    }

    public function testSetAuthorCall(string $className, bool $shouldCallSetUser, string $method)
    {
        $entityMock = $this->getEntityMock($className, $shouldCallSetUser);
        $tokenStorageMock = $this->getTokenStorageMock();
        $eventMock = $this->getEventMock($method, $entityMock);

        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser(
            $eventMock
        );
    }

    public function testNoTokenPresent()
    {
        $tokenStorageMock = $this->getTokenStorageMock(false);
        $eventMock = $this->getEventMock('POST', new class {});

        (new AuthoredEntitySubscriber($tokenStorageMock))->getAuthenticatedUser(
            $eventMock
        );
    }

    public function providerSetUserCall(): array
    {
        return [
            [Order::class, true, 'POST'],
            [Order::class, false, 'GET'],
            ['NonExisting', false, 'POST'],
        ];
    }

    private function getTokenStorageMock(bool $hasToken = true): MockObject
    {
        $tokenMock = $this->getMockBuilder(TokenInterface::class)
            ->getMockForAbstractClass();
        $tokenMock->expects($hasToken ? $this->once() : $this->never())
            ->method('getUser')
            ->willReturn(new User());

        $tokenStorageMock = $this->getMockBuilder(TokenStorageInterface::class)
            ->getMockForAbstractClass();
        $tokenStorageMock->expects($this->once())
            ->method('getToken')
            ->willReturn($hasToken ? $tokenMock : null);

        return $tokenStorageMock;
    }

    private function getEventMock(string $method, $controllerResult): MockObject
    {
        $requestMock = $this->getMockBuilder(Request::class)
            ->getMock();
        $requestMock->expects($this->once())
            ->method('getMethod')
            ->willReturn($method);

        $eventMock =
            $this->getMockBuilder(ViewEvent::class)
                ->disableOriginalConstructor()
                ->getMock();

        $eventMock->expects($this->once())
            ->method('getControllerResult')
            ->willReturn($controllerResult);
        $eventMock->expects($this->once())
            ->method('getRequest')
            ->willReturn($requestMock);

        return $eventMock;
    }

    private function getEntityMock(string $className, bool $shouldCallSetUser): MockObject
    {
        $entityMock = $this->getMockBuilder($className)
            ->setMethods(['setUser'])
            ->getMock();
        $entityMock->expects($shouldCallSetUser ? $this->once() : $this->never())
            ->method('setUser');

        return $entityMock;
    }
}
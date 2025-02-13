<?php

namespace Tests\Unit\Mail;

use App\Entity\Security\LocalAccount;
use App\Mail\MailService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class MailServiceTest.
 *
 * @covers \App\Mail\MailService
 */
class MailServiceTest extends KernelTestCase
{
    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * @var MailerInterface&MockObject
     */
    protected $mailer;

    /**
     * @var EntityManagerInterface&MockObject
     */
    protected $em;

    /**
     * @var TokenStorageInterface&MockObject
     */
    protected $tokenStorage;

    /**
     * @var ParameterBagInterface&MockObject
     */
    protected $params;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->mailer = $this->createMock(MailerInterface::class);
        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->tokenStorage = $this->createMock(TokenStorageInterface::class);
        $this->params = $this->createMock(ParameterBagInterface::class);

        $this->mailService = new MailService($this->mailer, $this->em, $this->tokenStorage, $this->params);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->mailService);
        unset($this->mailer);
        unset($this->em);
        unset($this->tokenStorage);
        unset($this->params);
    }

    public function testMessageNull(): void
    {
        $this->em->expects(self::never())->method('persist');
        $this->mailer->expects(self::never())->method('send');
        $this->mailService->message(null, '', '');
    }

    public function testMessage(): void
    {
        $recipient = new LocalAccount();
        $recipient
            ->setEmail('john.doe@foo.bar')
            ->setName('John Doe')
        ;

        $this->em->expects(self::atLeastOnce())->method('persist');
        $this->em->expects(self::once())->method('flush');
        $this->mailer->expects(self::once())->method('send');
        $this->mailService->message($recipient, '', '');
    }
}

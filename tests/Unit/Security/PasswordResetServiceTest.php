<?php

namespace Tests\Unit\Security;

use App\Security\PasswordResetService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\PasswordHasherFactoryInterface;

/**
 * Class PasswordResetServiceTest.
 *
 * @covers \App\Security\PasswordResetService
 */
class PasswordResetServiceTest extends KernelTestCase
{
    /**
     * @var PasswordResetService
     */
    protected $passwordResetService;

    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var PasswordHasherFactoryInterface
     */
    protected $encoderFactory;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->encoderFactory = self::getContainer()->get(PasswordHasherFactoryInterface::class);
        $this->passwordResetService = new PasswordResetService($this->em, $this->encoderFactory);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->passwordResetService);
        unset($this->em);
        unset($this->encoderFactory);
    }

    public function testIsPasswordRequestTokenValid(): void
    {
        /* @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testGeneratePasswordRequestToken(): void
    {
        /* @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testResetPasswordRequestToken(): void
    {
        /* @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}

<?php

namespace Tests\Unit\Entity\Activity;

use App\Entity\Activity\Activity;
use App\Entity\Activity\PriceOption;
use App\Entity\Activity\Registration;
use App\Entity\Group\Group;
use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class PriceOptionTest.
 *
 * @covers \App\Entity\Activity\PriceOption
 */
class PriceOptionTest extends KernelTestCase
{
    /**
     * @var PriceOption
     */
    protected $priceOption;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->priceOption = new PriceOption();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->priceOption);
    }

    public function testGetId(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->priceOption, $expected);
        self::assertSame($expected, $this->priceOption->getId());
    }

    public function testSetId(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $this->priceOption->setId($expected);
        self::assertSame($expected, $property->getValue($this->priceOption));
    }

    public function testGetName(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('name');
        $property->setAccessible(true);
        $property->setValue($this->priceOption, $expected);
        self::assertSame($expected, $this->priceOption->getName());
    }

    public function testSetName(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('name');
        $property->setAccessible(true);
        $this->priceOption->setName($expected);
        self::assertSame($expected, $property->getValue($this->priceOption));
    }

    public function testGetTarget(): void
    {
        $expected = new Group();
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('target');
        $property->setAccessible(true);
        $property->setValue($this->priceOption, $expected);
        self::assertSame($expected, $this->priceOption->getTarget());
    }

    public function testSetTarget(): void
    {
        $expected = new Group();
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('target');
        $property->setAccessible(true);
        $this->priceOption->setTarget($expected);
        self::assertSame($expected, $property->getValue($this->priceOption));
    }

    public function testGetPrice(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('price');
        $property->setAccessible(true);
        $property->setValue($this->priceOption, $expected);
        self::assertSame($expected, $this->priceOption->getPrice());
    }

    public function testSetPrice(): void
    {
        $expected = 42;
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('price');
        $property->setAccessible(true);
        $this->priceOption->setPrice($expected);
        self::assertSame($expected, $property->getValue($this->priceOption));
    }

    public function testGetDetails(): void
    {
        $expected = [];
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('details');
        $property->setAccessible(true);
        $property->setValue($this->priceOption, $expected);
        self::assertSame($expected, $this->priceOption->getDetails());
    }

    public function testSetDetails(): void
    {
        $expected = [];
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('details');
        $property->setAccessible(true);
        $this->priceOption->setDetails($expected);
        self::assertSame($expected, $property->getValue($this->priceOption));
    }

    public function testGetConfirmationMsg(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('confirmationMsg');
        $property->setAccessible(true);
        $property->setValue($this->priceOption, $expected);
        self::assertSame($expected, $this->priceOption->getConfirmationMsg());
    }

    public function testSetConfirmationMsg(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('confirmationMsg');
        $property->setAccessible(true);
        $this->priceOption->setConfirmationMsg($expected);
        self::assertSame($expected, $property->getValue($this->priceOption));
    }

    public function testGetActivity(): void
    {
        $expected = new Activity();
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('activity');
        $property->setAccessible(true);
        $property->setValue($this->priceOption, $expected);
        self::assertSame($expected, $this->priceOption->getActivity());
    }

    public function testSetActivity(): void
    {
        $expected = new Activity();
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('activity');
        $property->setAccessible(true);
        $this->priceOption->setActivity($expected);
        self::assertSame($expected, $property->getValue($this->priceOption));
    }

    public function test__ToString(): void
    {
        $expected = 'free €0.00';
        $priceOption = new PriceOption();
        $priceOption->setName('free');
        $priceOption->setPrice(0);
        self::assertSame($expected, strval($priceOption));
    }

    public function testGetRegistrations(): void
    {
        $expected = new ArrayCollection();
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('registrations');
        $property->setAccessible(true);
        $property->setValue($this->priceOption, $expected);
        self::assertSame($expected, $this->priceOption->getRegistrations());
    }

    public function testAddRegistration(): void
    {
        $expected = new Registration();
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('registrations');
        $property->setAccessible(true);
        $this->priceOption->addRegistration($expected);
        self::assertSame($expected, $property->getValue($this->priceOption)[0]);
    }

    public function testRemoveRegistration(): void
    {
        $expected = new ArrayCollection();
        $registration = new Registration();
        $expected->add($registration);
        $property = (new ReflectionClass(PriceOption::class))
            ->getProperty('registrations');
        $property->setAccessible(true);
        $property->setValue($this->priceOption, $expected);
        $this->priceOption->removeRegistration($registration);
        self::assertNotSame($registration, $property->getValue($this->priceOption));
    }
}

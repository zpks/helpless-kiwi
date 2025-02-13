<?php

namespace Tests\Unit\Entity\Group;

use App\Entity\Group\Group;
use App\Entity\Group\Relation;
use App\Entity\Security\LocalAccount;
use Doctrine\Common\Collections\ArrayCollection;
use ReflectionClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class RelationTest.
 *
 * @covers \App\Entity\Group\Relation
 * @group entities
 */
class RelationTest extends KernelTestCase
{
    /**
     * @var Relation
     */
    protected $relation;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->relation = new Relation();
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->relation);
    }

    public function testGetId(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->relation, $expected);
        self::assertSame($expected, $this->relation->getId());
    }

    public function testSetId(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $this->relation->setId($expected);
        self::assertSame($expected, $property->getValue($this->relation));
    }

    public function testGetDescription(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('description');
        $property->setAccessible(true);
        $property->setValue($this->relation, $expected);
        self::assertSame($expected, $this->relation->getDescription());
    }

    public function testSetDescription(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('description');
        $property->setAccessible(true);
        $this->relation->setDescription($expected);
        self::assertSame($expected, $property->getValue($this->relation));
    }

    public function testGetGroup(): void
    {
        $expected = new Group();
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('group');
        $property->setAccessible(true);
        $property->setValue($this->relation, $expected);
        self::assertSame($expected, $this->relation->getGroup());
    }

    public function testSetGroup(): void
    {
        $expected = new Group();
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('group');
        $property->setAccessible(true);
        $this->relation->setGroup($expected);
        self::assertSame($expected, $property->getValue($this->relation));
    }

    public function testGetPerson(): void
    {
        $expected = new LocalAccount();
        $expected->setEmail('john@doe.eyes');
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('person');
        $property->setAccessible(true);
        $property->setValue($this->relation, $expected);
        self::assertSame($expected, $this->relation->getPerson());
    }

    public function testSetPerson(): void
    {
        $expected = new LocalAccount();
        $expected->setEmail('john@doe.eyes');
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('person');
        $property->setAccessible(true);
        $this->relation->setPerson($expected);
        self::assertSame($expected, $this->relation->getPerson());
    }

    public function testGetParent(): void
    {
        $expected = new Relation();
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('parent');
        $property->setAccessible(true);
        $property->setValue($this->relation, $expected);
        self::assertSame($expected, $this->relation->getParent());
    }

    public function testSetParent(): void
    {
        $expected = new Relation();
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('parent');
        $property->setAccessible(true);
        $this->relation->setParent($expected);
        self::assertSame($expected, $property->getValue($this->relation));
    }

    public function testGetChildren(): void
    {
        /** @var ArrayCollection<int, Relation> */
        $expected = new ArrayCollection();
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('children');
        $property->setAccessible(true);
        $property->setValue($this->relation, $expected);
        self::assertSame($expected, $this->relation->getChildren());
    }

    public function testAddChild(): void
    {
        /* @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testRemoveChild(): void
    {
        $expected = new ArrayCollection();
        $relation = new Relation();
        $expected->add($relation);
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('children');
        $property->setAccessible(true);
        $property->setValue($this->relation, $expected);

        $this->relation->removeChild($relation);
        self::assertNotSame($relation, $property->getValue($this->relation));
    }

    //TODO figure out how to create recurvice testing objects
    public function testGetRoot(): void
    {
        /* @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    //TODO this feels.... funky, and not in the good way.
    public function testGetRootChildless(): void
    {
        $expected = '42';
        $property = (new ReflectionClass(Relation::class))
            ->getProperty('id');
        $property->setAccessible(true);
        $property->setValue($this->relation, $expected);
        self::assertSame($expected, $this->relation->getRoot()->getId());
    }

    public function testGetChildrenRecursive(): void
    {
        /* @todo This test is incomplete. */
        self::markTestIncomplete();
    }

    public function testGetAllRelations(): void
    {
        /* @todo This test is incomplete. */
        self::markTestIncomplete();
    }
}

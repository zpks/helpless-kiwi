<?php

namespace Tests\Unit\Calendar;

use App\Calendar\ICalProvider;
use App\Entity\Activity\Activity;
use App\Entity\Location\Location;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class ICalProviderTest.
 *
 * @author A-Daneel
 * @covers \App\Calendar\ICalProvider
 */
class ICalProviderTest extends KernelTestCase
{
    /** @var ICalProvider */
    protected $iCalProvider;

    /** @var Activity */
    protected $firstActivity;
    /** @var Activity */
    protected $secondActivity;
    /** @var Activity */
    protected $invalidActivity;

    /** @var DateTime */
    protected $now;

    /** @var string */
    protected $summary;
    /** @var string */
    protected $description;

    /** @var Location */
    protected $location;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->iCalProvider = new ICalProvider();
        $this->firstActivity = new Activity();
        $this->secondActivity = new Activity();
        $this->invalidActivity = new Activity();

        $this->now = new \DateTime();
        $this->summary = 'kiwi test summary';
        $this->description = 'kiwi test description';
        $this->location = new Location();

        $this->location->setAddress('@localhost');

        $this
            ->firstActivity
            ->setStart($this->now)
            ->setEnd($this->now)
            ->setName($this->summary)
            ->setLocation($this->location)
            ->setDescription($this->description)
            ->setId('a')
        ;

        $this
            ->secondActivity
            ->setStart($this->now)
            ->setEnd($this->now)
            ->setName('second '.$this->summary)
            ->setLocation($this->location)
            ->setDescription('second '.$this->description)
            ->setId('b')
        ;

        $this
            ->invalidActivity
            ->setStart($this->now)
            ->setEnd($this->now)
        ;
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        parent::tearDown();

        unset($this->iCalProvider);
        unset($this->firstActivity);
        unset($this->secondActivity);
        unset($this->invalidActivity);
    }

    /**
     * @test
     */
    public function icalsingleSuccesWithSingleEvent(): void
    {
        $recieved = $this->iCalProvider->icalSingle(
            $this->firstActivity
        );

        self::assertStringContainsString('PRODID:-//Helpless Kiwi//'.$_ENV['ORG_NAME'].' v1.0//NL', $recieved);
        $start = $this->firstActivity->getStart();
        if (!is_null($start)) {
            $start = $start->format('Ymd\THis');
        }
        self::assertStringContainsString('DTSTART:'.$start, $recieved);
        self::assertStringContainsString('SUMMARY:'.$this->summary, $recieved);
        self::assertStringContainsString('LOCATION:'.$this->location->getAddress(), $recieved);
        self::assertStringContainsString('DESCRIPTION:kiwi test description', $recieved);
    }

    /**
     * @test
     */
    public function icalsingleSuccesWithErrorHandling(): void
    {
        $this->expectError();
        $this->iCalProvider->icalSingle(
            $this->invalidActivity
        );
    }

    /**
     * @test
     */
    public function icalfeedSuccesWithSingleEvent(): void
    {
        $recieved = $this->iCalProvider->icalFeed([
            $this->firstActivity,
        ]);
        self::assertStringContainsString('PRODID:-//Helpless Kiwi//'.$_ENV['ORG_NAME'].' v1.0//NL', $recieved);
        $start = $this->firstActivity->getStart();
        if (!is_null($start)) {
            $start = $start->format('Ymd\THis');
        }
        self::assertStringContainsString('DTSTART:'.$start, $recieved);
        self::assertStringContainsString('SUMMARY:'.$this->summary, $recieved);
        self::assertStringContainsString('LOCATION:'.$this->location->getAddress(), $recieved);
        self::assertStringContainsString('DESCRIPTION:'.$this->description, $recieved);
    }

    /**
     * @test
     */
    public function icalfeedSuccesWithTwoValidEvents(): void
    {
        $recieved = $this->iCalProvider->icalFeed([
            $this->firstActivity,
            $this->secondActivity,
        ]);
        self::assertStringContainsString('SUMMARY:'.$this->summary, $recieved);
        self::assertStringContainsString('SUMMARY:second '.$this->summary, $recieved);
    }

    /**
     * @test
     */
    public function icalfeedSuccesWithOneValidAndOneInvalidEvent(): void
    {
        $recieved = $this->iCalProvider->icalFeed([
            $this->invalidActivity,
            $this->secondActivity,
        ]);
        self::assertStringContainsString('SUMMARY:second '.$this->summary, $recieved);
    }

    /**
     * @test
     */
    public function icalfeedSuccesWithMissingEnvVariable(): void
    {
        $_orgName = $_ENV['ORG_NAME'];
        unset($_ENV['ORG_NAME']);
        $recieved = $this->iCalProvider->icalFeed([
            $this->firstActivity,
        ]);
        self::assertStringContainsString('PRODID:-//Helpless Kiwi//kiwi v1.0//NL', $recieved);
        $_ENV['ORG_NAME'] = $_orgName;
    }
}

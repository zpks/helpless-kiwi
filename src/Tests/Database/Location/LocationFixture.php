<?php

namespace App\Tests\Database\Location;

use App\Entity\Location\Location;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class LocationFixture extends Fixture
{
    public const LOCATION_REFERENCE = 'localhost';

    public function load(ObjectManager $manager): void
    {
        $location = new Location();
        $location->setAddress('@localhost');

        $manager->persist($location);
        $this->addReference(self::LOCATION_REFERENCE, $location);

        $manager->flush();
    }
}

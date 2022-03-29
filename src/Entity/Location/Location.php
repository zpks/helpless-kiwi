<?php

namespace App\Entity\Location;

use Doctrine\ORM\Mapping as ORM;
use Overblog\GraphQLBundle\Annotation as GQL;

/**
 * @ORM\Entity
 * @GQL\Type
 * @GQL\Description("A physical location where activities are organized.")
 */
class Location
{
    /**
     * @var string | null
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @var string | null
     * @ORM\Column(type="string")
     * @GQL\Field(type="String")
     * @GQL\Description("The address of the location.")
     */
    private $address;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }
}

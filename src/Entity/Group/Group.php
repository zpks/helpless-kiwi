<?php

namespace App\Entity\Group;

use App\Entity\Taxonomy\Taxonomy;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(name="children", inversedBy="parent")
 * })
 */
class Group extends Taxonomy
{
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Group\Member", mappedBy="taxonomy", orphanRemoval=true)
     */
    private $members;

    public function __construct()
    {
        parent::__construct();

        $this->members = new ArrayCollection();
    }

    /**
     * @return Collection|Member[]
     */
    public function getMembers(): Collection
    {
        return $this->members;
    }

    public function addMember(Member $member): self
    {
        if (!$this->members->contains($member)) {
            $this->members[] = $member;
            $member->setTaxonomy($this);
        }

        return $this;
    }

    public function removeMember(Member $member): self
    {
        if ($this->members->contains($member)) {
            $this->members->removeElement($member);
            // set the owning side to null (unless already changed)
            if ($member->getTaxonomy() === $this) {
                $member->setTaxonomy(null);
            }
        }

        return $this;
    }
}

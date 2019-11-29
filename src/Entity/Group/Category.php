<?php

namespace App\Entity\Group;

use App\Entity\Taxonomy\Taxonomy;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(name="children", inversedBy="parent")
 * })
 */
class Category extends Taxonomy
{
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasGroups;

    public function getNoGroups(): ?bool
    {
        return null === $this->hasGroups ? null : !$this->hasGroups;
    }

    public function getHasGroups(): ?bool
    {
        return $this->hasGroups;
    }

    public function setHasGroups(bool $hasGroups): self
    {
        $this->hasGroups = $hasGroups;

        return $this;
    }

    /**
     * @return Collection|Category[]
     */
    public function getCategories(): Collection
    {
        return $this->children->filter(function ($x) { return $x instanceof Category; });
    }

    public function addCategory(Category $category): self
    {
        if (!$this->children->contains($category)) {
            $this->children[] = $category;
            $category->setParent($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        if ($this->children->contains($category)) {
            $this->children->removeElement($category);
            // set the owning side to null (unless already changed)
            if ($category->getParent() === $this) {
                $category->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Group[]
     */
    public function getGroups(): Collection
    {
        return $this->children->filter(function ($x) { return $x instanceof Group; });
    }

    public function addGroup(Group $group): self
    {
        if (!$this->children->contains($group)) {
            $this->children[] = $group;
            $group->setParent($this);
        }

        return $this;
    }

    public function removeGroup(Group $group): self
    {
        if ($this->children->contains($group)) {
            $this->children->removeElement($group);
            // set the owning side to null (unless already changed)
            if ($group->getParent() === $this) {
                $group->setParent(null);
            }
        }

        return $this;
    }
}

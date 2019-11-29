<?php

namespace App\Entity\Taxonomy;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class Taxonomy
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100, name="title")
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Taxonomy\Taxonomy", inversedBy="children")
     * @ORM\JoinColumn(name="parent", referencedColumnName="id")
     */
    private $parent;


     /**
     * @ORM\OneToMany(targetEntity="App\Entity\Group\Taxonomy", mappedBy="parent")
     */
    protected $children;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $hasChildren;

    /**
     * @ORM\Column(type="boolean")
     */
    private $readonly;

    /**
     * Get id.
     *
     * @return string
     */
    public function getId(): ?string
    {
        return $this->id;
    }

    /**
     * Set id.
     *
     * @param string $id
     */
    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set name.
     *
     * @param string $name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function setParent(?self $parent): self
    {
        $this->parent = $parent;

        return $this;
    }


    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->readonly = false;
    }


    /**
    *   @return Collection|self[]
    */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function getNoChildren(): ?bool
    {
        return null === $this->hasChildren ? null : !$this->hasChildren;
    }

    public function getHasChildren(): ?bool
    {
        return $this->hasChildren;
    }

    public function setHasChildren(bool $hasChildren): self
    {
        $this->hasChildren = $hasChildren;

        return $this;
    }

    
    

    public function getReadonly(): ?bool
    {
        return $this->readonly;
    }

    public function setReadonly(bool $readonly): self
    {
        $this->readonly = $readonly;

        return $this;
    }
}

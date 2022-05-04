<?php

namespace App\Entity\Mail;

use App\Entity\Security\LocalAccount;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Mail
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     *
     * @var ?string
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Security\LocalAccount")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id")
     *
     * @var ?LocalAccount
     */
    private $person;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Mail\Recipient", mappedBy="mail")
     *
     * @var Collection<int,Recipient>
     */
    private $recipients;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     *
     * @var string
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255)
     *
     * @var string
     */
    private $sender;

    /**
     * @ORM\Column(type="datetime")
     *
     * @var DateTime
     */
    private $sentAt;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getPerson(): ?LocalAccount
    {
        return $this->person;
    }

    public function setPerson(?LocalAccount $person): self
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get recipients options.
     *
     * @return Collection<int,Recipient>
     */
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    public function addRecipient(Recipient $recipient): self
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients[] = $recipient;
            $recipient->setMail($this);
        }

        return $this;
    }

    public function removeRecipient(Recipient $recipient): self
    {
        if ($this->recipients->contains($recipient)) {
            $this->recipients->removeElement($recipient);
            // set the owning side to null (unless already changed)
            if ($recipient->getMail() === $this) {
                $recipient->setMail(null);
            }
        }

        return $this;
    }

    public function getSender(): ?string
    {
        return $this->sender;
    }

    public function setSender(string $sender): self
    {
        $this->sender = $sender;

        return $this;
    }

    public function getSentAt(): ?DateTime
    {
        return $this->sentAt;
    }

    public function setSentAt(DateTime $sentAt): self
    {
        $this->sentAt = $sentAt;

        return $this;
    }
}

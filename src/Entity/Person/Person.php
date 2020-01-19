<?php

namespace App\Entity\Person;

use App\Entity\Security\Auth;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PersonRepository")
 */
class Person
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="UUID")
     * @ORM\Column(type="guid")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Security\Auth", mappedBy="person")
     */
    private $auth;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $shortname_expr;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name_expr;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Person\PersonValue", mappedBy="person", orphanRemoval=true, fetch="EAGER")
     */
    private $fieldValues;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Person\PersonScheme")
     */
    private $scheme;

    public function __construct()
    {
        $this->fieldValues = new ArrayCollection();
    }

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
     * Get authentication entity.
     *
     * @return string
     */
    public function getAuth(): ?Auth
    {
        return $this->auth;
    }

    /**
     * Set authentication entity.
     *
     * @param Auth $auth
     */
    public function setAuth(?Auth $auth): self
    {
        $this->auth = $auth;

        return $this;
    }

    /**
     * Get email address.
     *
     * @return string
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * Set email address.
     *
     * @param string $email
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|PersonValue[]
     */
    public function getFieldValues(): Collection
    {
        return $this->fieldValues;
    }

    public function addFieldValue(PersonValue $fieldValue): self
    {
        if (!$this->fieldValues->contains($fieldValue)) {
            $this->fieldValues[] = $fieldValue;
            $fieldValue->setPerson($this);
        }

        return $this;
    }

    public function removeFieldValue(PersonValue $fieldValue): self
    {
        if ($this->fieldValues->contains($fieldValue)) {
            $this->fieldValues->removeElement($fieldValue);
            // set the owning side to null (unless already changed)
            if ($fieldValue->getPerson() === $this) {
                $fieldValue->setPerson(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PersonValue[]
     */
    public function getValues(): Collection
    {
        if ($this->getScheme()) {
            // For each field in the scheme, map it to the corresponding PersonValue
            // associated with $this
            $mapToValue = function ($field) {
                $findValue = function ($value) use ($field) {
                    return $value->getField()->getId() == $field->getId();
                };

                return $this->getFieldValues()->filter($findValue)->first();
            };

            return $this->getScheme()->getFields()->map($mapToValue);
        } else {
            return $this->getFieldValues();
        }
    }

    public function getShortnameExpr(): ?string
    {
        return $this->shortname_expr;
    }

    public function setShortnameExpr(?string $shortname_expr): self
    {
        $this->shortname_expr = $shortname_expr;

        return $this;
    }

    public function getNameExpr(): ?string
    {
        return $this->name_expr;
    }

    public function setNameExpr(?string $name_expr): self
    {
        $this->name_expr = $name_expr;

        return $this;
    }

    public function getScheme(): ?PersonScheme
    {
        return $this->scheme;
    }

    public function setScheme(?PersonScheme $scheme): self
    {
        $this->scheme = $scheme;

        return $this;
    }

    public function getName(): ?string
    {
        $ownExpr = $this->getNameExpr();
        $scheme = $this->getScheme();

        if (is_null($ownExpr) && is_null($scheme)) {
            return null;
        }

        return $this->evalExpr($ownExpr ?? $scheme->getNameExpr());
    }

    public function getShortname(): ?string
    {
        $ownExpr = $this->getNameExpr();
        $scheme = $this->getScheme();

        if (is_null($ownExpr) && is_null($scheme)) {
            return null;
        }

        return $this->evalExpr($ownExpr ?? $scheme->getShortnameExpr());
    }

    public function getCanonical(): ?string
    {
        $pseudo = sprintf('pseudonymized (%s...)', substr($this->getId(), 0, 8));

        return $this->getName() ?? $this->getShortname() ?? $this->getEmail() ?? $pseudo;
    }

    public function __toString()
    {
        return $this->getCanonical();
    }

    private function evalExpr(?string $expr)
    {
        if (is_null($expr)) {
            return null;
        }

        $lang = new ExpressionLanguage();
        $lang->register('has', function ($str) {
            return 'isset(${'.$str.'})';
        }, function ($arguments, $str) {
            return array_key_exists($str, $arguments);
        });

        $lang->register('get', function ($str) {
            return '(${'.$str.'} ?? null)';
        }, function ($arguments, $str) {
            return $arguments[$str] ?? null;
        });

        $vars = [];
        foreach ($this->getValues() as $value) {
            $name = $value->getBuiltin() ?? $value->getField()->getSlug();
            $vars[$name] = $value->getValue();
        }
        $vars['auth'] = $this->getAuth();

        try {
            return $lang->evaluate($expr, $vars);
        } catch (\Exception $e) {
            return null;
        }
    }
}

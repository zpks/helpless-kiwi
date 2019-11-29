<?php

namespace App\Entity\Group;

use App\Entity\Taxonomy\Relation;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\AssociationOverrides({
 *      @ORM\AssociationOverride(name="children", inversedBy="parent")
 * })
 */
class Member extends Relation
{
}

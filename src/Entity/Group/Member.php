<?php

namespace App\Entity\Group;

use App\Entity\Taxonomy\Relation;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class Member
{
    use Relation;
}

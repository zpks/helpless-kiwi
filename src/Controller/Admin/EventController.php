<?php

namespace App\Controller\Admin;

use App\Entity\Log\Event;
use App\Log\EventService;
use App\Template\Attribute\MenuItem;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Event controller.
 */
#[Route("/admin/event", name: "admin_event_")]
class EventController extends AbstractController
{
    /**
     * @var EventService
     */
    private $events;

    public function __construct(EventService $events)
    {
        $this->events = $events;
    }

    /**
     * Lists all events.
     */
    #[MenuItem(title: "Gebeurtenislog", menu: "admin", role: "ROLE_ADMIN")]
    #[Route("/", name: "index", methods: ["GET"])]
    public function indexAction(Request $request, EntityManagerInterface $em): Response
    {
        $qb = $em->createQueryBuilder()
            ->select('e')
            ->from(Event::class, 'e')
            ->orderBy('e.time', 'DESC')
        ;

        $pagination = $this->paginate($qb, $request->query->getInt('index'), $request->query->getInt('results', 30));

        return $this->render('admin/event/index.html.twig', [
            'log' => $this->events->populateAll($pagination['results']),
            'pagination' => $pagination,
        ]);
    }

    private function paginate(QueryBuilder $qb, int $index = 0, int $limit = 30)
    {
        $index = \max(0, $index);
        $limit = \max(1, $limit);

        $cqb = clone $qb;
        $count = current(
            $cqb
            ->select('count('.$qb->getRootAlias().')')
            ->getQuery()
            ->getOneOrNullResult()
        );

        $rqb = clone $qb;
        $results = $rqb
            ->setFirstResult($index)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;

        $prev = $index > 0 ? \max(0, $index - $limit) : null;
        $next = $index + $limit < $count ? $index + $limit : null;

        return [
            'total' => $count,
            'results' => $results,
            'hasPrev' => !\is_null($prev),
            'hasNext' => !\is_null($next),
            'prev' => $prev,
            'index' => $index,
            'next' => $next,
        ];
    }
}

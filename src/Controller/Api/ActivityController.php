<?php

namespace App\Controller\Api;

use App\Entity\Activity\Activity;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Log\EventService;
use App\Controller\Access\ActivityAccess;

/**
 * Activity controller.
 *
 * @Route("/api/activity", name="api_activity_")
 */
class ActivityController extends AbstractController
{
    private $access;

    public function __construct(EventService $events, ActivityAccess $access)
    {
        $this->access = $access;
    }

    /**
     * Lists all activities.
     *
     * @Route("/", name="index", methods={"GET"})
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $activities = $this->access->fetch();

        return $this->json($activities);
    }

    /**
     * Finds and displays a activity entity.
     *
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function showAction(string $id)
    {
        $activity = $this->access->read($id);

        return $this->json($activity);
    }
}

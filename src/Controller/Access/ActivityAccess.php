<?php

namespace App\Controller\Access;

use App\Entity\Activity\Activity;
use Symfony\Component\HttpFoundation\Request;

/**
 * Activity access.
 */
class ActivityAccess extends AbstractAccess
{
    /**
     * Finds and displays a activity entity.
     */
    public function fetch(): array
    {
        $em = $this->getDoctrine()->getManager();

        $activities = $em->getRepository(Activity::class)->findBy([], ['start' => 'DESC']);

        return $activities;
    }

    /**
     * Creates a new activity entity.
     */
    public function create(Request $request)
    {
        $activity = new Activity();

        $form = $this->createForm('App\Form\Activity\ActivityType', $activity);
        $this->handleRequest($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $em->persist($activity);
            $em->persist($activity->getLocation());
            $em->flush();

            return $activity;
        }

        return $form;
    }

    /**
     * Finds and displays a activity entity.
     */
    public function read(string $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $activity = $em->getRepository(Activity::class)->find($uuid);

        if (!$this->isGranted('ROLE_USER')) {
            $activity->setRegistrations(null);
        }

        return $this->generateAccess($activity);
    }

    /**
     * Displays a form to edit an existing activity entity.
     */
    public function update(string $uuid, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $activity = $em->getRepository(Activity::class)->find($uuid);

        $form = $this->createForm('App\Form\Activity\ActivityType', $activity);
        $this->handleRequest($form, $request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $activity;
        }

        return $form;
    }

    /**
     * Deletes a ApiKey entity.
     */
    public function delete(string $uuid)
    {
        $em = $this->getDoctrine()->getManager();

        $activity = $em->getRepository(Activity::class)->find($uuid);

        $em->remove($activity);
        $em->flush();

        return self::SUCCESS;
    }
}

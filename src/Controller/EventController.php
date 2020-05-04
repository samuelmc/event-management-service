<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\EventRepository;
use App\Service\RequestHelper;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="event_")
 */
class EventController extends AbstractController
{
    /**
     * @Route("/", name="home", methods={"GET"})
     * @param EventRepository $eventRepository
     * @return Response
     */
    public function home(EventRepository $eventRepository): Response
    {
        return $this->render('event/home.html.twig', [
            'events' => $eventRepository->findHomePageEvents(),
        ]);
    }

    /**
     * @Route("/events", name="index", methods={"GET"})
     * @param Request $request
     * @param RequestHelper $requestHelper
     * @param EventRepository $eventRepository
     * @return Response
     */
    public function index(Request $request, RequestHelper $requestHelper, EventRepository $eventRepository): Response
    {
        $response = New Response();
        list($search, $page, $items, $sort, $order)
            = $requestHelper->listRequestData($request, $response, 'events');

        $requestHelper->rememberSortingSelection($request, $response, 'events');

        $queryParams = [];
        if ($search) $queryParams['search'] = $search;
        if ($page > 1) $queryParams['page'] = $page;

        return $response->setContent($this->renderView('event/index.html.twig', [
            'events' => $eventRepository->findByPage($page, $items, $search, $sort, $order),
            'queryParams' => $queryParams,
            'sorting' => ['field' => $sort, 'order' => $order],
            'pagination' => $requestHelper->getPaginationParams($request, $response, $eventRepository)
        ]));
    }

    /**
     * @Route("/event/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugger = New Slugger();
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $this->getUser();

            $slugger->sluggifyEntity($entityManager, $event, 'title');
            $event->setCreatedBy($user);

            $entityManager->persist($event);
            $entityManager->flush();

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/new.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/{slug}", name="show", methods={"GET"})
     * @param Event $event
     * @return Response
     */
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    /**
     * @Route("/event/{slug}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param Event $event
     * @return Response
     */
    public function edit(Request $request, Event $event): Response
    {
        $this->denyAccessUnlessGranted('edit', $event);
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('event_index');
        }

        return $this->render('event/edit.html.twig', [
            'event' => $event,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/event/{slug}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param Event $event
     * @return Response
     */
    public function delete(Request $request, Event $event): Response
    {
        $this->denyAccessUnlessGranted('delete',$event);
        if ($this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($event);
            $entityManager->flush();
        }

        return $this->redirectToRoute('event_index');
    }
}

<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\EventRepository;
use App\Repository\UserRepository;
use App\Service\RequestHelper;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile")
 */
class UserController extends AbstractController
{

    /**
     * @Route("/", name="user_profile", methods={"GET"})
     */
    public function show(Request $request, RequestHelper $requestHelper, EventRepository $eventRepository): Response
    {
        $response = New Response();
        list($search, $page, $items, $sort, $order)
            = $requestHelper->listRequestData($request, $response, 'events');

        $requestHelper->rememberSortingSelection($request, $response, 'events');

        $queryParams = [];
        if ($search) $queryParams['search'] = $search;
        if ($page > 1) $queryParams['page'] = $page;

        /** @var User $user */
        $user = $this->getUser();
        $userEvents = $eventRepository->findOwnEvents($user, $page, $items, $search, $sort, $order);

        $totalItems = $eventRepository->countOwnEvents($user, $search);
        $pages = $totalItems > 0 ? ceil($totalItems/$items) : 1;

        $pagination = [
            'page' => $page,
            'pages' => $pages,
            'pageItems' => $items,
            'totalItems' => $totalItems,
            'from' => ($items*($page-1)),
            'to' => ($items*$page) < $totalItems ? ($items*$page) : $totalItems
        ];


        return $response->setContent($this->renderView('user/show.html.twig', [
            'user' => $user,
            'events' => $eventRepository->findOwnEvents($user, $page, $items, $search, $sort, $order),
            'queryParams' => $queryParams,
            'sorting' => ['field' => $sort, 'order' => $order],
            'pagination' => $pagination
        ]));
    }

    /**
     * @Route("/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/remove", name="user_remove", methods={"DELETE"})
     */
    public function delete(Request $request): Response
    {
        $user = $this->getUser();
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $user->setActive(false);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_logout');
    }
}

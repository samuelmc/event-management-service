<?php /** @noinspection ALL */

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Form\CityType;
use App\Repository\CityRepository;
use App\Repository\EventRepository;
use App\Service\RequestHelper;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\Slugger;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="city_")
 */
class CityController extends AbstractController
{
    /**
     * @Route("/cities", name="index", methods={"GET","POST"})
     * @param Request $request
     * @param CityRepository $cityRepository
     * @return Response
     */
    public function index(Request $request, RequestHelper $requestHelper, CityRepository $cityRepository): Response
    {
        $newCity = new City();
        $form = $this->createForm(CityType::class, $newCity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugger = New Slugger();
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->getDoctrine()->getManager();

            $slugger->sluggifyEntity($entityManager, $city, 'name');
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('city_index');
        }

        $response = New Response();
        list($search, $page, $items, $sort, $order)
            = $requestHelper->listRequestData($request, $response, 'cities');

        $requestHelper->rememberSortingSelection($request, $response, 'cities');

        $queryParams = [];
        if ($search) $queryParams['search'] = $search;
        if ($page > 1) $queryParams['page'] = $page;

        return $response->setContent($this->renderView('city/index.html.twig', [
            'cities' => $cityRepository->findByPage($page, $items, $search, $sort, $order),
            'new_city' => $newCity,
            'form' => $form->createView(),
            'queryParams' => $queryParams,
            'sorting' => ['field' => $sort, 'order' => $order],
            'pagination' => $requestHelper->getPaginationParams($request, $response, $cityRepository)
        ]));
    }

    /**
     * @Route("/city/{slug}", name="show", methods={"GET"})
     * @param City $city
     * @return Response
     */
    public function show(Request $request, RequestHelper $requestHelper, CityRepository $cityRepository, EventRepository $eventRepository, City $city): Response
    {
        $response = New Response();
        list($search, $page, $items, $sort, $order)
            = $requestHelper->listRequestData($request, $response, 'events');

        $requestHelper->rememberSortingSelection($request, $response, 'events');

        $queryParams = [];
        if ($search) $queryParams['search'] = $search;
        if ($page > 1) $queryParams['page'] = $page;

        return $response->setContent($this->renderView('city/show.html.twig', [
            'city' => $city,
            'events' => $eventRepository->findByPage($page, $items, $search, $sort, $order, $city),
            'queryParams' => $queryParams,
            'sorting' => ['field' => $sort, 'order' => $order],
            'pagination' => $requestHelper->getPaginationParams($request, $response, $eventRepository)
        ]));
    }

    /**
     * @Route("/city/{slug}/edit", name="edit", methods={"GET","POST"})
     * @param Request $request
     * @param City $city
     * @return Response
     */
    public function edit(Request $request, City $city): Response
    {
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('city_index');
        }

        return $this->render('city/edit.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/city/{slug}", name="delete", methods={"DELETE"})
     * @param Request $request
     * @param City $city
     * @return Response
     */
    public function delete(Request $request, City $city): Response
    {
        if ($this->isCsrfTokenValid('delete'.$city->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($city);
            $entityManager->flush();
        }

        return $this->redirectToRoute('city_index');
    }
}

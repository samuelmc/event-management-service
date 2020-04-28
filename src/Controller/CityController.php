<?php /** @noinspection ALL */

namespace App\Controller;

use App\Entity\City;
use App\Entity\User;
use App\Form\CityType;
use App\Repository\CityRepository;
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
     * @Route("/cities", name="index", methods={"GET"})
     * @param Request $request
     * @param CityRepository $cityRepository
     * @return Response
     */
    public function index(Request $request, CityRepository $cityRepository): Response
    {
        $newCity = new City();
        $form = $this->createForm(CityType::class, $newCity);

        $search = $request->query->get('search', '');
        $page = (int) $request->query->get('page', 1);
        $items = (int) $request->query->get('items', 2);
        $sort = $request->query->get('sort', 'createdAt');
        $order = $request->query->get('order', 'desc');

        $totalItems = $cityRepository->countCities($search);
        $pages = ceil($totalItems/$items);

        return $this->render('city/index.html.twig', [
            'cities' => $cityRepository->findByPage($page, $items, $search, $sort, $order),
            'new_city' => $newCity,
            'form' => $form->createView(),
            'queryParams' => $request->query->all(),
            'pagination' => [
                'page' => $page,
                'pages' => $pages,
                'pageItems' => $items,
                'totalItems' => $totalItems
            ]
        ]);
    }

    /**
     * @Route("/city/new", name="new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class, $city);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugger = New Slugger();
            /** @var EntityManagerInterface $entityManager */
            $entityManager = $this->getDoctrine()->getManager();
            /** @var User $user */
            $user = $this->getUser();

            $slugger->sluggifyEntity($entityManager, $city, 'name');
            $entityManager->persist($city);
            $entityManager->flush();

            return $this->redirectToRoute('city_index');
        }

        return $this->render('city/new.html.twig', [
            'city' => $city,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/city/{slug}", name="show", methods={"GET"})
     * @param City $city
     * @return Response
     */
    public function show(City $city): Response
    {
        return $this->render('city/show.html.twig', [
            'city' => $city,
        ]);
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

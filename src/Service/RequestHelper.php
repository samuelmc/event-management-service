<?php


namespace App\Service;


use App\Repository\CountableRepositoryInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\CompiledRoute;
use Symfony\Component\Routing\Route;

class RequestHelper
{

    public function listRequestData(Request &$request, Response &$response, string $listName = null): array
    {
        $itemsPerPage = $request->query->get('items');

        if ($itemsPerPage === null)
            $itemsPerPage = $request->cookies->get('itemsPerPage', 10);

        else {
            if (!$request->cookies->has('itemsPerPage') || $request->cookies->get('itemsPerPage') != $itemsPerPage)
                $response->headers->setCookie(Cookie::create('itemsPerPage', $itemsPerPage));
                $request->cookies->set('itemsPerPage', $itemsPerPage);

            $request->query->remove('items');
        }

        $defaultSorting = ['sort' => 'createdAt', 'order' => 'desc'];
        $rememberedSorting = $listName !== null ? json_decode($request->cookies->get("{$listName}Sorting", json_encode($defaultSorting)), true): $defaultSorting;

        return [
            $request->query->get('search', ''),
            $request->query->get('page', 1),
            $itemsPerPage,
            $request->query->get('sort', $rememberedSorting['sort']),
            $request->query->get('order', $rememberedSorting['order']),
        ];
    }

    public function rememberSortingSelection(Request &$request, Response &$response, string $listName): void
    {
        $sort = $request->query->get('sort');
        if ($sort !== null) {
            $order = $request->query->get('order', 'desc');
            $response->headers->setCookie(Cookie::create("{$listName}Sorting", json_encode(['sort'=> $sort, 'order' => $order])));
            $request->query->remove('sort');
            $request->query->remove('order');
        }
    }

    public function getPaginationParams(Request $request, Response &$response, CountableRepositoryInterface $repository): array
    {
        list($search, $page, $items) = $this->listRequestData($request, $response);
        $totalItems = call_user_func($repository->countMethod(), $search);
        $pages = $totalItems > 0 ? ceil($totalItems/$items) : 1;

        return [
            'page' => $page,
            'pages' => $pages,
            'pageItems' => $items,
            'totalItems' => $totalItems,
            'from' => ($items*($page-1)),
            'to' => ($items*$page) < $totalItems ? ($items*$page) : $totalItems
        ];
    }

}
<?php

namespace VeeZions\BuilderEngine\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;
use VeeZions\BuilderEngine\Form\LibraryType;
use VeeZions\BuilderEngine\Manager\AssetManager;
use VeeZions\BuilderEngine\Manager\EngineManager;
use VeeZions\BuilderEngine\Constant\AssetConstant;

class LibraryController
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EngineManager $engineManager,
        private AssetManager $assetManager,
    ) {
    }

    public function index(): Response
    {
        return $this->engineManager->new(LibraryType::class, [
            'data' => $this->entityManager->getRepository(BuilderLibrary::class)->paginate(),
            'search_form' => $this->engineManager->createLibrarySearchForm()
        ]);
    }
    
    public function list(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        $order = $request->request->get('media_search_order');
        $types = $request->request->getIterator()->getArrayCopy()['media_search_types'] ?? [];
        $search = strlen($request->request->get('media_search')) > 0 
            ? $request->request->get('media_search') 
            : null;
        
        return new JsonResponse(['html' => $this->engineManager->renderMediaList($order, $types, $search)]);
    }
    
    public function save(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        if (!$request->request->has('id') || !$request->request->has('title')) {
            throw new BadRequestHttpException('Something went wrong...');
        }

        $id = $request->request->getInt('id');
        $title = $request->request->getString('title');
        $legend = $request->request->has('legend') ? $request->request->getString('legend') : null;

        $library = $this->entityManager->getRepository(BuilderLibrary::class)->find($id);
        if (null !== $library) {
            $library->setTitle($title)->setLegend($legend);
            $this->entityManager->flush();
        }

        $order = $request->request->get('media_search_order');
        $types = $request->request->getIterator()->getArrayCopy()['media_search_types'] ?? [];
        $search = strlen($request->request->get('media_search')) > 0
            ? $request->request->get('media_search')
            : null;

        return new JsonResponse(['html' => $this->engineManager->renderMediaList($order, $types, $search)]);
    }
    
    public function delete(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        if (!$request->request->has('ids')) {
            throw new AccessDeniedException();
        }

        $ids = json_decode($request->request->getString('ids'), true);
        $ids = is_array($ids) ? $ids : [];

        if (!empty($ids)) {
            $libraries = $this->entityManager->getRepository(BuilderLibrary::class)->getSelectedMedia($ids);
            foreach ($libraries as $media) {
                $this->assetManager->delete($media->getUrl());
            }
        }

        $order = $request->request->get('media_search_order');
        $types = $request->request->getIterator()->getArrayCopy()['media_search_types'] ?? [];
        $search = strlen($request->request->get('media_search')) > 0
            ? $request->request->get('media_search')
            : null;

        return new JsonResponse(['html' => $this->engineManager->renderMediaList($order, $types, $search)]);
    }
    
    public function modal(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        if (!$request->request->has('id')) {
            throw new BadRequestHttpException('Something went wrong...');
        }

        $id = $request->request->getInt('id');
        return new JsonResponse(['html' => $this->engineManager->renderModal($id)]);
    }

    public function more(Request $request): JsonResponse
    {
        if (!$request->isXmlHttpRequest()) {
            throw new AccessDeniedException();
        }

        if (!$request->request->has('count')) {
            throw new BadRequestHttpException('Something went wrong...');
        }

        $order = $request->request->get('media_search_order');
        $types = $request->request->getIterator()->getArrayCopy()['media_search_types'] ?? [];
        $search = strlen($request->request->get('media_search')) > 0
            ? $request->request->get('media_search')
            : null;

        $count = $request->request->getInt('count', AssetConstant::ITEMS_PER_LOAD);
        return new JsonResponse(['html' => $this->engineManager->renderMediaList($order, $types, $search, $count)]);
    }
}

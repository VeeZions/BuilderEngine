<?php

namespace XenoLabs\XenoEngine\Manager;

use XenoLabs\XenoEngine\Entity\Element;
use XenoLabs\XenoEngine\Entity\Ged as GedEntity;
use XenoLabs\XenoEngine\Entity\Page;
use XenoLabs\XenoEngine\Repository\ElementRepository;
use XenoLabs\XenoEngine\Repository\GedRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

readonly class GedManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private GedRepository $gedRepository,
        private ElementRepository $elementRepository,
        private TranslatorInterface $translator,
    ) {
    }

    public function setGedsFromPageBuilder(Page $page): void
    {
        $content = $page->getContent();
        $this->removeAllElementGeds($page);
        $elements = [];
        if (is_array($content)) {
            $elements = $this->loopOnStagesForGeds($content, $elements, $page);
        }

        foreach ($elements as $element) {
            $p = $element['page'];
            $g = $element['bgImage'];
            $bId = $element['builderId'];
            $t = $element['type'];
            if ($p instanceof Page
                && is_string($bId)
                && is_string($t)
            ) {
                $el = new Element();
                $el->setPage($p)
                    ->setBuilderId($bId)
                    ->setType($t)
                ;
                if ($g instanceof GedEntity) {
                    $el->setBgImage($g);
                    $el->addMedium($g);
                }
                if (isset($element['moduleType']) && is_string($element['moduleType'])) {
                    $el->setModuleType($element['moduleType']);
                }
                if (is_iterable($element['media'])) {
                    foreach ($element['media'] as $media) {
                        if ($media instanceof GedEntity) {
                            $el->addMedium($media);
                        }
                    }
                }
                $this->entityManager->persist($el);
            }
        }

        $this->entityManager->flush();
    }

    /**
     * @param array<int, mixed>                $content
     * @param array<int, array<string, mixed>> $elements
     *
     * @return array<int, array<string, mixed>>
     */
    private function loopOnStagesForGeds(array $content, array $elements, Page $page): array
    {
        foreach ($content as $row) {
            if ((is_array($row) && isset($row['bg']['image']) && is_int($row['bg']['image']))
                || (is_array($row) && isset($row['data']['ged']) && is_int($row['data']['ged']))
            ) {
                $el = [
                    'page' => $page,
                    'builderId' => $row['id'],
                    'type' => $row['type'],
                    'bgImage' => isset($row['bg']['image']) ? $this->gedRepository->find($row['bg']['image']) : null,
                ];
                $el['media'] = match (true) {
                    isset($row['data']['ged']) && is_int($row['data']['ged']) => [$this->gedRepository->find($row['data']['ged'])],
                    isset($row['data']['slider']) && is_array($row['data']['slider']) => array_map(function ($item) {
                        return $this->gedRepository->find($item['ged']);
                    }, $row['data']['slider']),
                    isset($row['data']['geds']) && is_array($row['data']['geds']) => array_map(function ($item) {
                        return $this->gedRepository->find($item['ged']);
                    }, $row['data']['geds']),
                    default => [],
                };
                if ('module' === $row['type']) {
                    $el['moduleType'] = $row['contentType'];
                }
                $elements[] = $el;
            }
            if (is_array($row) && !empty($row['children'])) {
                $elements = $this->loopOnStagesForGeds($row['children'], $elements, $page);
            }
        }

        return $elements;
    }

    public function removeAllElementGeds(Page $page): void
    {
        $elements = $this->elementRepository->findBy(['page' => $page]);
        foreach ($elements as $element) {
            $geds = $element->getMedia();
            foreach ($geds as $ged) {
                $element->removeMedium($ged);
            }
            $this->entityManager->remove($element);
        }
        $this->entityManager->flush();
    }

    public function getElementHumanizedRanking(Element $element): string
    {
        if (null === $element->getBuilderId() || null === $element->getType()) {
            return '';
        }

        $label = 'module' !== $element->getType()
            ? $this->translator->trans('crud.label.builder.header.'.$element->getType(), [], 'crud')
            : $this->translator->trans('crud.label.builder.header.module.'.$element->getModuleType(), [], 'crud');

        $id = str_replace($element->getType(), '', $element->getBuilderId());

        $rank = match (true) {
            'module' === $element->getType() => $this->setModuleRanking($id),
            default => ' n°'.$id,
        };

        return $label.$rank;
    }

    private function setModuleRanking(string $id): string
    {
        $array = explode('-', $id);
        $details = [];
        $details[] = $this->translator->trans('crud.label.builder.header.row', [], 'crud').' '.$array[0];
        $details[] = $this->translator->trans('crud.label.builder.header.section', [], 'crud').' '.$array[1];
        $details[] = $this->translator->trans('crud.label.builder.header.block', [], 'crud').' '.$array[2];

        return ' n°'.end($array).'('.implode(', ', $details).')';
    }
}

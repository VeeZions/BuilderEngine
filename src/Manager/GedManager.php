<?php

namespace VeeZions\BuilderEngine\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use VeeZions\BuilderEngine\Entity\BuilderElement;
use VeeZions\BuilderEngine\Entity\BuilderLibrary;
use VeeZions\BuilderEngine\Entity\BuilderPage;

readonly class GedManager
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TranslatorInterface $translator,
    ) {
    }

    public function setGedsFromPageBuilder(BuilderPage $page): void
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
            if ($p instanceof BuilderPage
                && is_string($bId)
                && is_string($t)
            ) {
                $el = new BuilderElement();
                $el->setPage($p)
                    ->setBuilderId($bId)
                    ->setType($t)
                ;
                if ($g instanceof BuilderLibrary) {
                    $el->setBgImage($g);
                    $el->addMedium($g);
                }
                if (isset($element['moduleType']) && is_string($element['moduleType'])) {
                    $el->setModuleType($element['moduleType']);
                }
                if (is_iterable($element['media'])) {
                    foreach ($element['media'] as $media) {
                        if ($media instanceof BuilderLibrary) {
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
    private function loopOnStagesForGeds(array $content, array $elements, BuilderPage $page): array
    {
        foreach ($content as $row) {
            if ((is_array($row) && isset($row['bg']['image']) && is_int($row['bg']['image']))
                || (is_array($row) && isset($row['data']['library']) && is_int($row['data']['library']))
            ) {
                $el = [
                    'page' => $page,
                    'builderId' => $row['id'],
                    'type' => $row['type'],
                    'bgImage' => isset($row['bg']['image']) ? $this->entityManager->getRepository(BuilderLibrary::class)->find($row['bg']['image']) : null,
                ];
                $el['media'] = match (true) {
                    isset($row['data']['library']) && is_int($row['data']['library']) => [$this->entityManager->getRepository(BuilderLibrary::class)->find($row['data']['library'])],
                    isset($row['data']['slider']) && is_array($row['data']['slider']) => array_map(function ($item) {
                        return $this->entityManager->getRepository(BuilderLibrary::class)->find($item['library']);
                    }, $row['data']['slider']),
                    isset($row['data']['libraries']) && is_array($row['data']['libraries']) => array_map(function ($item) {
                        return $this->entityManager->getRepository(BuilderLibrary::class)->find($item['library']);
                    }, $row['data']['libraries']),
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

    public function removeAllElementGeds(BuilderPage $page): void
    {
        $elements = $this->entityManager->getRepository(BuilderElement::class)->findBy(['page' => $page]);
        foreach ($elements as $element) {
            $geds = $element->getMedia();
            foreach ($geds as $ged) {
                $element->removeMedium($ged);
            }
            $this->entityManager->remove($element);
        }
        $this->entityManager->flush();
    }

    public function getElementHumanizedRanking(BuilderElement $element): string
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

<?php

namespace VeeZions\BuilderEngine\Provider;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Intl\Locales;

class LocaleProvider
{
    /**
     * @return array<string, array<string, string>>
     */
    public function getList(?string $choicedLocale = null): array
    {
        $filesystem = new Filesystem();
        $path = __DIR__.'/../../assets/libraries/compiled_locales.json';
        if (!$filesystem->exists($path)) {
            $this->generateList($choicedLocale);
        }

        $content = file_get_contents($path);
        if (!is_string($content)) {
            return [];
        }

        $results = json_decode($content, true);
        if (!is_array($results)) {
            return [];
        }

        return $results;
    }

    /**
     * @return array<int, string>
     */
    public function getProviderList(?string $choicedLocale = null): array
    {
        $filesystem = new Filesystem();
        $path = __DIR__.'/../../assets/libraries/compiled_locales.json';
        if (!$filesystem->exists($path)) {
            $this->generateList($choicedLocale);
        }

        $content = file_get_contents($path);
        if (!is_string($content)) {
            return [];
        }

        $results = json_decode($content, true);
        if (!is_array($results)) {
            return [];
        }

        $list = [];
        foreach ($results as $l => $n) {
            $list[$l] = $n['language'];
        }

        return $list;
    }

    private function generateList(?string $choicedLocale): void
    {
        $jsonFile = __DIR__.'/../../assets/libraries/locales.json';
        $content = file_get_contents($jsonFile);
        if (!is_string($content)) {
            return;
        }

        $json = json_decode($content, true);
        if (!is_array($json)) {
            return;
        }

        $locales = [];
        foreach (Locales::getNames($choicedLocale) as $locale => $language) {
            $reference = str_replace('_', '-', $locale);
            $split = explode('-', $reference);
            $locales[$reference] = [
                'initial' => $split[0],
                'alpha2' => end($split),
                'language' => ucfirst($language),
                'locale' => $reference,
            ];
        }

        $jsonData = [];
        foreach ($json as $c) {
            $jsonData[$c['locale']] = [
                'flag' => $c['country']['flag'],
                'country' => ucfirst($c['country']['name']),
                'language' => $c['language']['iso_639_1'],
                'code' => $c['country']['code'],
                'locale' => str_replace('-', '_', $c['locale']),
            ];
        }

        ksort($locales);
        ksort($jsonData);

        $results = [];
        $initials = [];
        $alpha2 = [];

        foreach ($locales as $locale => $data) {
            if (isset($jsonData[$locale])) {
                $ref = $jsonData[$locale];
                $ref['language'] = $data['language'];
                $results[str_replace('-', '_', $locale)] = $ref;
                unset($jsonData[$locale]);
                unset($locales[$locale]);
            } else {
                $data['locale'] = $locale;
                $initials[$data['initial']] = $data;
                $alpha2[$data['alpha2']] = $data;
            }
        }

        foreach ($jsonData as $loc => $countryData) {
            if (isset($initials[$countryData['language']])
                && !isset($results[$initials[$countryData['language']]['locale']])) {
                $results[$initials[$countryData['language']]['locale']] = [
                    'flag' => $countryData['flag'],
                    'country' => $countryData['country'],
                    'language' => $initials[$countryData['language']]['language'],
                    'code' => $countryData['code'],
                    'locale' => $initials[$countryData['language']]['locale'],
                ];
                unset($jsonData[$loc]);
                unset($locales[$initials[$countryData['language']]['locale']]);
                unset($initials[$countryData['language']]);
            }
        }

        foreach ($jsonData as $loc => $countryData) {
            if (isset($alpha2[$countryData['code']])
                && !isset($results[$alpha2[$countryData['code']]['locale']])) {
                $results[str_replace('-', '_', $loc)] = [
                    'flag' => $countryData['flag'],
                    'country' => $countryData['country'],
                    'language' => $alpha2[$countryData['code']]['language'],
                    'code' => $countryData['code'],
                    'locale' => str_replace('-', '_', $loc),
                ];
                unset($jsonData[$loc]);
                unset($locales[$alpha2[$countryData['code']]['locale']]);
                unset($alpha2[$countryData['code']]);
            }
        }

        ksort($results);

        $filesystem = new Filesystem();
        $path = __DIR__.'/../../assets/libraries/compiled_locales.json';

        $content = json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (!is_string($content)) {
            return;
        }
        $filesystem->dumpFile($path, $content);
    }
}

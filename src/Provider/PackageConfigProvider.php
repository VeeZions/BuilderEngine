<?php

namespace VeeZions\BuilderEngine\Provider;

use League\Flysystem\Filesystem as OneupFlysystem;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Exception\InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;
use VeeZions\BuilderEngine\Constant\AssetConstant;
use VeeZions\BuilderEngine\Constant\ConfigConstant;

class PackageConfigProvider
{

    public static function getConfigFileFromFileName(ContainerBuilder $container, string $fileName): array
    {
        $filesystem = new Filesystem();
        $configFile = $container
            ->getParameterBag()
            ->resolveValue('%kernel.project_dir%/config/packages/'.$fileName.'.yaml');

        if ($filesystem->exists($configFile)) {
            $whenAtEnv = 'when@'.$container->getParameter('kernel.environment');
            $yaml = Yaml::parseFile($configFile);
            return [$fileName => $yaml[$whenAtEnv][$fileName] ?? $yaml[$fileName]];
        }

        return [];
    }

    public static function isLocaleDriver(array $config, array $liipConfig, array $oneupConfig): bool
    {
        return
            (!isset($config[ConfigConstant::CONFIG_FILE_NAME]['library_config']['driver'])
                || (isset($config[ConfigConstant::CONFIG_FILE_NAME]['library_config']['driver'])
                    && $config[ConfigConstant::CONFIG_FILE_NAME]['library_config']['driver'] === ConfigConstant::CONFIG_DEFAULT_DRIVER)
            )
            && !isset(
                $liipConfig[ConfigConstant::CONFIG_LIIP_FILE_NAME]['loaders']['vbe_system_loader'],
                $liipConfig[ConfigConstant::CONFIG_LIIP_FILE_NAME]['resolvers']['vbe_system_resolver'],
                $oneupConfig[ConfigConstant::CONFIG_ONEUP_FILE_NAME]['adapters']['vbe_uploads_adapter'],
                $oneupConfig[ConfigConstant::CONFIG_ONEUP_FILE_NAME]['filesystems']['vbe_uploads']
            );
    }

    public static function isS3Driver(array $config, array $liipConfig, array $oneupConfig): bool
    {
        return
            isset($config[ConfigConstant::CONFIG_FILE_NAME]['library_config']['driver'])
            && $config[ConfigConstant::CONFIG_FILE_NAME]['library_config']['driver'] === ConfigConstant::CONFIG_S3_DRIVER
            && !isset(
                $liipConfig[ConfigConstant::CONFIG_LIIP_FILE_NAME]['loaders']['vbe_system_loader'],
                $liipConfig[ConfigConstant::CONFIG_LIIP_FILE_NAME]['resolvers']['vbe_system_resolver'],
                $oneupConfig[ConfigConstant::CONFIG_ONEUP_FILE_NAME]['adapters']['vbe_uploads_adapter'],
                $oneupConfig[ConfigConstant::CONFIG_ONEUP_FILE_NAME]['filesystems']['vbe_+uploads']);
    }
    
    private static function setLiipImagineFiltersSets(): array
    {
        return [
            'filter_sets' => [
                AssetConstant::FILTER_THUMBNAIL => [
                    'filters' => [
                        'thumbnail' => [
                            'size' => [200, 200],
                            'mode' => 'outbound',
                            'allow_upscale' => true,
                        ]
                    ]
                ],
                AssetConstant::FILTER_LOW_QUALITY => [
                    'quality' => 10,
                ]
            ]
        ];
    }
    
    public static function setLiipImagineLocaleConfiguration(array $liipConfig, array $config): array
    {
        $data = [];
        if (!isset($liipConfig[ConfigConstant::CONFIG_LIIP_FILE_NAME]['twig']['mode'])) {
            $data['twig']['mode'] = 'lazy';
        }

        $data['loaders'] = [
            'vbe_system_loader' => [
                'flysystem' => [
                    'filesystem_service' => 'oneup_flysystem.vbe_uploads_filesystem'
                ]
            ]
        ];

        $data['resolvers'] = [
            'vbe_system_resolver' => [
                'flysystem' => [
                    'filesystem_service' => 'oneup_flysystem.vbe_uploads_filesystem',
                    'cache_prefix' => 'uploads/filters',
                    'root_url' => $config['library_config']['s3_cdn_url'] ?? '/',
                    'visibility' => 'public'
                ]
            ]
        ];

        $data['data_loader'] = 'vbe_system_loader';

        $data['cache'] = 'vbe_system_resolver';

        return array_merge($data, self::setLiipImagineFiltersSets());
    }

    public static function setLiipImagineS3Configuration(array $liipConfig, array $config): array
    {
        $data = [];
        if (!isset($liipConfig[ConfigConstant::CONFIG_LIIP_FILE_NAME]['twig']['mode'])) {
            $data['twig']['mode'] = 'lazy';
        }

        $data['loaders'] = [
            'vbe_system_loader' => [
                'flysystem' => [
                    'filesystem_service' => 'oneup_flysystem.vbe_uploads_filesystem'
                ]
            ]
        ];

        $data['resolvers'] = [
            'vbe_system_resolver' => [
                'flysystem' => [
                    'filesystem_service' => 'oneup_flysystem.vbe_uploads_filesystem',
                    'cache_prefix' => 'uploads/filters',
                    'root_url' => '/',
                    'visibility' => 'public',
                ]
            ]
        ];

        $data['data_loader'] = 'vbe_system_loader';

        $data['cache'] = 'vbe_system_resolver';

        return array_merge($data, self::setLiipImagineFiltersSets());
    }

    public static function setOneupFlysystemLocaleConfiguration(): array
    {
        return [
            'adapters' => [
                'vbe_uploads_adapter' => [
                    'local' => [
                        'location' => '%kernel.project_dir%/public',
                        'permissions' => [
                            'dir' => [
                                'public' => 0755,
                                'private' => 0700
                            ],
                            'file' => [
                                'public' => 0644,
                                'private' => 0600
                            ]
                        ]
                    ]
                ]
            ],
            'filesystems' => [
                'vbe_uploads' => [
                    'adapter' => 'vbe_uploads_adapter',
                    'alias' => OneupFlysystem::class,
                ]
            ]
        ];
    }

    public static function setOneupFlysystemS3Configuration(array $config): array
    {
        if (null === $config['library_config']['service']) {
            throw new InvalidArgumentException('You must provide a service name for the S3 client.');
        }

        if (null === $config['library_config']['s3_bucket']) {
            throw new InvalidArgumentException('You must provide a bucket name for the S3 client.');
        }

        return [
            'adapters' => [
                'vbe_uploads_adapter' => [
                    'awss3v3' => [
                        'client' => $config['library_config']['service'],
                        'bucket' => $config['library_config']['s3_bucket'],
                    ]
                ]
            ],
            'filesystems' => [
                'vbe_uploads' => [
                    'adapter' => 'vbe_uploads_adapter',
                    'alias' => 'vbe_uploads_filesystem',
                ]
            ]
        ];
    }
}

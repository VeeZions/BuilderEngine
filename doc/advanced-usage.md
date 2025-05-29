# Tests

### phpstan:
```shell
php vendor/bin/phpstan analyze -c phpstan.dist.neon --memory-limit 1G
```

### CS Fixer
```shell
PHP_CS_FIXER_IGNORE_ENV=1 vendor/bin/php-cs-fixer fix vendor/veezions/builder-engine-bundle/src
```
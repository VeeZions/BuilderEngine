# Xeno Engine

This package is a [Symfony](http://symfony.com) Bundle allowing you to use particular **FormTypes** in order to build ***complete frontend pages*** with ***categories***, ***articles*** and ***navigation***, including ***GED***, in the context of a ***CMS***.

[![Package version](https://img.shields.io/packagist/v/xenolab/xeno-engine-bundle.svg?style=flat-square)](https://packagist.org/packages/xenolab/xeno-engine-bundle)
[![license](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE)
[![Slack](https://img.shields.io/badge/slack-%23xeno--engine--bundle-gold.svg?style=flat-square)](https://join.slack.com/t/sensioxenolab/shared_invite/zt-2j1r521bb-njCE7vP1vT9Ujcwfguyw4w)

## When to use this bundle ?

A public web or shop site to mount in your Symfony/PHP stack? A need to quickly increase skills on a flexible and powerful tool that meets the needs of the state of the art of website design? Then you will surely need the XenoEngineBundle. You will have at your disposal an efficient page builder that can work with the most well-known CSS libraries, a connection to Stimulus, a range of modules and the ability to customize them or create new ones. The ability to manage a blog, as well as the integration of an EDM (local or remote like Amazon S3).

## Documentation

1. [Install](#installation)
2. [Basic usage](#basic-usage)
3. [Advanced usage](doc/advanced-usage.md)
4. [Resources](#resources)

## Installation

With [Symfony Flex](https://symfony.com/doc/current/setup/flex.html) (recommended):

```
composer require xenolab/xeno-engine-bundle
```

You're ready to use XenoEngineBundle, and can jump to the next section!

Without Flex, you will have to register the bundle accordingly in `config/bundles.php`:

```php
<?php

return [
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    // ...
    NeoPageBuilder\XenoEngineBundle::class => ['all' => true],
];
```

Configure the bundle to your needs, for example:

## Basic usage

## Database testing

## Resources

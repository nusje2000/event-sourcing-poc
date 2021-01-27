<?php

declare(strict_types=1);

namespace App\Component\Twig;

use ReflectionClass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

final class Extension extends AbstractExtension
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    public function __construct(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('class_source_url', [$this, 'classSource']),
        ];
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('class_name', [$this, 'className']),
            new TwigFilter('short_class_name', [$this, 'shortClassName']),
        ];
    }

    public function className(object $class): string
    {
        return get_class($class);
    }

    public function shortClassName(object $class): string
    {
        return (new ReflectionClass($class))->getShortName();
    }

    /**
     * @param class-string|object $className
     */
    public function classSource($className): string
    {
        $fs = new Filesystem();
        $ref = new ReflectionClass($className);
        $file = $ref->getFileName() ?? '';
        $file = $fs->makePathRelative($file, dirname(__DIR__, 3));
        $file = rtrim($file, '/');

        return $this->urlGenerator->generate('_profiler_open_file', [
            'file' => $file,
            'line' => $ref->getStartLine(),
        ]);
    }
}

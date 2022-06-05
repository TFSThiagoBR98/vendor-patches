<?php

declare(strict_types=1);

use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\PackageBuilder\Composer\VendorDirProvider;
use Symplify\PackageBuilder\Yaml\ParametersMerger;
use Symplify\SmartFileSystem\Json\JsonFileSystem;
use Symplify\VendorPatches\Console\VendorPatchesApplication;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator->services();

    $services->defaults()
        ->public()
        ->autowire();

    $services->load('Symplify\VendorPatches\\', __DIR__ . '/../src')
        ->exclude([__DIR__ . '/../src/Kernel', __DIR__ . '/../src/ValueObject']);

    $services->set(UnifiedDiffOutputBuilder::class)
        ->args([
            '$addLineNumbers' => true,
        ]);

    $services->set(Differ::class)
        ->args([
            '$outputBuilder' => service(UnifiedDiffOutputBuilder::class),
        ]);

    $services->set(VendorDirProvider::class);
    $services->set(JsonFileSystem::class);

    // for autowired commands
    $services->alias(Application::class, VendorPatchesApplication::class);

    $services->set(ParametersMerger::class);
};

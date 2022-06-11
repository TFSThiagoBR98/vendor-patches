<?php

declare (strict_types=1);
namespace VendorPatches20220611;

use VendorPatches20220611\SebastianBergmann\Diff\Differ;
use VendorPatches20220611\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use VendorPatches20220611\Symplify\PackageBuilder\Console\Formatter\ColorConsoleDiffFormatter;
use VendorPatches20220611\Symplify\PackageBuilder\Console\Output\ConsoleDiffer;
use VendorPatches20220611\Symplify\PackageBuilder\Diff\Output\CompleteUnifiedDiffOutputBuilderFactory;
use VendorPatches20220611\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
return static function (ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire();
    $services->set(ColorConsoleDiffFormatter::class);
    $services->set(ConsoleDiffer::class);
    $services->set(CompleteUnifiedDiffOutputBuilderFactory::class);
    $services->set(Differ::class);
    $services->set(PrivatesAccessor::class);
};

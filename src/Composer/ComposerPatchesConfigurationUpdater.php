<?php

declare (strict_types=1);
namespace Symplify\VendorPatches\Composer;

use VendorPatches20220611\Symplify\Astral\Exception\ShouldNotHappenException;
use VendorPatches20220611\Symplify\ComposerJsonManipulator\ComposerJsonFactory;
use VendorPatches20220611\Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager;
use VendorPatches20220611\Symplify\ComposerJsonManipulator\ValueObject\ComposerJson;
use VendorPatches20220611\Symplify\PackageBuilder\Yaml\ParametersMerger;
use VendorPatches20220611\Symplify\SmartFileSystem\SmartFileInfo;
/**
 * @see \Symplify\VendorPatches\Tests\Composer\ComposerPatchesConfigurationUpdater\ComposerPatchesConfigurationUpdaterTest
 */
final class ComposerPatchesConfigurationUpdater
{
    /**
     * @var \Symplify\ComposerJsonManipulator\ComposerJsonFactory
     */
    private $composerJsonFactory;
    /**
     * @var \Symplify\ComposerJsonManipulator\FileSystem\JsonFileManager
     */
    private $jsonFileManager;
    /**
     * @var \Symplify\PackageBuilder\Yaml\ParametersMerger
     */
    private $parametersMerger;
    public function __construct(ComposerJsonFactory $composerJsonFactory, JsonFileManager $jsonFileManager, ParametersMerger $parametersMerger)
    {
        $this->composerJsonFactory = $composerJsonFactory;
        $this->jsonFileManager = $jsonFileManager;
        $this->parametersMerger = $parametersMerger;
    }
    /**
     * @param mixed[] $composerExtraPatches
     */
    public function updateComposerJson(string $composerJsonFilePath, array $composerExtraPatches) : ComposerJson
    {
        $extra = ['patches' => $composerExtraPatches];
        $composerJson = $this->composerJsonFactory->createFromFilePath($composerJsonFilePath);
        // merge "extra" section - deep merge is needed, so original patches are included
        $newExtra = $this->parametersMerger->merge($composerJson->getExtra(), $extra);
        $composerJson->setExtra($newExtra);
        return $composerJson;
    }
    /**
     * @param mixed[] $composerExtraPatches
     */
    public function updateComposerJsonAndPrint(string $composerJsonFilePath, array $composerExtraPatches) : void
    {
        $composerJson = $this->updateComposerJson($composerJsonFilePath, $composerExtraPatches);
        $fileInfo = $composerJson->getFileInfo();
        if (!$fileInfo instanceof SmartFileInfo) {
            throw new ShouldNotHappenException();
        }
        $this->jsonFileManager->printComposerJsonToFilePath($composerJson, $fileInfo->getRealPath());
    }
}

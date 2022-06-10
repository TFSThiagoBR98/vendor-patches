<?php

declare (strict_types=1);
namespace VendorPatches20220610\Symplify\AutowireArrayParameter\Skipper;

use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use VendorPatches20220610\Symfony\Component\DependencyInjection\Definition;
use VendorPatches20220610\Symplify\AutowireArrayParameter\TypeResolver\ParameterTypeResolver;
final class ParameterSkipper
{
    /**
     * Classes that create circular dependencies
     *
     * @var string[]
     */
    private const DEFAULT_EXCLUDED_FATAL_CLASSES = ['VendorPatches20220610\\Symfony\\Component\\Form\\FormExtensionInterface', 'VendorPatches20220610\\Symfony\\Component\\Asset\\PackageInterface', 'VendorPatches20220610\\Symfony\\Component\\Config\\Loader\\LoaderInterface', 'VendorPatches20220610\\Symfony\\Component\\VarDumper\\Dumper\\ContextProvider\\ContextProviderInterface', 'VendorPatches20220610\\EasyCorp\\Bundle\\EasyAdminBundle\\Form\\Type\\Configurator\\TypeConfiguratorInterface', 'VendorPatches20220610\\Sonata\\CoreBundle\\Model\\Adapter\\AdapterInterface', 'VendorPatches20220610\\Sonata\\Doctrine\\Adapter\\AdapterChain', 'VendorPatches20220610\\Sonata\\Twig\\Extension\\TemplateExtension'];
    /**
     * @var string[]
     */
    private $excludedFatalClasses = [];
    /**
     * @var \Symplify\AutowireArrayParameter\TypeResolver\ParameterTypeResolver
     */
    private $parameterTypeResolver;
    /**
     * @param string[] $excludedFatalClasses
     */
    public function __construct(ParameterTypeResolver $parameterTypeResolver, array $excludedFatalClasses)
    {
        $this->parameterTypeResolver = $parameterTypeResolver;
        $this->excludedFatalClasses = \array_merge(self::DEFAULT_EXCLUDED_FATAL_CLASSES, $excludedFatalClasses);
    }
    public function shouldSkipParameter(ReflectionMethod $reflectionMethod, Definition $definition, ReflectionParameter $reflectionParameter) : bool
    {
        if (!$this->isArrayType($reflectionParameter)) {
            return \true;
        }
        // already set
        $argumentName = '$' . $reflectionParameter->getName();
        if (isset($definition->getArguments()[$argumentName])) {
            return \true;
        }
        $parameterType = $this->parameterTypeResolver->resolveParameterType($reflectionParameter->getName(), $reflectionMethod);
        if ($parameterType === null) {
            return \true;
        }
        if (\in_array($parameterType, $this->excludedFatalClasses, \true)) {
            return \true;
        }
        if (!\class_exists($parameterType) && !\interface_exists($parameterType)) {
            return \true;
        }
        // prevent circular dependency
        if ($definition->getClass() === null) {
            return \false;
        }
        return \is_a($definition->getClass(), $parameterType, \true);
    }
    private function isArrayType(ReflectionParameter $reflectionParameter) : bool
    {
        if ($reflectionParameter->getType() === null) {
            return \false;
        }
        $reflectionParameterType = $reflectionParameter->getType();
        if (!$reflectionParameterType instanceof ReflectionNamedType) {
            return \false;
        }
        return $reflectionParameterType->getName() === 'array';
    }
}

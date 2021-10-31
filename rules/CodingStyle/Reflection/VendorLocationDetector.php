<?php

declare(strict_types=1);

namespace Rector\CodingStyle\Reflection;

use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\ReflectionWithFilename;
use Symplify\SmartFileSystem\Normalizer\PathNormalizer;

final class VendorLocationDetector
{
    public function __construct(
        private PathNormalizer $pathNormalizer,
    ) {
    }

    public function detectFunctionLikeReflection(
        ReflectionWithFilename | MethodReflection | FunctionReflection $reflection
    ): bool {
        $fileName = $this->resolveReflectionFileName($reflection);

        // probably internal
        if ($fileName === null) {
            return false;
        }

        $normalizedFileName = $this->pathNormalizer->normalizePath($fileName);
        return str_contains($normalizedFileName, '/vendor/');
    }

    private function resolveReflectionFileName(
        MethodReflection | ReflectionWithFilename | FunctionReflection $reflection
    ): ?string {
        if ($reflection instanceof ReflectionWithFilename) {
            return $reflection->getFileName();
        }

        if ($reflection instanceof FunctionReflection) {
            return $reflection->getFileName();
        }

        $declaringClassReflection = $reflection->getDeclaringClass();
        return $declaringClassReflection->getFileName();
    }
}

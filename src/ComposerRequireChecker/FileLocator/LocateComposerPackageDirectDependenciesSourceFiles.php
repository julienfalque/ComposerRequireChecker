<?php

namespace ComposerRequireChecker\FileLocator;

use Generator;

final class LocateComposerPackageDirectDependenciesSourceFiles
{
    public function __invoke(string $composerJsonPath): Generator
    {
        $packageDir = dirname($composerJsonPath);

        $composerJson = json_decode(file_get_contents($composerJsonPath), true);
        $configVendorDir = $composerJson['config']['vendor-dir'] ?? 'vendor';
        $vendorDirs = array_values(
            array_map(
                function (string $vendorName) use ($packageDir, $configVendorDir) {
                    return $packageDir . '/' . $configVendorDir . '/' . $vendorName;
                },
                array_keys($composerJson['require'] ?? [])
            )
        );

        foreach ($vendorDirs as $vendorDir) {
            if (!file_exists($vendorDir . '/composer.json')) {
                continue;
            }

            yield from (new LocateComposerPackageSourceFiles())->__invoke($vendorDir . '/composer.json');
        }
    }
}

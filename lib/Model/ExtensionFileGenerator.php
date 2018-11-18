<?php

namespace Phpactor\Extension\ExtensionManager\Model;

use Composer\EventDispatcher\ScriptExecutionException;
use Composer\Package\PackageInterface;
use Phpactor\Extension\ExtensionManager\Util\PackageFilter;

class ExtensionFileGenerator
{
    const EXTENSION_CLASS_PROPERTY = 'phpactor.extension_class';

    /**
     * @var string
     */
    private $extensionListFile;

    public function __construct(string $extensionListFile)
    {
        $this->extensionListFile = $extensionListFile;
    }

    /**
     * @param PackageInterface[] $packages
     */
    public function writeExtensionList(iterable $packages)
    {
        $packages = PackageFilter::filter($packages);

        $code = [
            '<?php',
            '// ' . date('c'),
            '// this file is autogenerated by phpactor do not edit it',
            '',
            'return ['
        ];

        foreach ($packages as $package) {
            $className = $this->classNameForPackage($package);
            $code[] = sprintf('  \\%s::class,', $this->classNameForPackage($package));
        }

        $code[] = '];';

        if (!file_exists(dirname($this->extensionListFile))) {
            mkdir(dirname($this->extensionListFile), 0777, true);
        }

        file_put_contents($this->extensionListFile, implode(PHP_EOL, $code));
    }

    private function classNameForPackage(PackageInterface $package)
    {
        $extra = $package->getExtra();

        if (!isset($extra[self::EXTENSION_CLASS_PROPERTY])) {
            throw new ScriptExecutionException(sprintf(
                'Phpactor Package "%s" has no "%s" in the extra section. This parameter must define the extensions class',
                $package->getName(),
                self::EXTENSION_CLASS_PROPERTY
            ));
        }

        return $extra[self::EXTENSION_CLASS_PROPERTY];
    }
}

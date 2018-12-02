<?php

namespace Phpactor\Extension\CodeTransform\Rpc;

use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\CodeTransform\Domain\ClassName;
use Phpactor\CodeTransform\Domain\GenerateFromExisting;
use Phpactor\CodeTransform\Domain\Generators;
use Phpactor\CodeTransform\Domain\SourceCode;
use Webmozart\Glob\Glob;

class ClassInflectHandler extends AbstractClassGenerateHandler
{
    const NAME = 'class_inflect';

    public function name(): string
    {
        return self::NAME;
    }

    protected function generate(array $arguments): SourceCode
    {
        $inflector = $this->generators->get($arguments[self::PARAM_VARIANT]);
        assert($inflector instanceof GenerateFromExisting);

        $currentClass = $this->className($arguments[self::PARAM_CURRENT_PATH]);
        $targetClass = $this->className($arguments[self::PARAM_NEW_PATH]);

        $sourceCodes = $inflector->generateFromExisting(
            $currentClass,
            $targetClass
        );

        if (count($sourceCodes) !== 1) {
            throw new \RuntimeException(sprintf(
                'Expected 1 path from class generator, got %s',
                count($sourceCodes)
            ));
        }

        return reset($sourceCodes);
    }

    public function newMessage(): string
    {
        return 'Create inflection at: ';
    }
}

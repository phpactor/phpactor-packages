<?php

namespace Phpactor\Extension\CodeTransform\Rpc;

use Phpactor\ClassFileConverter\Domain\FilePath;
use Phpactor\ClassFileConverter\Domain\FileToClass;
use Phpactor\CodeTransform\Domain\ClassName;
use Phpactor\CodeTransform\Domain\Generators;
use Phpactor\CodeTransform\Domain\SourceCode;
use Phpactor\MapResolver\Resolver;
use Phpactor\Extension\Rpc\Response\Input\TextInput;
use Phpactor\Extension\Rpc\Response\Input\ChoiceInput;
use Phpactor\Extension\Rpc\Handler\AbstractHandler;
use Phpactor\Extension\Rpc\Response\ReplaceFileSourceResponse;

abstract class AbstractClassGenerateHandler extends AbstractHandler
{
    const PARAM_CURRENT_PATH = 'current_path';
    const PARAM_NEW_PATH = 'new_path';
    const PARAM_OVERWRITE = 'overwrite';
    const PARAM_VARIANT = 'variant';

    /**
     * @var Generators
     */
    protected $generators;

    /**
     * @var FileToClass
     */
    protected $fileToClass;

    public function __construct(Generators $generators, FileToClass $fileToClass)
    {
        $this->generators = $generators;
        $this->fileToClass = $fileToClass;
    }

    public function configure(Resolver $resolver)
    {
        $resolver->setDefaults([
            self::PARAM_NEW_PATH => null,
            self::PARAM_VARIANT => null,
        ]);
        $resolver->setRequired([
            self::PARAM_CURRENT_PATH
        ]);
    }

    abstract protected function generate(array $arguments): SourceCode;

    abstract protected function newMessage(): string;

    public function handle(array $arguments)
    {
        $missingInputs = [];

        if (null === $arguments[self::PARAM_VARIANT]) {
            $this->requireInput(ChoiceInput::fromNameLabelChoicesAndDefault(
                self::PARAM_VARIANT,
                'Variant: ',
                (array) array_combine(
                    $this->generators->names(),
                    $this->generators->names()
                )
            ));
        }

        $this->requireInput(TextInput::fromNameLabelAndDefault(
            self::PARAM_NEW_PATH,
            $this->newMessage(),
            $arguments[self::PARAM_CURRENT_PATH],
            'file'
        ));

        if ($this->hasMissingArguments($arguments)) {
            return $this->createInputCallback($arguments);
        }

        $code = $this->generate($arguments);

        return ReplaceFileSourceResponse::fromPathAndSource($code->path(), (string) $code);
    }

    protected function className(string $path)
    {
        $candidates = $this->fileToClass->fileToClassCandidates(FilePath::fromString($path));
        return ClassName::fromString($candidates->best()->__toString());
    }
}

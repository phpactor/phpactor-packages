<?php

use Phpactor\ClassMover\Extension\ClassMoverExtension;
use Phpactor\Container\PhpactorContainer;
use Phpactor\Extension\ClassToFile\ClassToFileExtension;
use Phpactor\Extension\CodeTransform\CodeTransformExtension;
use Phpactor\Extension\CompletionRpc\CompletionRpcExtension;
use Phpactor\Extension\CompletionWorse\CompletionWorseExtension;
use Phpactor\Extension\Completion\CompletionExtension;
use Phpactor\Extension\ComposerAutoloader\ComposerAutoloaderExtension;
use Phpactor\Extension\Console\ConsoleExtension;
use Phpactor\Extension\Console\PhpactorCommandLoader;
use Phpactor\Extension\ExtensionManager\ExtensionManagerExtension;
use Phpactor\Extension\LanguageServerBridge\LanguageServerBridgeExtension;
use Phpactor\Extension\LanguageServerCodeTransform\LanguageServerCodeTransformExtension;
use Phpactor\Extension\LanguageServerCompletion\LanguageServerCompletionExtension;
use Phpactor\Extension\LanguageServerDiagnostics\LanguageServerDiagnosticsExtension;
use Phpactor\Extension\LanguageServerHover\LanguageServerHoverExtension;
use Phpactor\Extension\LanguageServerIndexer\LanguageServerIndexerExtension;
use Phpactor\Extension\LanguageServerReferenceFinder\LanguageServerReferenceFinderExtension;
use Phpactor\Extension\LanguageServerRename\LanguageServerRenameExtension;
use Phpactor\Extension\LanguageServerRename\LanguageServerRenameWorseExtension;
use Phpactor\Extension\LanguageServerSelectionRange\LanguageServerSelectionRangeExtension;
use Phpactor\Extension\LanguageServerSymbolProvider\LanguageServerSymbolProviderExtension;
use Phpactor\Extension\LanguageServerWorseReflection\LanguageServerWorseReflectionExtension;
use Phpactor\Extension\LanguageServer\LanguageServerExtension;
use Phpactor\Extension\Logger\LoggingExtension;
use Phpactor\Extension\Php\PhpExtension;
use Phpactor\Extension\ReferenceFinderRpc\ReferenceFinderRpcExtension;
use Phpactor\Extension\ReferenceFinder\ReferenceFinderExtension;
use Phpactor\Extension\Rpc\RpcExtension;
use Phpactor\Extension\SourceCodeFilesystem\SourceCodeFilesystemExtension;
use Phpactor\Extension\WorseReferenceFinder\WorseReferenceFinderExtension;
use Phpactor\Extension\WorseReflection\WorseReflectionExtension;
use Phpactor\FilePathResolverExtension\FilePathResolverExtension;
use Phpactor\Indexer\Extension\IndexerExtension;
use Symfony\Component\Console\Application;

require __DIR__ . '/../vendor/autoload.php';

$container = PhpactorContainer::fromExtensions([
    ClassToFileExtension::class,
    ClassMoverExtension::class,
    CodeTransformExtension::class,
    CompletionWorseExtension::class,
    CompletionExtension::class,
    CompletionRpcExtension::class,
    RpcExtension::class,
    SourceCodeFilesystemExtension::class,
    WorseReflectionExtension::class,
    FilePathResolverExtension::class,
    LoggingExtension::class,
    ComposerAutoloaderExtension::class,
    ConsoleExtension::class,
    ExtensionManagerExtension::class,
    WorseReferenceFinderExtension::class,
    ReferenceFinderRpcExtension::class,
    ReferenceFinderExtension::class,
    PhpExtension::class,
    LanguageServerExtension::class,
    LanguageServerCompletionExtension::class,
    LanguageServerReferenceFinderExtension::class,
    LanguageServerWorseReflectionExtension::class,
    LanguageServerIndexerExtension::class,
    LanguageServerHoverExtension::class,
    LanguageServerBridgeExtension::class,
    LanguageServerCodeTransformExtension::class,
    LanguageServerSymbolProviderExtension::class,
    LanguageServerSelectionRangeExtension::class,
    LanguageServerDiagnosticsExtension::class,
    LanguageServerRenameExtension::class,
    LanguageServerRenameWorseExtension::class,
    IndexerExtension::class,
], [
    'file_path_resolver.application_root' => realpath(__DIR__ . '/..')
]);
$application = new Application();
$application->setCommandLoader($container->get('console.command_loader'));
$application->run();

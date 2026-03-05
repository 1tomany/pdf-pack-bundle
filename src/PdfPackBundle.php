<?php

namespace OneToMany\PdfPackBundle;

use OneToMany\PdfPack\Action\ExtractAction;
use OneToMany\PdfPack\Action\ReadAction;
use OneToMany\PdfPack\Client\Mock\MockClient;
use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Action\ExtractActionInterface;
use OneToMany\PdfPack\Contract\Action\ReadActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Factory\ClientFactory;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

class PdfPackBundle extends AbstractBundle
{
    protected string $extensionAlias = 'onetomany_pdfpack';

    /**
     * @see Symfony\Component\Config\Definition\ConfigurableInterface
     *
     * @param DefinitionConfigurator<'array'> $definition
     */
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition
            ->rootNode()
                ->children()
                    ->stringNode('client')
                        ->cannotBeEmpty()
                        ->defaultValue('poppler')
                    ->end()
                    ->arrayNode('poppler_client')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->stringNode('pdfinfo_binary')
                                ->cannotBeEmpty()
                                ->defaultValue('pdfinfo')
                            ->end()
                            ->stringNode('pdftoppm_binary')
                                ->cannotBeEmpty()
                                ->defaultValue('pdftoppm')
                            ->end()
                            ->stringNode('pdftotext_binary')
                                ->cannotBeEmpty()
                                ->defaultValue('pdftotext')
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface
     *
     * @param array{
     *   client: non-empty-string,
     *   poppler_client: array{
     *     pdfinfo_binary: non-empty-string,
     *     pdftoppm_binary: non-empty-string,
     *     pdftotext_binary: non-empty-string,
     *   },
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container
            ->services()
                // Factories
                ->set(ClientFactory::class)
                    ->arg('$container', tagged_locator('onetomany.pdfpack.client', 'key'))

                // Actions
                ->set(ExtractAction::class)
                    ->arg('$client', service(ClientInterface::class))
                    ->alias(ExtractActionInterface::class, service(ExtractAction::class))
                ->set(ReadAction::class)
                    ->arg('$client', service(ClientInterface::class))
                    ->alias(ReadActionInterface::class, service(ReadAction::class))

                // Clients
                ->set(ClientInterface::class)
                    ->factory([service(ClientFactory::class), 'create'])
                    ->arg('$service', $config['client'])
                ->set(MockClient::class)
                    ->tag('onetomany.pdfpack.client', ['key' => 'mock'])
                ->set(PopplerClient::class)
                    ->tag('onetomany.pdfpack.client', ['key' => 'poppler'])
                    ->arg('$pdfInfoBinary', $config['poppler_client']['pdfinfo_binary'])
                    ->arg('$pdfToPpmBinary', $config['poppler_client']['pdftoppm_binary'])
                    ->arg('$pdfToTextBinary', $config['poppler_client']['pdftotext_binary'])
        ;

        /*
        $container->import('../config/services.php');

        if ($builder->hasDefinition(ClientFactory::class)) {
            $builder
                ->getDefinition(ClientFactory::class)
                ->setArgument('$service', $config['client']);
        }

        if ($builder->hasDefinition(PopplerClient::class)) {
            $builder
                ->getDefinition(PopplerClient::class)
                ->setArgument('$pdfInfoBinary', $config['poppler_client']['pdfinfo_binary'])
                ->setArgument('$pdfToPpmBinary', $config['poppler_client']['pdftoppm_binary'])
                ->setArgument('$pdfToTextBinary', $config['poppler_client']['pdftotext_binary']);
        }
        */
    }
}

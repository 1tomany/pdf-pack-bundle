<?php

namespace OneToMany\PdfPackBundle;

use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Factory\ClientFactory;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

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
        $definition->import('../config/config.php');
    }

    /**
     * @see Symfony\Component\DependencyInjection\Extension\ConfigurableExtensionInterface
     *
     * @param array{
     *   client: 'mock'|'poppler',
     *   poppler_client: array{
     *     pdfinfo_binary: non-empty-string,
     *     pdftoppm_binary: non-empty-string,
     *     pdftotext_binary: non-empty-string,
     *   },
     * } $config
     */
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
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
    }
}

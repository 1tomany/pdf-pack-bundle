<?php

use OneToMany\PdfPack\Action\ExtractAction;
use OneToMany\PdfPack\Action\ReadAction;
use OneToMany\PdfPack\Client\Mock\MockClient;
use OneToMany\PdfPack\Client\Poppler\PopplerClient;
use OneToMany\PdfPack\Contract\Action\ExtractActionInterface;
use OneToMany\PdfPack\Contract\Action\ReadActionInterface;
use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Factory\ClientFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $container): void {
    $container
        ->services()
            // Factories
            ->set(ClientFactory::class)
                ->arg('$container', tagged_locator('1tomany.pdfpack.client', 'key'))

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
                ->arg('$id', 'poppler')
            ->set(MockClient::class)
                ->tag('1tomany.pdfpack.client', ['key' => 'mock'])
            ->set(PopplerClient::class)
                ->tag('1tomany.pdfpack.client', ['key' => 'poppler'])
    ;
};

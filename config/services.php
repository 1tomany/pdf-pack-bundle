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
// use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_locator;

return static function (ContainerConfigurator $container): void {
    $container
        ->services()
            // Factories
            ->set('1tomany.pdfpack.factory.client', ClientFactory::class)
                ->arg('$container', tagged_locator('1tomany.pdfpack.action.extract', 'key'))

            // Actions
            ->alias(ExtractActionInterface::class, service('1tomany.pdfpack.action.extract'))
            ->set('1tomany.pdfpack.action.extract', ExtractAction::class)
                ->arg('$client', service('1tomany.pdfpack.client.interface'))
            ->alias(ReadActionInterface::class, service('1tomany.pdfpack.action.read'))
            ->set('1tomany.pdfpack.action.read', ReadAction::class)
                ->arg('$clientFactory', service('1tomany.pdfpack.client.interface'))

            // Clients
            ->set('1tomany.pdfpack.client.interface', ClientInterface::class)
                ->factory([service('1tomany.pdfpack.factory.client'), 'create'])
                ->arg('$id', 'poppler')
            ->set('1tomany.pdfpack.client.mock', MockClient::class)
                ->tag('1tomany.pdfpack.client', ['key' => 'mock'])
            ->set('1tomany.pdfpack.client.poppler', PopplerClient::class)
                ->tag('1tomany.pdfpack.client', ['key' => 'poppler'])
    ;
};

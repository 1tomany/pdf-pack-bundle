<?php

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;

/**
 * @param DefinitionConfigurator<'array'> $configurator
 */
$configurator = static function (DefinitionConfigurator $configurator): void {
    $configurator
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
};

return $configurator;

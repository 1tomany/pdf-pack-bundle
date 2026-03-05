# PDF Extraction Bundle for PHP

[pdf-pack](https://github.com/1tomany/pdf-pack) is a simple PHP library that makes rasterizing pages and extracting text from PDFs for large language models easy.

## Install the bundle

```shell
composer require 1tomany/pdf-pack-bundle
```

## Usage

Symfony will autowire the necessary classes after the bundle is installed. Any constructor argument typed with `OneToMany\PdfPack\Contract\Action\ExtractActionInterface` or `OneToMany\PdfPack\Contract\Action\ReadActionInterface` will allow you to interact with the concrete extractor client via the `act()` method.

```php
<?php

namespace App\File\Action\Handler;

use OneToMany\PdfPack\Contract\Action\ExtractActionInterface;
use OneToMany\PdfPack\Contract\Action\ReadActionInterface;
use OneToMany\PdfPack\Request\ExtractRequest;
use OneToMany\PdfPack\Request\ReadRequest;

final readonly class UploadFileHandler
{
    public function __construct(
        private ReadActionInterface $readAction,
        private ExtractActionInterface $extractAction,
    ) {
    }

    public function handle(string $filePath): void
    {
        // Read PDF metadata like page count
        $metadata = $this->readAction->act(
            new ReadRequest($filePath)
        );

        // Rasterize all pages of a PDF
        $request = new ExtractRequest($filePath)
            ->fromPage(1) // First page to extract
            ->toPage(null) // Last page to extract, NULL for all pages
            ->asPngOutput() // Generate PNG images
            ->atResolution(150); // At 150 DPI

        // @see OneToMany\PdfPack\Response\ExtractResponse
        foreach ($this->extractAction->act($request) as $page) {
            // $page->getData() or $page->toDataUri()
        }

        // Extract text from pages 2 through 8
        $request = new ExtractRequest($filePath, 2, 8)->asTextOutput();

        // @see OneToMany\PdfPack\Response\ExtractResponse
        foreach ($this->extractAction->act($request) as $page) {
            // $page->getData() or $page->toDataUri()
        }
    }
}
```

### Testing

If you wish to avoid interacting with an external process in your test environment, you can take advantage of the `MockClient` by simply setting the `onetomany_pdfpack.client` parameter to the value `'mock'` in your Symfony service configuration for the `test` environment.

```yaml
when@test:
    onetomany_pdfpack:
        client: "mock"
```

Without changing _any_ other code, Symfony will automatically inject the `MockClient` instead of the default `PopplerClient` for your tests.

### Creating your own client

Don't want to use Poppler? No problem! Create your own extractor class that implements the `OneToMany\PdfPack\Contract\Client\ClientInterface` interface and tag it accordingly.

```php
<?php

namespace App\PdfPack\Client\Magick;

use OneToMany\PdfPack\Contract\Client\ClientInterface;
use OneToMany\PdfPack\Contract\Request\ExtractRequest;
use OneToMany\PdfPack\Contract\Request\ReadRequest;
use OneToMany\PdfPack\Contract\Response\ReadResponse;

class MagickClient implements ClientInterface
{
    public function read(ReadRequest $request): ReadResponse
    {
        // Add your implementation here
    }

    public function extract(ExtractRequest $request): \Generator
    {
        // Add your implementation here
    }
}
```

```yaml
onetomany_pdfpack:
    client: "magick"

services:
    App\PdfPack\Client\Magick\MagickClient:
        tags:
            - { name: 1tomany.pdfpack.client, key: magick }
```

That's it! Again, without changing _any_ code, Symfony will automatically inject the correct extractor client for the action interfaces outlined above.

## Credits

- [Vic Cherubini](https://github.com/viccherubini), [1:N Labs, LLC](https://1tomany.com)

## License

The MIT License

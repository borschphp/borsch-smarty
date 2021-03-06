# Borsch Smarty Template Renderer

[![Build Status](https://travis-ci.com/borschphp/borsch-smarty.svg?branch=master)](https://travis-ci.com/borschphp/borsch-smarty)
[![Latest Stable Version](https://poser.pugx.org/borschphp/smarty/v)](//packagist.org/packages/borschphp/smarty)
[![License](https://poser.pugx.org/borschphp/smarty/license)](//packagist.org/packages/borschphp/smarty)

An implementation of Smarty for the Borsch's TemplateRendererInterface.  

This package is part of the Borsch Framework.

## Installation

Via [composer](https://getcomposer.org/) :

`composer require borschphp/smarty`

## Integration in Borsch Framework

Open the file `config/container.php` then add your TemplateRendererInterface definition.

```php
use Borsch\Smarty\Smarty;
use Borsch\Template\TemplateRendererInterface;

/*
 * Template renderer definitions
 * -----------------------------
 *
 * Defining the TemplateRendererInterface here so our HomeHandler handler (see upper) can use it when instantiated.
 * The default Borsch Smarty renderer is used.
 * We cache it so that it is not re-created when fetched a second time.
 */
$container->set(TemplateRendererInterface::class, function () {
    $smarty = new Smarty();
    $smarty->setTemplateDir(__DIR__.'/../resources/templates');
    $smarty->setCompileDir(__DIR__.'/../storage/smarty/templates_c');
    $smarty->setCacheDir(__DIR__.'/../storage/smarty/cache');

    return $smarty;
})->cache(true);
```

To load it in your handler, add it as a parameter of the constructor :

```php
class HomeHandler implements RequestHandlerInterface
{

    /** @var TemplateRendererInterface */
    protected $renderer;

    /**
     * HomeHandler constructor.
     * @param TemplateRendererInterface $renderer
     */
    public function __construct(TemplateRendererInterface $renderer)
    {
        $this->renderer = $renderer;
    }

    /**
     * @inheritDoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse($this->renderer->render('home'));
    }
}
```

## Usage

```php
$smarty = new \Borsch\Smarty\Smarty();

// Adding some directories where templates are located
$smarty->addPath(__DIR__.'/templates');
$smarty->addPath(__DIR__.'/templates/error', 'error');
$smarty->addPath(__DIR__.'/templates/success', 'success');

// Assign some variables
$smarty->assign([
    'key1' => 'value1',
    'key2' => 'value2',
    'key3' => 'value3'
]);

// Display the template (with or without .tpl extension)
echo $smarty->render('template_file_name');
```

## License

The package is licensed under the MIT license. See [License File](https://github.com/borschphp/borsch-smarty/blob/master/LICENSE.md) for more information.
<?php
/**
 * @author debuss-a
 */

namespace BorschTest;

require_once __DIR__.'/../vendor/autoload.php';

use Borsch\Smarty\Smarty;
use PHPUnit\Framework\TestCase;

class SmartyTest extends TestCase
{

    /** @var Smarty */
    protected $smarty;

    public function setUp(): void
    {
        $this->smarty = new Smarty();
    }

    public function testAddPath()
    {
        $this->smarty->addPath(__DIR__.'/templates');
        $this->smarty->addPath(__DIR__.'/templates/error', 'error');
        $this->smarty->addPath(__DIR__.'/templates/success', 'success');

        // Here we use native Smarty method to get directory listing
        $paths = $this->smarty->getTemplateDir();

        // Smarty automatically adds "./templates" directory by default
        array_shift($paths);

        $this->assertIsArray($paths);
        $this->assertEquals(
            [
                realpath(__DIR__.'/templates').'/',
                realpath(__DIR__.'/templates/error').'/',
                realpath(__DIR__.'/templates/success').'/'
            ],
            array_values($paths)
        );
    }

    public function testPathKeyIsNamespace()
    {
        $this->smarty->addPath(__DIR__.'/templates/error', 'error');
        $this->smarty->addPath(__DIR__.'/templates/success', 'success');

        $paths = $this->smarty->getTemplateDir();

        $this->assertIsArray($paths);

        $this->assertArrayHasKey('error', $paths);
        $this->assertEquals($paths['error'], realpath(__DIR__.'/templates/error').'/');

        $this->assertArrayHasKey('success', $paths);
        $this->assertEquals($paths['success'], realpath(__DIR__.'/templates/success').'/');

    }

    public function testGetPaths()
    {
        $this->smarty->addPath(__DIR__.'/templates');
        $this->smarty->addPath(__DIR__.'/templates/error', 'error');
        $this->smarty->addPath(__DIR__.'/templates/success', 'success');

        // Here we use TemplateRendererInterface method to get directory listing
        $paths = $this->smarty->getPaths();

        // Smarty automatically adds "./templates" directory by default
        array_shift($paths);

        $this->assertIsArray($paths);
        $this->assertEquals(
            [
                realpath(__DIR__.'/templates').'/',
                realpath(__DIR__.'/templates/error').'/',
                realpath(__DIR__.'/templates/success').'/'
            ],
            array_values($paths)
        );
    }

    public function testAssign()
    {
        $this->smarty->addPath(__DIR__.'/templates');
        $this->smarty->assign(['my_value' => 42]);
        // Here we use the native fetch method from Smarty
        $this->assertEquals(42, $this->smarty->fetch('test_assign.tpl'));
    }

    public function testRenderWithoutNamespace()
    {
        $this->smarty->addPath(__DIR__.'/templates');
        $this->smarty->assign(['my_value' => 42]);
        // Here we use the TemplateRendererInterface method
        $this->assertEquals(42, $this->smarty->render('test_assign.tpl'));
    }

    public function testRenderWithNamespace()
    {
        $this->smarty->addPath(__DIR__.'/templates');
        $this->smarty->addPath(__DIR__.'/templates/error', 'error');

        $this->assertEquals(
            '404 Not Found',
            $this->smarty->render('error::404')
        );
    }

    public function testRenderWithParams()
    {
        $this->smarty->addPath(__DIR__.'/templates');
        $this->smarty->assign(['my_value' => 42]);

        $this->assertEquals(42, $this->smarty->render('test_assign.tpl'));
        // Let's replace my_value by "02"
        $this->assertEquals('02', $this->smarty->render('test_assign.tpl', ['my_value' => '02']));
        $this->assertEquals(42, $this->smarty->render('test_assign.tpl'));
    }

    public function testTemplateExtensionCanBeOmitted()
    {
        $this->smarty->addPath(__DIR__.'/templates/error', 'error');

        $this->assertEquals(
            '404 Not Found',
            $this->smarty->render('404')
        );
    }
}

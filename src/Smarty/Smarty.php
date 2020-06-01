<?php
/**
 * @author debuss-a
 */

namespace Borsch\Smarty;

use BadMethodCallException;
use Borsch\Template\AbstractTemplateRenderer;
use InvalidArgumentException;
use Smarty as SmartyEngine;
use SmartyException;

/**
 * Class Smarty
 * @package Borsch\Smarty
 * @mixin SmartyEngine
 */
class Smarty extends AbstractTemplateRenderer
{

    /** @var SmartyEngine */
    protected $smarty;

    /**
     * Smarty constructor.
     */
    public function __construct()
    {
        $this->smarty = new SmartyEngine();
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments = [])
    {
        if (!method_exists($this->smarty, $name)) {
            throw new BadMethodCallException(sprintf(
                'Method %s does not exist in Smarty...',
                $name
            ));
        }

        $result = call_user_func_array([$this->smarty, $name], $arguments);
        if ($result instanceof SmartyEngine) {
            return $this;
        }

        return $result;
    }

    /**
     * @param string $path
     * @param string|null $namespace
     */
    public function addPath(string $path, ?string $namespace = null): void
    {
        $this->smarty->addTemplateDir($path, $namespace);
    }

    /**
     * @return string[]
     */
    public function getPaths(): array
    {
        return $this->smarty->getTemplateDir();
    }

    /**
     * @param array $params
     */
    public function assign(array $params): void
    {
        $this->smarty->assign($params);
    }

    /**
     * @param string $name
     * @param array $params
     * @return string
     * @throws SmartyException
     * @throws InvalidArgumentException
     */
    public function render(string $name, array $params = []): string
    {
        $namespace_template = explode('::', $name);
        $count = count($namespace_template);

        if ($count == 0 || $count > 2) {
            throw new InvalidArgumentException(sprintf(
                'Invalide `namespace::template` parameter provided...'
            ));
        }

        $template = $namespace_template[0];
        if ($count == 2) {
            $template = sprintf('%s/%s', $template, $namespace_template[1]);
        }

        $template = trim($template);
        if (substr($template, -4) != '.tpl') {
            $template .= '.tpl';
        }

        // Clone smarty so $params will not override the one already in place.
        $smarty = clone $this->smarty;

        if (count($params)) {
            $smarty->assign($params);
        }
        
        return $smarty->fetch($template);
    }
}

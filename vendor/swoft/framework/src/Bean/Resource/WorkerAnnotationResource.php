<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */
namespace Swoft\Bean\Resource;

use Swoft\Helper\ComponentHelper;

/**
 *  The annotation resource of worker
 */
class WorkerAnnotationResource extends AnnotationResource
{
    /**
     * Register the scaned namespace
     */
    public function registerNamespace()
    {
        $hostDir = \dirname(__FILE__, 5);
        if (\in_array(\basename($hostDir), ['swoft', 'src'])) {
            //install by composer
            $componentDirs = scandir($hostDir, null);
        } else {
            //independent
            $componentDirs = ['swoft-framework'];
        }
        foreach ($componentDirs as $component) {
            if ($component === '.' || $component === '..') {
                continue;
            }

            $componentDir = $hostDir . DS . $component;
            $componentCommandDir = $componentDir . DS . 'src';
            if (! is_dir($componentCommandDir)) {
                continue;
            }

            $ns = ComponentHelper::getComponentNamespace($component, $componentDir);
            $this->componentNamespaces[] = $ns;

            // ignore the comoponent of console
            if ($component === $this->consoleName) {
                continue;
            }

            $scanDirs = scandir($componentCommandDir, null);
            foreach ($scanDirs as $dir) {
                if ($dir == '.' || $dir == '..') {
                    continue;
                }
                if (\in_array($dir, $this->serverScan, true)) {
                    continue;
                }
                $scanDir = $componentCommandDir . DS . $dir;

                if (! is_dir($scanDir)) {
                    $this->scanFiles[$ns][] = $scanDir;
                    continue;
                }
                $scanNs = $ns . '\\' . $dir;

                $this->scanNamespaces[$scanNs] = $scanDir;
            }
        }
    }
}

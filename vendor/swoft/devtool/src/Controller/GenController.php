<?php
/**
 * This file is part of Swoft.
 *
 * @link https://swoft.org
 * @document https://doc.swoft.org
 * @contact group@swoft.org
 * @license https://github.com/swoft-cloud/swoft/blob/master/LICENSE
 */

namespace Swoft\Devtool\Controller;

use Swoft\Http\Message\Server\Request;
use Swoft\Http\Server\Bean\Annotation\Controller;
use Swoft\Http\Server\Bean\Annotation\RequestMapping;
use Swoft\Http\Server\Bean\Annotation\RequestMethod;

/**
 * Class GenController
 * @Controller(prefix="/__devtool/gen/")
 * @package Swoft\Devtool\Controller
 */
class GenController
{
    /**
     * this is a example action
     * @RequestMapping(route="/__devtool/gen", method=RequestMethod::GET)
     * @param Request $request
     * @return array
     */
    public function index(Request $request): array
    {
        // $request->isAjax();

        return ['item0', 'item1'];
    }

    /**
     * Generate class file preview
     * @RequestMapping(route="preview", method=RequestMethod::POST)
     * @param Request $request
     * @return array
     */
    public function preview(Request $request): array
    {
        // $data = $request->json();

        return ['item0', 'item1'];
    }

    /**
     * Generate class file
     * @RequestMapping(route="file", method=RequestMethod::POST)
     * @param Request $request
     * @return array
     */
    public function create(Request $request): array
    {
        // $request->isAjax();

        return ['item0', 'item1'];
    }


}

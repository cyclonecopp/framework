<?php

namespace Dappur\Controller\Admin;

use Dappur\App\FileBrowser;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
class Developer extends Controller
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function logs(Request $request, Response $response)
    {
        if ($check = $this->sentinel->hasPerm('settings.developer', 'dashboard')) {
            return $check;
        }
        
        if ($request->getParam('operation')) {
            $fs = new FileBrowser(realpath(dirname(__FILE__) . '/../../../../storage/log'));
            $rslt = null;
            switch ($request->getParam('operation')) {
                case 'get_node':
                    $node = $request->getParam('id') && $request->getParam('id') !== '#' ? $request->getParam('id') : '/';
                    $rslt = $fs->lst($node, ($request->getParam('id') && $request->getParam('id') === '#'));
                    break;
                case "get_content":
                    $node = $request->getParam('id') && $request->getParam('id') !== '#' ? $request->getParam('id') : '/';
                    $rslt = $fs->data($node);
                    break;
                case 'create_node':
                    $node = $request->getParam('id') && $request->getParam('id') !== '#' ? $request->getParam('id') : '/';
                    $rslt = $fs->create($node, $request->getParam('text') ? $request->getParam('text') : '', (!$request->getParam('type') || $request->getParam('type') !== 'file'));
                    break;
                case 'rename_node':
                    $node = $request->getParam('id') && $request->getParam('id') !== '#' ? $request->getParam('id') : '/';
                    $rslt = $fs->rename($node, $request->getParam('text') ? $request->getParam('text') : '');
                    break;
                case 'delete_node':
                    $node = $request->getParam('id') && $request->getParam('id') !== '#' ? $request->getParam('id') : '/';
                    $rslt = $fs->remove($node);
                    break;
                case 'move_node':
                    $node = $request->getParam('id') && $request->getParam('id') !== '#' ? $request->getParam('id') : '/';
                    $parn = $request->getParam('parent') && $request->getParam('parent') !== '#' ? $request->getParam('parent') : '/';
                    $rslt = $fs->move($node, $parn);
                    break;
                case 'copy_node':
                    $node = $request->getParam('id') && $request->getParam('id') !== '#' ? $request->getParam('id') : '/';
                    $parn = $request->getParam('parent') && $request->getParam('parent') !== '#' ? $request->getParam('parent') : '/';
                    $rslt = $fs->copy($node, $parn);
                    break;
                default:
                    throw new Exception('Unsupported operation: ' . $request->getParam('operation'));
                    break;
            }
            $response = $response->write(json_encode($rslt));
            $response = $response->withHeader('Content-Type', 'application/json;charset=utf-8');
            return $response->withStatus(201);

            return false;
        }

        return $this->view->render($response, 'developer-logs.twig');
    }
}

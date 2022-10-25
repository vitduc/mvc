<?php
declare(strict_types=1);

namespace Cowell\BasicTraining\Controller;

use Magento\Framework\App\Action\Forward;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\App\RouterInterface;

class Router implements RouterInterface
{
    /**
     * @var ActionFactory
     */
    private $actionFactory;
    /**
     * @var ResponseInterface
     */
    private $response;
    /**
     * Router constructor.
     *
     * @param ActionFactory $actionFactory
     * @param ResponseInterface $response
     */
    public function __construct(
        ActionFactory $actionFactory,
        ResponseInterface $response
    ) {
        $this->actionFactory = $actionFactory;
        $this->response = $response;
    }
    /**
     * @param RequestInterface $request
     * @return ActionInterface|null
     */
    public function match(RequestInterface $request): ?ActionInterface
    {
        $identifier = trim($request->getPathInfo(), '/');
        $arrayURL = explode('/', $identifier);
        if (strpos($identifier, 'danh-sach-sinh-vien') !== false) {
            $request->setModuleName('training3');
            $request->setControllerName('student');
            $request->setActionName('index');
            $request->setParams([
                'p' => $request->getParam('p'),
            ]);
            return $this->actionFactory->create(Forward::class, ['request' => $request]);
        }
        if (strpos($identifier, 'sinh-vien') !== false && count($arrayURL) == 2
            && $arrayURL[0] == 'sinh-vien' && is_numeric($arrayURL[1])) {
            $request->setModuleName('training3');
            $request->setControllerName('student');
            $request->setActionName('detail');
            $request->setParams([
                'id' => $arrayURL[1]
            ]);
            return $this->actionFactory->create(Forward::class, ['request' => $request]);
        }
        return null;
    }
}

<?php

namespace Meetanshi\Inquiry\Controller;

use Meetanshi\Inquiry\Helper\Data;
use Magento\Framework\App\ActionFactory;
use Magento\Framework\App\RouterInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Url;

class Router implements RouterInterface
{
    protected $actionFactory;
    protected $helper;

    public function __construct(
        ActionFactory $actionFactory,
        Data $helper
    )
    {
        $this->actionFactory = $actionFactory;
        $this->helper = $helper;
    }

    public function match(RequestInterface $request)
    {
        $identifier = trim($request->getPathInfo(), '/');
        $identifier = urldecode($identifier);
        $pathInfo = explode('/', $identifier);
        $blogRoute = $this->helper->getUrlKey();
        $blogRoute = explode('/', $blogRoute);
        $result = array_diff($pathInfo, $blogRoute);
        if (!empty($result)) {
            return;
        }
        $request->setRouteName('inquiry')->setControllerName('index')->setActionName('index');
        $request->setAlias(Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);
        return $this->actionFactory->create(
            'Magento\Framework\App\Action\Forward',
            ['request' => $request]
        );
    }
}

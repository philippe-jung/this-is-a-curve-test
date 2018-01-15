<?php

namespace Curve\Module\Service;

use Curve\Module\Service\Response\Error;
use Curve\Request;
use Curve\Module\DispatcherInterface;
use Curve\Module\Service\Exception;

abstract class AbstractServiceDispatcher implements DispatcherInterface
{
    /**
     * Execute the service matching the Request Params
     *
     * @throws \Exception
     */
    public function execute()
    {
        try {
            $service = $this->getServiceObject();
            $response = $service->execute();
        } catch (Exception\Exception $e) {
            $response = new Error($e->getMessage());
        }
        $response->send();
    }

    /**
     * Return the service that matches the Request Method
     *
     * @return AbstractService
     * @throws Exception\UnsupportedMethod
     * @throws \Exception
     */
    protected function getServiceObject(): AbstractService
    {
        $method = Request::getMethod();
        $baseNamespace = get_class($this);
        $dispatcherClassName = substr(strrchr($baseNamespace, "\\"), 1);

        $serviceClassName = str_replace($dispatcherClassName, ucfirst(strtolower($method)), $baseNamespace);

        // if the class does not exist, that means the type of request is not supported
        if (!class_exists($serviceClassName)) {
            throw new Exception\UnsupportedMethod($method);
        }

        $service = new $serviceClassName();
        // if the class is not an AbstractService, something is wrong on our side
        if (!$service instanceof AbstractService) {
            throw new \Exception($serviceClassName . ' is not an AbstractService');
        }

        return $service;
    }
}
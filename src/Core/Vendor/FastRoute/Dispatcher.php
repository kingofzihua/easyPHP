<?php

namespace FastRoute;

interface Dispatcher
{
    const NOT_FOUND = 0; //没找到
    const FOUND = 1;//找到了
    const METHOD_NOT_ALLOWED = 2; //不允许的方法

    /**
     * 根据提供的HTTP方法动词和URI进行分派。
     * Dispatches against the provided HTTP method verb and URI.
     *
     * Returns array with one of the following formats:
     *
     *     [self::NOT_FOUND]
     *     [self::METHOD_NOT_ALLOWED, ['GET', 'OTHER_ALLOWED_METHODS']]
     *     [self::FOUND, $handler, ['varName' => 'value', ...]]
     *
     * @param string $httpMethod
     * @param string $uri
     *
     * @return array
     */
    public function dispatch($httpMethod, $uri);
}
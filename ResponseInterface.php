<?php

namespace src\Integration;

/**
 * Interface ResponseInterface
 * @package src\Integration\ResponseInterface
 */
interface ResponseInterface
{
    /**
     * @param array $request
     * @return array
     */
    public function getResponse(array $request): array;

    /**
     * @param array $request
     * @return array
     */
    public function getRemoteRequest(array $request): array;
}

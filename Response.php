<?php
namespace src\Integration;

use DateTime;
use Exception;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Log\LoggerInterface;
use src\Integration\ResponseInterface;

/**
 * Class Response
 * @package src\Integration
 */
class Response implements ResponseInterface
{
    /**
     * @var CacheItemPoolInterface
     */
    public $cache;
    /**
     * @var LoggerInterface
     */
    public $logger;

    /**
     * @param string $host
     * @param string $user
     * @param string $password
     */
    public function __construct(string $host, string $user, string $password)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
    }

    /**
     * @param CacheItemPoolInterface $cache
     */
    public function setCache(CacheItemPoolInterface $cache): void
    {
        $this->cache = $cache;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $request
     *
     * @return array
     */
    public function getRemoteRequest(array $request)
    {
        // returns a response from external service
    }

    /**
     * {@inheritdoc}
     */
    public function getResponse(array $input): array
    {
        try {
            $cacheKey = $this->getCacheKey($input);
            $cacheItem = $this->cache->getItem($cacheKey);
            if ($cacheItem->isHit()) {
                return $cacheItem->get();
            }

            $result = $this->getRemoteRequest($input);

            $cacheItem
                ->set($result)
                ->expiresAt(
                    (new DateTime())->modify('+1 day')
                );

            return $result;
        } catch (Exception $e) {
            $this->logger->critical('Error');
        }

        return [];
    }

    /**
     * @param array $input
     * @return string
     */
    public function getCacheKey(array $input): string
    {
        return md5($input);
    }
}

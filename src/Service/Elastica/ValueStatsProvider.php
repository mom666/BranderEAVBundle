<?php
namespace Brander\Bundle\EAVBundle\Service\Elastica;

use Brander\Bundle\EAVBundle\Repo\Value;
use Brander\Bundle\EAVBundle\Service\Filter\ValueMinMax;
use Brander\Bundle\EAVBundle\Service\Stats\ProviderInterface;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\InvalidArgumentException as InvalidArgumentExceptionInterface;
use Symfony\Component\Cache\Exception\InvalidArgumentException;

/**
 * @author tomfun
 */
class ValueStatsProvider implements ProviderInterface
{
    /**
     * @var Value
     */
    private $repoValue;

    /**
     * @param Value $value
     */
    public function __construct(
        Value $value
    ) {
        $this->repoValue = $value;
    }


    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *   The key for which to return the corresponding Cache Item.
     *
     * @throws InvalidArgumentExceptionInterface
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return CacheItemInterface
     *   The corresponding Cache Item.
     */
    public function getItem($key)
    {
        if (strpos($key, ValueMinMax::VALUE_STAT) !== 0) {
            throw new InvalidArgumentException('Wrong attribute');
        }
        return new ValueMinMax($this->repoValue, ValueMinMax::parseAttributeId($key));
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *    The key for which to check existence.
     *
     * @throws InvalidArgumentExceptionInterface
     *   If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *   MUST be thrown.
     *
     * @return bool
     *  True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        return ValueMinMax::isMyKey($key);
    }
}
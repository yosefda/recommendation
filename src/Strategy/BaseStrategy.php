<?php

namespace Yosefda\Recommendation\Strategy;

/**
 * Class BaseRecommendationStrategy
 * @package Yosefda\Recommendation\Strategy
 *
 * Base class for all recommendation strategies in our application.
 */
abstract class BaseStrategy
{
    /**
     * @var string[]
     * List of required input criteria for calculating recommendation.
     */
    protected $requiredInputCriteria;

    /**
     * @var iterable
     * List of items from which recommendation is to be made.
     */
    protected $items;

    /**
     * Get recommended items.
     * @param string[] $criteria Required criteria to calculate the recommended items
     * @param iterable $items List of items from which recommendation is to be made
     * @return iterable
     * @throws \InvalidArgumentException
     */
    public function getRecommendations(array $criteria, iterable $items)
    {
        $missing_criteria = $this->getMissingCriteria($criteria);
        if (!empty($missing_criteria)) {
            throw new \InvalidArgumentException("Missing required criteria '{$missing_criteria}'");
        }

        $this->items = $items;
    }

    /**
     * Get any missing required criteria.
     * @param string[] $criteria
     * @return string
     */
    protected function getMissingCriteria(array $criteria)
    {
        // make sure that all required criteria are given
        foreach ($this->requiredInputCriteria as $required) {
            if (empty($criteria[$required])) {
                return $required;
            }
        }

        return "";
    }
}
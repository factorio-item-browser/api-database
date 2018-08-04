<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Constant;

/**
 * The interface holding the priorities of search results.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface SearchResultPriority
{
    /**
     * The search result is an exact match of the name, the highest priority.
     */
    public const EXACT_MATCH = 1;

    /**
     * The search result is matched by the primary locale, i.e. the language of the user.
     */
    public const PRIMARY_LOCALE_MATCH = 10;

    /**
     * The search results is matched by the secondary locale, i.e. English.
     */
    public const SECONDARY_LOCALE_MATCH = 11;

    /**
     * The search result has no relevance compared to the others.
     */
    public const ANY_MATCH = 100;
}

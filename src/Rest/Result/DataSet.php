<?php

namespace Pipas\Rest\Result;

/**
 * Result set of objects from repository
 * @author Petr Štipek <p.stipek@email.cz>
 * @property-read int $totalCount
 */
class DataSet extends DataArray
{
    /** @var int */
    protected $totalCount;

    function __construct($data = array(), $totalCount = 0)
    {
        $this->totalCount = (int)$totalCount;
        parent::__construct($data);
    }

    /**
     * Total count of existing records into database
     * @return int
     */
    function getTotalCount()
    {
        return $this->totalCount;
    }


}

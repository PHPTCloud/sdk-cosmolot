<?php

/**
 * @class AbstractServiceContract
 * @package Cosmolot\Contracts
 */

namespace Cosmolot\Contracts;

use Cosmolot\AbstractModel;

interface AbstractServiceContract
{
    /**
     * @param int $id
     * @return AbstractModel
     */
    public function getOne(int $id);

    /**
     * @return AbstractModel[]
     */
    public function get();
}

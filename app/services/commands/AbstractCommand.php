<?php

namespace App\Services\Commands;

use App\Model\DataModel;

abstract class AbstractCommand
{
    protected DataModel $data_model;
    
    abstract protected function execute($entry);

    public function __construct(DataModel $data_model)
    {
        $this->data_model = $data_model;
    }
}

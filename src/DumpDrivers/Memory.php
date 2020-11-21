<?php
declare(strict_types=1);

namespace Harpya\DataGate\DumpDrivers;

class Memory implements DriverInterface
{
    protected $data = [];

    public function getData() : array
    {
        return $this->data;
    }

    public function addItem($item)
    {
        $this->data[] = $item;
    }
}

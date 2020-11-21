<?php

namespace Harpya\DataGate\DumpDrivers;

interface DriverInterface
{
    public function getData() : array;
}

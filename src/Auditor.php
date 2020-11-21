<?php
declare(strict_types=1);

namespace Harpya\DataGate;

class Auditor
{
    protected $driver;

    public function getDriver() : \Harpya\DataGate\DumpDrivers\DriverInterface
    {
        return $this->driver;
    }

    public function __construct(\Harpya\DataGate\DumpDrivers\DriverInterface $driver)
    {
        $this->driver = $driver;
    }

    public function handle()
    {
        $this->handleGetVariables();
    }

    protected function handleGetVariables()
    {
        if (isset($_GET)) {
            foreach ($_GET as $k => $v) {
                $item = $this->evaluate($k, $v);
                $this->getDriver()->addItem($item);
            }
        }
    }

    protected function evaluate($k, $v)
    {
        $evalResult = [
            'name' => $k,
            'value' => $this->encode('' . $v),
            'inferred_types' => [],
        ];

        if (\is_scalar($v)) {
            $evalResult['length'] = strlen('' . $v);
        }

        if (\is_numeric($v)) {
            $evalResult['inferred_types'][] = 'numeric';
        }

        return $evalResult;
    }

    public function encode($v) : string
    {
        return base64_encode($v);
    }

    public function decode($v) : string
    {
        return base64_decode($v);
    }
}

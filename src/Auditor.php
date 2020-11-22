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
        $listSuperVars = ['_GET', '_POST', '_COOKIE'];

        foreach ($listSuperVars as $varName) {
            if (isset($GLOBALS[$varName])) {
                foreach ($GLOBALS[$varName] as $k => $v) {
                    if (\is_scalar($v)) {
                        $item = $this->evaluate($k, $v);
                        $this->getDriver()->addItem($item);
                    } elseif (is_array($v)) {
                        $item = $this->evaluateArray($k, $v);
                        $this->getDriver()->addItem($item);
                    }
                }
            }
        }
    }

    protected function evaluateArray($k, $v)
    {
        $evalResult = [
            'name' => $k,
            'type' => 'array',
        ];
        foreach ($v as $k2 => $v2) {
            if (\is_scalar($v2)) {
                $item = $this->evaluate($k2, $v2, $k);
                $this->getDriver()->addItem($item);
            } elseif (is_array($v2)) {
                $item = $this->evaluateArray($k . '/' . $k2, $v2);
                $this->getDriver()->addItem($item);
            }
        }
        return $evalResult;
    }

    protected function evaluate($k, $v, $scope = null)
    {
        $evalResult = [
            'name' => $k,
            'value' => $this->encode('' . $v),
            'inferred_types' => [],
        ];

        if ($scope) {
            $evalResult['scope'] = $scope;
        }

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

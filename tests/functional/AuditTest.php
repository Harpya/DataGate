<?php

use PHPUnit\Framework\TestCase;
use Harpya\DataGate\Auditor;
use Harpya\DataGate\DumpDrivers\Memory;

class AuditTest extends TestCase
{
    protected $driver;
    protected $auditor;

    public function getDriver($clear = false)
    {
        if (!$this->driver || $clear) {
            $this->driver = new Memory();
        }
        return $this->driver;
    }

    public function getAuditor($clear = true)
    {
        if (!$this->auditor || $clear) {
            $this->auditor = new Auditor($this->getDriver($clear));
        }
        return $this->auditor;
    }

    public function testAuditEmpty()
    {
        $this->getAuditor(true)->handle();
        $lines = $this->getDriver()->getData();
        $this->assertTrue(is_array($lines));
        $this->assertCount(0, $lines);
    }

    public function testGetVariable()
    {
        $_GET = [
            'email' => 'john.doe@company.com',
            'user_id' => 12345
        ];
        $this->getAuditor(true)->handle();
        $lines = $this->getDriver()->getData();
        $this->assertTrue(is_array($lines));
        $this->assertCount(count($_GET), $lines);

        foreach ($lines as $k => $entry) {
            $name = $entry['name'];
            $this->assertEquals(strlen('' . $_GET[$name]), $entry['length']);
            $this->assertEquals($_GET[$name], $this->getAuditor()->decode($entry['value']));
        }
    }
}

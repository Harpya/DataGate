<?php

use PHPUnit\Framework\TestCase;
use Harpya\DataGate\Auditor;
use Harpya\DataGate\DumpDrivers\Memory;

class AuditTest extends TestCase
{
    protected $driver;
    protected $auditor;

    protected function tearDown()
    {
        $_GET = [];
        $_POST = [];
        $_JSON = [];
        $_COOKIE = [];
    }

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

    public function testPostVariable()
    {
        $_POST = [
            'email' => 'john.doe@company.com',
            'user_id' => 12345
        ];

        $this->getAuditor(true)->handle();
        $lines = $this->getDriver()->getData();
        $this->assertTrue(is_array($lines));
        $this->assertCount(count($_POST), $lines);

        foreach ($lines as $k => $entry) {
            $name = $entry['name'];
            $this->assertEquals(strlen('' . $_POST[$name]), $entry['length']);
            $this->assertEquals($_POST[$name], $this->getAuditor()->decode($entry['value']));
        }
    }

    public function testGetPostVariables()
    {
        $_GET = [
            'user_id' => 123
        ];

        $_POST = [
            'email' => 'john.doe@company.com',
            'user_id' => 12345
        ];

        $this->getAuditor(true)->handle();
        $lines = $this->getDriver()->getData();
        $this->assertTrue(is_array($lines));
        $this->assertCount(count($_POST) + count($_GET), $lines);
    }

    public function testComplexPostVariables()
    {
        $_POST = [
            'email' => 'john.doe@company.com',
            'user_id' => 12345,
            'data' => [
                'auth' => ['username' => 'abc', 'password' => 'xyz'],
                'metadata' => [
                    'nested' => [
                        'email' => 'abc@x.com'
                    ]
                ]
            ]
        ];

        $this->getAuditor(true)->handle();
        $lines = $this->getDriver()->getData();

        $this->assertTrue(is_array($lines));
        $this->assertCount(9, $lines);
    }
}

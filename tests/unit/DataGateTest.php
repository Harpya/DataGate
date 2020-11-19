<?php

use PHPUnit\Framework\TestCase;
use Harpya\DataGate\Gate;
use Harpya\DataGate\Validator;
use Harpya\DataGate\Rule;

class DataGateTest extends TestCase
{
    public function testWithNoPolicy()
    {
        $initialName = 'John Doe';
        $initialEmail = 'john.doe@domain.com';

        $_GET = [
            'name' => $initialName,
            'email' => $initialEmail
        ];

        $gate = new Gate();

        $gate->handle();

        $this->assertEquals($initialName, $_GET['name']);
        $this->assertEquals($initialEmail, $_GET['email']);
    }

    public function testSetSimplePolicy()
    {
        $initialName = 'John Seinfield Doe';
        $maxlength = 14;

        $_GET = [
            'fullname' => $initialName,
        ];

        $gate = new Gate();

        $gate->getPolicyManager()
            ->createRule('fullname', [], [
                'maxlength' => $maxlength,
                'onError' => Rule::ON_ERROR_JUST_FIX
            ]);

        $gate->handle();

        $this->assertEquals(substr($initialName, 0, $maxlength), $_GET['fullname']);
    }
}

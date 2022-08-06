<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class TestHistory extends CIUnitTestCase
{
    use ControllerTestTrait;
    use DatabaseTestTrait;
    public function testIndex()
    {
        $result = $this->withURI(base_url('asdzxcas'))
            ->controller(\App\Controllers\User\History::class)
            ->execute('index');

        $this->assertTrue($result->isOK());
    }
}

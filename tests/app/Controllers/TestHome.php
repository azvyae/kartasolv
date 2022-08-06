<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class TestHome extends CIUnitTestCase
{
    use ControllerTestTrait;
    use DatabaseTestTrait;
    public function testIndex()
    {
        $result = $this->withURI(base_url())
            ->controller(\App\Controllers\Home::class)
            ->execute('index');

        $this->assertTrue($result->isOK());
    }
}

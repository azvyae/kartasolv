<?php

namespace CodeIgniter;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\ControllerTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

class Profile extends CIUnitTestCase
{
    use ControllerTestTrait;
    use DatabaseTestTrait;
    public function testIndex()
    {
        $result = $this->withURI(base_url())
            ->controller(\App\Controllers\User\Profile::class)
            ->execute('index');

        $this->assertTrue($result->isOK());
    }
}

<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Test tidak boleh bergantung pada hasil `npm run build` (manifest Vite),
        // sehingga job CI tanpa build aset tetap dapat merender halaman.
        $this->withoutVite();
    }
}

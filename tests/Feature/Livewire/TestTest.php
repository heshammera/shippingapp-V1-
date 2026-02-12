<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class TestTest extends TestCase
{
    public function test_renders_successfully()
    {
        Livewire::test(Test::class)
            ->assertStatus(200);
    }
}

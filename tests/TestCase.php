<?php

namespace Tests;

use Faker\Factory as Faker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $faker;

    /**
     * Sets up the tests
     */
    public function setUp(): void
    {
        parent::setUp();

        User::create([
            'name'=> 'Super Admin',
            'email'=> 'validsuperadmin@email.com',
            'password'=> bcrypt('validpassword'),
            'gender'=> 'M',
            'phone'=> '0123456789',
            'is_superadmin'=> true,
        ]);

        User::create([
            'name'=> 'Admin',
            'email'=> 'validadmin@email.com',
            'password'=> bcrypt('validpassword'),
            'gender'=> 'M',
            'phone'=> '0123456789',
            'is_superadmin'=> false,
        ]);

        $this->superadmin_token = $this->postJson('/api/login/', [
            'email'=> 'validsuperadmin@email.com',
            'password'=> 'validpassword',
        ])['token'];

        $this->admin_token = $this->postJson('/api/login/', [
            'email'=> 'validadmin@email.com',
            'password'=> 'validpassword',
        ])['token'];

        $this->faker = Faker::create();

        Artisan::call('migrate'); // runs the migration
    }


    /**
     * Rolls back migrations
     */
    public function tearDown(): void
    {
        Artisan::call('migrate:rollback');

        parent::tearDown();
    }
}
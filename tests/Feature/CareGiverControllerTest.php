<?php

namespace Tests\Feature;

use App\Enums\Gender;
use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CareGiverControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_returns_401_without_auth(): void
    {
        $response = $this->postJson('/api/caregivers', [
            'dob' => '1990-01-01',
            'phone_number' => '+11234567890',
            'year_experience' => 5,
            'fee' => 100000,
            'bio' => 'Test bio test',
        ]);

        $response->assertStatus(401);
    }

    public function test_store_creates_caregiver_successfully(): void
    {
        $user = User::create([
            'uid' => 'test-uid-1',
            'full_name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'gender' => Gender::MALE,
            'role' => Role::GIVER,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/caregivers', [
                'dob' => '1990-01-01',
                'phone_number' => '+84987654321',
                'year_experience' => 5,
                'fee' => 100000,
                'bio' => 'Test caregiver bio',
                'skills' => ['First Aid', 'Caregiving'],
                'certifications' => [
                    [
                        'name' => 'CPR Certificate',
                        'issuer' => 'Red Cross',
                        'issue_date' => '2023-01-01',
                        'expiration_date' => '2025-01-01',
                    ],
                ],
                'schedules' => [
                    [
                        'days' => ['Monday', 'Tuesday'],
                        'start_time' => '08:00',
                        'end_time' => '17:00',
                    ],
                ],
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.userUid', 'test-uid-1')
            ->assertJsonPath('data.phoneNumber', '+84987654321')
            ->assertJsonPath('data.yearExperience', 5)
            ->assertJsonPath('data.fee', 100000)
            ->assertJsonPath('data.bio', 'Test caregiver bio')
            ->assertJsonPath('data.fullName', 'Test User')
            ->assertJsonPath('data.email', 'test@example.com');
    }

    public function test_store_returns_422_for_invalid_input(): void
    {
        $user = User::create([
            'uid' => 'test-uid-2',
            'full_name' => 'Test User 2',
            'email' => 'test2@example.com',
            'password' => bcrypt('password'),
            'gender' => Gender::FEMALE,
            'role' => Role::GIVER,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/caregivers', [
                'dob' => 'invalid-date',
                'phone_number' => '',
                'year_experience' => -1,
                'fee' => -100,
                'bio' => '',
            ]);

        $response->assertStatus(422);
    }

    public function test_store_returns_403_if_user_already_has_caregiver_profile(): void
    {
        $user = User::create([
            'uid' => 'test-uid-3',
            'full_name' => 'Test User 3',
            'email' => 'test3@example.com',
            'password' => bcrypt('password'),
            'gender' => Gender::MALE,
            'role' => Role::GIVER,
        ]);

        $user->careGiver()->create([
            'uid' => 'existing-caregiver-uid',
            'user_uid' => $user->uid,
            'dob' => '1990-01-01',
            'phone_number' => '+11111111111',
            'year_experience' => 1,
            'fee' => 50000,
            'bio' => 'Existing bio',
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/caregivers', [
                'dob' => '1995-01-01',
                'phone_number' => '+12222222222',
                'year_experience' => 3,
                'fee' => 80000,
                'bio' => 'New bio text',
            ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'User already has a caregiver profile']);
    }

    public function test_store_returns_400_if_phone_number_already_registered(): void
    {
        $user = User::create([
            'uid' => 'test-uid-4',
            'full_name' => 'Test User 4',
            'email' => 'test4@example.com',
            'password' => bcrypt('password'),
            'gender' => Gender::MALE,
            'role' => Role::GIVER,
        ]);

        $user->careGiver()->create([
            'uid' => 'existing-caregiver-uid-2',
            'user_uid' => $user->uid,
            'dob' => '1990-01-01',
            'phone_number' => '+19999999999',
            'year_experience' => 1,
            'fee' => 50000,
            'bio' => 'Existing bio',
        ]);

        $user2 = User::create([
            'uid' => 'test-uid-5',
            'full_name' => 'Test User 5',
            'email' => 'test5@example.com',
            'password' => bcrypt('password'),
            'gender' => Gender::FEMALE,
            'role' => Role::GIVER,
        ]);

        $response = $this->actingAs($user2, 'sanctum')
            ->postJson('/api/caregivers', [
                'dob' => '1995-01-01',
                'phone_number' => '+19999999999',
                'year_experience' => 3,
                'fee' => 80000,
                'bio' => 'New bio text',
            ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'Phone number already registered']);
    }
}
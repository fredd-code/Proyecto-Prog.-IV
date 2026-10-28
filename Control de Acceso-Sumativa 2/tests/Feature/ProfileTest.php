<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_profile_management(): void
    {
        $this->get(route('profile.show'))->assertRedirect(route('login'));
        $this->put(route('profile.update'))->assertRedirect(route('login'));
        $this->delete(route('profile.destroy'))->assertRedirect(route('login'));
    }

    public function test_user_can_register_and_is_logged_in(): void
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Ana Perez',
            'email' => 'ana@example.com',
            'password' => 'secure-password',
            'password_confirmation' => 'secure-password',
        ]);

        $user = User::where('email', 'ana@example.com')->firstOrFail();

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('users', ['name' => 'Ana Perez', 'email' => 'ana@example.com']);
    }

    public function test_user_can_view_and_update_only_their_profile(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($user)
            ->get(route('profile.show'))
            ->assertOk()
            ->assertSeeText($user->name)
            ->assertDontSeeText($otherUser->email);

        $this->put(route('profile.update'), [
            'name' => 'Nombre actualizado',
            'email' => 'updated@example.com',
            'current_password' => '',
            'password' => '',
            'password_confirmation' => '',
        ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Nombre actualizado',
            'email' => 'updated@example.com',
        ]);
        $this->assertDatabaseHas('users', [
            'id' => $otherUser->id,
            'email' => $otherUser->email,
        ]);
    }

    public function test_user_can_change_password_after_confirming_current_password(): void
    {
        $user = User::factory()->create([
            'password' => 'current-password',
        ]);

        $this->actingAs($user)
            ->put(route('profile.update'), [
                'name' => $user->name,
                'email' => $user->email,
                'current_password' => 'current-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertRedirect(route('profile.show'))
            ->assertSessionDoesntHaveErrors();

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }

    public function test_user_must_confirm_current_password_to_delete_account(): void
    {
        $user = User::factory()->create([
            'password' => 'current-password',
        ]);

        $this->actingAs($user)
            ->delete(route('profile.destroy'), ['password' => 'wrong-password'])
            ->assertSessionHasErrors('password');

        $this->assertDatabaseHas('users', ['id' => $user->id]);

        $this->delete(route('profile.destroy'), ['password' => 'current-password'])
            ->assertRedirect(route('login'))
            ->assertSessionHas('status')
            ->assertSessionDoesntHaveErrors();

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }
}

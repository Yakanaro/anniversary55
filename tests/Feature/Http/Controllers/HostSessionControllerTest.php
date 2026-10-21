<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;

class HostSessionControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config(['invitation.host_password' => 'secret-curtain']);
    }

    public function test_correct_password_opens_guest_list(): void
    {
        $response = $this->post(route('host.login.store'), ['password' => 'secret-curtain']);

        $response->assertRedirect(route('guests.index'));
        $response->assertSessionHas('host_authenticated', true);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $response = $this->from(route('host.login'))->post(route('host.login.store'), ['password' => 'guess']);

        $response->assertRedirect(route('host.login'));
        $response->assertSessionHasErrors(['password' => 'Неверный пароль.']);
        $response->assertSessionMissing('host_authenticated');
    }

    public function test_guest_list_stays_closed_when_no_password_is_configured(): void
    {
        config(['invitation.host_password' => '']);

        $response = $this->post(route('host.login.store'), ['password' => '']);

        $response->assertSessionHasErrors('password');
        $response->assertSessionMissing('host_authenticated');
    }

    public function test_logout_closes_guest_list(): void
    {
        $response = $this->withSession(['host_authenticated' => true])->post(route('host.logout'));

        $response->assertRedirect(route('host.login'));
        $response->assertSessionMissing('host_authenticated');
    }
}

<?php

namespace Tests\Feature\Http\Controllers;

use App\Drink;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvitationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_renders_invitation_with_date_venue_and_drinks(): void
    {
        $response = $this->get(route('invitation'));

        $response->assertOk();
        $response->assertSeeText('Юбилей Ирины');
        $response->assertSeeText('4 декабря 2026 · 16:30');
        $response->assertSeeText('пер. Репникова, д. 10, район Перевалка, Петрозаводск');
        $response->assertSeeText(Drink::Champagne->label());
        $response->assertSeeText('Created by Sergey Druzhinin');
    }

    public function test_does_not_mention_celebrant_surname_or_age(): void
    {
        $response = $this->get(route('invitation'));

        $response->assertDontSeeText('Овсиенко');
        $response->assertDontSeeText('55');
    }

    public function test_links_to_guest_chat_when_configured(): void
    {
        config(['invitation.chat_url' => 'https://t.me/+guest-chat']);

        $response = $this->get(route('invitation'));

        $response->assertSeeText('Чат гостей');
        $response->assertSee('href="https://t.me/+guest-chat"', false);
    }

    public function test_hides_guest_chat_when_link_is_not_configured(): void
    {
        config(['invitation.chat_url' => null]);

        $response = $this->get(route('invitation'));

        $response->assertDontSeeText('Чат гостей');
        $response->assertDontSeeText('В чат гостей');
    }

    public function test_renders_issued_ticket_for_guest_who_signed_up_without_javascript(): void
    {
        $guest = Guest::factory()->create([
            'first_name' => 'Анна',
            'last_name' => 'Петрова',
            'drink' => Drink::Cognac,
        ]);

        $response = $this->withSession(['ticket_guest_id' => $guest->id])->get(route('invitation'));

        $response->assertOk();
        $response->assertSeeText('Допущен');
        $response->assertSeeText('Анна Петрова');
        $response->assertSeeText("№ {$guest->id}");
    }
}

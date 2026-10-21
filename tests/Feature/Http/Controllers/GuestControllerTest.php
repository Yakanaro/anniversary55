<?php

namespace Tests\Feature\Http\Controllers;

use App\Drink;
use App\Models\Guest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_signup_creates_guest_and_returns_ticket_with_201(): void
    {
        $response = $this->postJson(route('guests.store'), [
            'first_name' => 'Анна',
            'last_name' => 'Петрова',
            'drink' => Drink::Champagne->value,
        ]);

        $guest = Guest::sole();
        $response->assertCreated();
        $response->assertExactJson([
            'ticket' => [
                'number' => $guest->id,
                'row' => $guest->row,
                'seat' => $guest->seat,
                'name' => 'Анна Петрова',
                'drink' => 'Шампанское',
            ],
        ]);
        $this->assertDatabaseHas('guests', [
            'first_name' => 'Анна',
            'last_name' => 'Петрова',
            'drink' => Drink::Champagne->value,
        ]);
    }

    public function test_signing_up_twice_under_the_same_name_returns_the_same_ticket(): void
    {
        $first = $this->postJson(route('guests.store'), [
            'first_name' => 'Анна',
            'last_name' => 'Петрова',
            'drink' => Drink::Champagne->value,
        ]);

        $second = $this->postJson(route('guests.store'), [
            'first_name' => '  анна ',
            'last_name' => 'ПЕТРОВА',
            'drink' => Drink::Vodka->value,
        ]);

        $second->assertOk();
        $second->assertJsonPath('ticket.number', $first->json('ticket.number'));
        $second->assertJsonPath('ticket.drink', 'Водка');
        $this->assertDatabaseCount('guests', 1);
    }

    public function test_yo_and_ye_are_treated_as_the_same_name(): void
    {
        Guest::factory()->create(['first_name' => 'Фёдор', 'last_name' => 'Иванов']);

        $this->postJson(route('guests.store'), [
            'first_name' => 'Федор',
            'last_name' => 'Иванов',
            'drink' => Drink::Whisky->value,
        ])->assertOk();

        $this->assertDatabaseCount('guests', 1);
    }

    public function test_different_guests_with_one_surname_each_get_their_own_ticket(): void
    {
        Guest::factory()->create(['first_name' => 'Анна', 'last_name' => 'Петрова']);

        $this->postJson(route('guests.store'), [
            'first_name' => 'Ольга',
            'last_name' => 'Петрова',
            'drink' => Drink::RedWine->value,
        ])->assertCreated();

        $this->assertDatabaseCount('guests', 2);
    }

    public function test_signup_without_javascript_redirects_back_to_ticket(): void
    {
        $response = $this->post(route('guests.store'), [
            'first_name' => 'Анна',
            'last_name' => 'Петрова',
            'drink' => Drink::RedWine->value,
        ]);

        $response->assertRedirect(route('invitation').'#rsvp');
        $response->assertSessionHas('ticket_guest_id', Guest::sole()->id);
    }

    public function test_empty_signup_returns_422_with_russian_messages(): void
    {
        $response = $this->postJson(route('guests.store'), []);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors([
            'first_name' => 'Впишите имя.',
            'last_name' => 'Впишите фамилию.',
            'drink' => 'Выберите напиток.',
        ]);
        $this->assertDatabaseCount('guests', 0);
    }

    public function test_signup_rejects_drink_outside_the_list(): void
    {
        $response = $this->postJson(route('guests.store'), [
            'first_name' => 'Анна',
            'last_name' => 'Петрова',
            'drink' => 'absinthe',
        ]);

        $response->assertJsonValidationErrors(['drink' => 'Выберите напиток из списка.']);
        $this->assertDatabaseCount('guests', 0);
    }

    public function test_signup_rejects_name_longer_than_60_characters(): void
    {
        $response = $this->postJson(route('guests.store'), [
            'first_name' => str_repeat('а', 61),
            'last_name' => 'Петрова',
            'drink' => Drink::Vodka->value,
        ]);

        $response->assertJsonValidationErrors(['first_name' => 'Имя должно быть не длиннее 60 символов.']);
    }

    public function test_guest_list_redirects_to_login_without_host_password(): void
    {
        $response = $this->get(route('guests.index'));

        $response->assertRedirect(route('host.login'));
    }

    public function test_guest_list_shows_guests_and_drink_totals_to_host(): void
    {
        Guest::factory()->count(2)->create(['drink' => Drink::Champagne]);
        Guest::factory()->create(['first_name' => 'Олег', 'last_name' => 'Смирнов', 'drink' => Drink::Cognac]);

        $response = $this->withSession(['host_authenticated' => true])->get(route('guests.index'));

        $response->assertOk();
        $response->assertSeeText('Олег Смирнов');
        $response->assertViewHas('drinkCounts', fn ($counts): bool => $counts->all() === [
            'Шампанское' => 2,
            'Коньяк' => 1,
        ]);
    }

    public function test_host_can_remove_guest(): void
    {
        $guest = Guest::factory()->create();

        $response = $this->withSession(['host_authenticated' => true])->delete(route('guests.destroy', $guest));

        $response->assertRedirect(route('guests.index'));
        $this->assertModelMissing($guest);
    }

    public function test_visitor_cannot_remove_guest(): void
    {
        $guest = Guest::factory()->create();

        $response = $this->delete(route('guests.destroy', $guest));

        $response->assertRedirect(route('host.login'));
        $this->assertModelExists($guest);
    }
}

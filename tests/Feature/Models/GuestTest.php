<?php

namespace Tests\Feature\Models;

use App\Models\Guest;
use PHPUnit\Framework\Attributes\TestWith;
use Tests\TestCase;

class GuestTest extends TestCase
{
    #[TestWith([1, 1, 1])]
    #[TestWith([10, 1, 10])]
    #[TestWith([11, 2, 1])]
    #[TestWith([25, 3, 5])]
    public function test_seats_are_handed_out_ten_per_row_in_signup_order(int $ticketNumber, int $row, int $seat): void
    {
        $guest = Guest::factory()->make(['id' => $ticketNumber]);

        $this->assertSame($row, $guest->row);
        $this->assertSame($seat, $guest->seat);
    }
}

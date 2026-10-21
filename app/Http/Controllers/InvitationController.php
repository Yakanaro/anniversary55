<?php

namespace App\Http\Controllers;

use App\Drink;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class InvitationController extends Controller
{
    /**
     * Show the invitation page.
     */
    public function __invoke(Request $request): View
    {
        $ticketGuestId = $request->session()->get('ticket_guest_id');

        return view('invitation', [
            'startsAt' => Carbon::parse(config('invitation.starts_at'), config('invitation.timezone')),
            'drinks' => Drink::cases(),
            'ticketGuest' => $ticketGuestId ? Guest::find($ticketGuestId) : null,
        ]);
    }
}

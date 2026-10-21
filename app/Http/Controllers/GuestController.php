<?php

namespace App\Http\Controllers;

use App\Drink;
use App\Http\Requests\StoreGuestRequest;
use App\Models\Guest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GuestController extends Controller
{
    /**
     * Show the host who is coming and what to pour.
     */
    public function index(): View
    {
        $guests = Guest::query()->orderBy('id')->get();

        $drinkCounts = collect(Drink::cases())
            ->mapWithKeys(fn (Drink $drink): array => [
                $drink->label() => $guests->where('drink', $drink)->count(),
            ])
            ->filter();

        return view('guests.index', [
            'guests' => $guests,
            'drinkCounts' => $drinkCounts,
        ]);
    }

    /**
     * Sign a guest up and issue their ticket. Signing up again under the same
     * name hands back the same ticket, with the drink updated to the new choice.
     */
    public function store(StoreGuestRequest $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validated();

        $guest = Guest::query()->createOrFirst(
            ['name_key' => Guest::nameKey($validated['first_name'], $validated['last_name'])],
            $validated,
        );

        if (! $guest->wasRecentlyCreated) {
            $guest->update(['drink' => $validated['drink']]);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ticket' => [
                    'number' => $guest->id,
                    'row' => $guest->row,
                    'seat' => $guest->seat,
                    'name' => $guest->full_name,
                    'drink' => $guest->drink->label(),
                ],
            ], $guest->wasRecentlyCreated ? 201 : 200);
        }

        return redirect()
            ->to(route('invitation').'#rsvp')
            ->with('ticket_guest_id', $guest->id);
    }

    /**
     * Remove a guest from the list.
     */
    public function destroy(Guest $guest): RedirectResponse
    {
        $guest->delete();

        return redirect()
            ->route('guests.index')
            ->with('status', "{$guest->full_name} удалён(а) из списка.");
    }
}

<x-layout title="Список гостей">
    <main class="wrap host">
        <div class="host-head">
            <h1>Список гостей</h1>
            <form method="POST" action="{{ route('host.logout') }}">
                @csrf
                <button class="link-btn" type="submit">Выйти</button>
            </form>
        </div>

        @if (session('status'))
            <p class="notice" role="status">{{ session('status') }}</p>
        @endif

        <div class="tally">
            <div><small>Всего гостей</small><b>{{ $guests->count() }}</b></div>
            @foreach ($drinkCounts as $label => $count)
                <div><small>{{ $label }}</small><b>{{ $count }}</b></div>
            @endforeach
        </div>

        <div class="table-wrap">
            @if ($guests->isEmpty())
                <p class="empty">Пока никто не записался. Отправьте гостям ссылку на приглашение: {{ route('invitation') }}</p>
            @else
                <table class="guest-table">
                    <thead>
                        <tr>
                            <th>Билет</th>
                            <th>Гость</th>
                            <th>Напиток</th>
                            <th>Ряд / место</th>
                            <th>Записался</th>
                            <th><span class="sr-only">Действия</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($guests as $guest)
                            <tr>
                                <td data-label="Билет">№ {{ $guest->id }}</td>
                                <td data-label="Гость">{{ $guest->full_name }}</td>
                                <td data-label="Напиток">{{ $guest->drink->label() }}</td>
                                <td data-label="Ряд / место">{{ $guest->row }} / {{ $guest->seat }}</td>
                                <td data-label="Записался">{{ $guest->created_at->timezone(config('invitation.timezone'))->format('d.m H:i') }}</td>
                                <td>
                                    <form method="POST" action="{{ route('guests.destroy', $guest) }}" onsubmit="return confirm({{ Js::from('Удалить '.$guest->full_name.' из списка?') }})">
                                        @csrf
                                        @method('DELETE')
                                        <button class="link-btn" type="submit">Удалить</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </main>
</x-layout>

@php
    $venue = config('invitation.venue');
    $date = $startsAt->locale('ru');
    $mapWidgetUrl = 'https://yandex.ru/map-widget/v1/?'.http_build_query([
        'll' => $venue['longitude'].','.$venue['latitude'],
        'z' => 16,
        'mode' => 'search',
        'text' => $venue['search_query'],
    ]);
    $yandexMapsUrl = 'https://yandex.ru/maps/?'.http_build_query(['text' => $venue['search_query']]);
    $twoGisUrl = 'https://2gis.ru/petrozavodsk/search/'.rawurlencode('переулок Репникова, 10');
@endphp

<x-layout
    title="Юбилей Ирины"
    description="Ирина приглашает на свой юбилей {{ $date->translatedFormat('j F') }} в {{ $date->format('H:i') }}: банкетный зал, {{ $venue['address'] }}, {{ $venue['city'] }}."
>
    <div class="drapes" aria-hidden="true"><div class="drape l"></div><div class="drape r"></div></div>
    <div class="spot" aria-hidden="true"></div>
    <div class="valance" aria-hidden="true"></div>

    <main>
        <header class="wrap">
            <div class="marquee" data-marquee>
                <div class="bulbs" data-bulbs aria-hidden="true"></div>
                <div class="presents">Только один сеанс</div>
                <h1>Юбилей Ирины</h1>
                <div class="sub">{{ $date->translatedFormat('j F Y') }} · {{ $date->format('H:i') }}</div>
            </div>
        </header>

        <section class="wrap">
            <div class="poster">
                <div class="frame">
                    <img src="{{ asset('images/irina.jpg') }}" alt="Ирина" width="1000" height="1500">
                    <div class="leader" aria-hidden="true"><div class="v"></div><span data-leader-number>3</span></div>
                    <div class="perf l"></div><div class="perf r"></div>
                </div>
                <div class="billing">
                    <div class="role">В главной роли</div>
                    <div class="star">Ирина</div>
                    <div class="film">{{ $date->format('d.m') }}<small>Премьера года</small></div>
                    <div class="meta"><span class="chip">Семейный</span><span class="chip">Праздник</span><span class="chip">{{ $venue['district'] }}</span></div>
                </div>
            </div>
        </section>

        <section class="wrap credits">
            <div class="k">Обращение к зрителям</div>
            <p>Раз в год у меня есть прекрасный повод собрать всех, кого люблю. Этот раз — <b>4 декабря 2026</b>. Жду Вас в банкетном зале на переулке Репникова, д. 10 (район Перевалка) в <b>16 часов 30 минут</b>.</p>
            <div class="session">
                <div><small>Сеанс</small><b>{{ $date->translatedFormat('j F') }}</b></div>
                <div><small>Начало</small><b>{{ $date->format('H:i') }}</b></div>
                <div><small>Зал</small><b>Банкетный зал</b></div>
            </div>
            <div class="count">До начала сеанса: <span data-countdown="{{ $startsAt->toIso8601String() }}">—</span></div>
        </section>

        <section class="wrap" id="map">
            <h2>Как найти зал</h2>
            <p class="lead">{{ $venue['address'] }}, район {{ $venue['district'] }}, {{ $venue['city'] }}. Постройте маршрут в навигаторе.</p>
            <div class="mapbox">
                <iframe src="{{ $mapWidgetUrl }}" title="Карта: {{ $venue['search_query'] }}" loading="lazy" allowfullscreen></iframe>
            </div>
            <div class="btns">
                <a class="btn" href="{{ $yandexMapsUrl }}" target="_blank" rel="noopener">Яндекс Карты</a>
                <a class="btn o" href="{{ $twoGisUrl }}" target="_blank" rel="noopener">2ГИС</a>
            </div>
        </section>

        <section class="wrap" id="rsvp">
            <h2 class="center">Получите свой билет</h2>
            <p class="lead center">Впишите имя, фамилию и напиток. Мы выдадим вам именной билет на премьеру.</p>

            <div @class(['ticket', 'torn' => $ticketGuest]) data-ticket>
                <div class="t-main">
                    <div class="hd">
                        <span>Пригласительный билет</span>
                        <span data-ticket-number>{{ $ticketGuest ? '№ '.$ticketGuest->id : $venue['city'] }}</span>
                    </div>

                    <form class="rsvp-form" method="POST" action="{{ route('guests.store') }}" novalidate @if ($ticketGuest) hidden @endif>
                        @csrf
                        <div class="field">
                            <label for="first_name">Имя</label>
                            <input id="first_name" name="first_name" value="{{ old('first_name') }}" maxlength="60" autocomplete="given-name" placeholder="Анна" required>
                        </div>
                        <div class="field">
                            <label for="last_name">Фамилия</label>
                            <input id="last_name" name="last_name" value="{{ old('last_name') }}" maxlength="60" autocomplete="family-name" placeholder="Петрова" required>
                        </div>
                        <div class="field full">
                            <label for="drink">Напиток в зале</label>
                            <select id="drink" name="drink" required>
                                <option value="">Выберите напиток</option>
                                @foreach ($drinks as $drink)
                                    <option value="{{ $drink->value }}" @selected(old('drink') === $drink->value)>{{ $drink->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                        <p class="err" data-error role="alert">{{ $errors->first() }}</p>
                        <button class="btn" type="submit">Получить билет</button>
                    </form>

                    <div data-issued @if (! $ticketGuest) hidden @endif>
                        <div class="stamp">Допущен</div>
                        <p class="guest-name" data-ticket-name>{{ $ticketGuest?->full_name }}</p>
                        <div class="issued-grid">
                            <div><small>Ряд</small><b data-ticket-row>{{ $ticketGuest?->row }}</b></div>
                            <div><small>Место</small><b data-ticket-seat>{{ $ticketGuest?->seat }}</b></div>
                            <div><small>Напиток</small><b data-ticket-drink>{{ $ticketGuest?->drink->label() }}</b></div>
                        </div>
                        <a class="again" href="{{ route('invitation') }}#rsvp" data-again>Оформить ещё билет</a>
                    </div>
                </div>
                <div class="t-stub"><small>Контроль</small><b>★</b><small>{{ $date->format('d.m') }} · {{ $date->format('H:i') }}</small></div>
            </div>
        </section>
    </main>
</x-layout>

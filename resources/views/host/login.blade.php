<x-layout title="Список гостей · вход">
    <main class="wrap host">
        <div class="login-card">
            <div class="host-head"><h1>Список гостей</h1></div>
            <p class="lead">Введите пароль, чтобы посмотреть, кто придёт и что будет пить.</p>
            <form method="POST" action="{{ route('host.login.store') }}">
                @csrf
                <div class="field">
                    <label for="password">Пароль</label>
                    <input id="password" name="password" type="password" autocomplete="current-password" required autofocus>
                </div>
                <p class="err" role="alert">{{ $errors->first('password') }}</p>
                <button class="btn" type="submit">Войти</button>
            </form>
        </div>
    </main>
</x-layout>

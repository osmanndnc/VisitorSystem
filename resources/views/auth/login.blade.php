<x-guest-layout>
    <!-- @push('head')
        <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
    @endpush -->
    <div class="mb-6 text-center">
        <h2 class="text-2xl font-bold text-gray-800 dark:text-white">Hoş Geldiniz!</h2>
        <p class="text-gray-500 dark:text-gray-300 text-sm mt-1">Lütfen giriş bilgilerinizi girin</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if($errors->any())
        <div class="mb-4 text-red-600 font-semibold text-sm text-center">
            {{ $errors->first() }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Username -->
        <div class="mb-4">
            <x-input-label for="username" :value="__('Kullanıcı Adı')" />
            <x-text-input
                id="username"
                class="block mt-1 w-full"
                type="text"
                name="username"
                :value="old('username')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('username')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mb-4">
            <x-input-label for="password" :value="__('Şifre')" />
            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me + Forgot Password -->
        <div class="flex items-center justify-between mb-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" name="remember">
                <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Beni hatırla') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-200" href="{{ route('password.request') }}">
                    {{ __('Şifremi unuttum?') }}
                </a>
            @endif
        </div>

        <!-- Login Button -->
        <div>
            <x-primary-button class="w-full justify-center py-2 text-base">
                {{ __('Giriş Yap') }}
            </x-primary-button>
        </div>
    </form>
<script>
    // if (window.history.replaceState) {
    //     window.history.replaceState(null, null, window.location.href);
    // }

    // window.onload = function() {
    //     if (performance.navigation.type === 2) {
    //         // Tarayıcı geri tuşu ile gelindiyse login sayfasına yönlendir
    //         window.location.href = "{{ route('login') }}";
    //     }
    // };
    // Sayfa yüklendiğinde geçmişteki login olmayan sayfayı sil
    if (window.history && window.history.pushState) {
        window.history.pushState(null, null, window.location.href);
        window.onpopstate = function () {
            window.location.href = "{{ route('login') }}";
        };
    }
</script>
</x-guest-layout>

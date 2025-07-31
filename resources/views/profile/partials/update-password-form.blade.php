<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Şifre Güncelle') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Hesabınızın güvenli kalması için uzun ve rastgele bir şifre kullandığından emin olun.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Mevcut Şifre')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Yeni Şifre')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            
            <!-- Hata mesajlarını Türkçeleştir -->
            @if($errors->updatePassword->has('password'))
                <p class="text-red-500 text-sm mt-2">
                    @if($errors->updatePassword->first('password') === 'The password field must be at least 8 characters.')
                        Şifre en az 8 karakter olmalıdır.
                    @elseif($errors->updatePassword->first('password') === 'The password field confirmation does not match.')
                        Şifre onayı eşleşmiyor.
                    @else
                        {{ $errors->updatePassword->first('password') }}
                    @endif
                </p>
            @endif
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Şifre Onayla')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            
            @if($errors->updatePassword->has('password_confirmation'))
                <p class="text-red-500 text-sm mt-2">
                    @if($errors->updatePassword->first('password_confirmation') === 'The password field confirmation does not match.')
                        Şifre onayı eşleşmiyor.
                    @else
                        {{ $errors->updatePassword->first('password_confirmation') }}
                    @endif
                </p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Kaydet') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ __('Kaydedildi.') }}</p>
            @endif
        </div>
    </form>
</section>
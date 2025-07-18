@if (session('success'))
    <div class="mb-4 text-green-600">
        {{ session('success') }}
    </div>
@endif
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Ziyaretçi Kaydı Oluştur
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="POST" action="{{ route('security.store') }}">
                    @csrf

                    <h3 class="text-lg font-medium mb-4">Ziyaretçi Bilgileri</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" :value="'Ad Soyad'" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="tc_no" :value="'T.C. Kimlik No'" />
                            <x-text-input id="tc_no" name="tc_no" type="text" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="phone" :value="'Telefon'" />
                            <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="plate" :value="'Plaka'" />
                            <x-text-input id="plate" name="plate" type="text" class="mt-1 block w-full" />
                        </div>
                    </div>

                    <h3 class="text-lg font-medium mt-6 mb-4">Ziyaret Bilgisi</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="entry_time" :value="'Giriş Saati'" />
                            <x-text-input id="entry_time" name="entry_time" type="datetime-local" class="mt-1 block w-full" required />
                        </div>

                        <div>
                            <x-input-label for="person_to_visit" :value="'Ziyaret Edilen Kişi'" />
                            <x-text-input id="person_to_visit" name="person_to_visit" type="text" class="mt-1 block w-full" required />
                        </div>

                        <div class="mt-4">
                            <label for="purpose" class="block font-medium text-sm text-gray-700">Ziyaret Sebebi</label>
                            <textarea name="purpose" id="purpose" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
                        </div>
                    </div>

                    <div class="mt-6">
                        <x-primary-button>KAYDET</x-primary-button>
                    </div>

                </form>

            </div>
        </div>
    </div>
</x-app-layout>

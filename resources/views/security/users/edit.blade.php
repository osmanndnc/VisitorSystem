<!-- <x-app-layout>
    <style>
        html {
            zoom: 80%;
        }
        body {
            background: #f1f5f9;
        }
    </style>
    <div class="max-w-3xl mx-auto py-6">
        <h2 class="text-xl font-bold mb-4">Kullanıcıyı Düzenle</h2>

        <form method="POST" action="{{ route('security.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium">Kullanıcı Adı</label>
                <input type="text" name="username" value="{{ old('username', $user->username) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Rol</label>
                <input type="text" name="role" value="{{ old('role', $user->role) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Durum</label>
                <select name="is_active" class="w-full border rounded px-3 py-2">
                    <option value="1" {{ $user->is_active ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ !$user->is_active ? 'selected' : '' }}>Pasif</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium">Yeni Şifre <span class="text-sm text-gray-500">(Boş bırakılırsa değişmez)</span></label>
                <input type="password" name="password" class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Güncelle</button>
            </div>
        </form>
    </div>
</x-app-layout> -->

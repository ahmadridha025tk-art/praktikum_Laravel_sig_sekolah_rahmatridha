@php $cls = 'w-full border rounded px-3 py-2'; @endphp

<div class="bg-white rounded shadow p-6 max-w-2xl space-y-4">
    <div>
        <label for="name" class="block font-medium">Nama</label>
        <input type="text" id="name" name="name"
               value="{{ old('name', $user->name) }}" class="{{ $cls }}">
        @error('name')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="email" class="block font-medium">Email</label>
        <input type="email" id="email" name="email"
               value="{{ old('email', $user->email) }}" class="{{ $cls }}">
        @error('email')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="role" class="block font-medium">Role</label>
        <select id="role" name="role" class="{{ $cls }}">
            @foreach ([
                'admin' => 'Admin',
                'dinas' => 'Dinas Pendidikan',
                'sekolah' => 'Sekolah'
            ] as $nilai => $label)
                <option value="{{ $nilai }}"
                    @selected(old('role', $user->role ?? 'sekolah') === $nilai)>
                    {{ $label }}
                </option>
            @endforeach
        </select>
        @error('role')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div id="blok-sekolah">
        <label for="sekolah_id" class="block font-medium">Sekolah</label>
        <select id="sekolah_id" name="sekolah_id" class="{{ $cls }}">
            <option value="">-- Pilih sekolah --</option>
            @foreach ($sekolahs as $s)
                <option value="{{ $s->id }}"
                    @selected((int) old('sekolah_id', $user->sekolah_id) === $s->id)>
                    {{ $s->npsn }} - {{ $s->nama }}
                </option>
            @endforeach
        </select>
        @error('sekolah_id')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password" class="block font-medium">Kata Sandi</label>
        <input type="password" id="password" name="password"
               class="{{ $cls }}" autocomplete="new-password">

        @if ($user->exists)
            <p class="text-gray-500 text-sm">
                Kosongkan jika tidak diganti.
            </p>
        @endif

        @error('password')
            <p class="text-red-600 text-sm">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password_confirmation" class="block font-medium">
            Ulangi Kata Sandi
        </label>
        <input type="password" id="password_confirmation"
               name="password_confirmation" class="{{ $cls }}"
               autocomplete="new-password">
    </div>

    <div class="flex gap-3 pt-2">
        <button class="px-5 py-2 bg-blue-700 text-white rounded">
            Simpan
        </button>

        <a href="{{ route('admin.users.index') }}"
           class="px-5 py-2 bg-gray-300 rounded">
            Batal
        </a>
    </div>
</div>

@push('scripts')
<script>
    const role = document.getElementById('role');
    const blok = document.getElementById('blok-sekolah');

    function aturBlokSekolah() {
        blok.classList.toggle('hidden', role.value !== 'sekolah');
    }

    role.addEventListener('change', aturBlokSekolah);
    aturBlokSekolah();
</script>
@endpush

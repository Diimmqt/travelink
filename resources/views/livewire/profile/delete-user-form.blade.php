<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

// Komponen Volt untuk bagian hapus akun pada halaman profil pengguna
new class extends Component
{
    // Properti untuk menampung password konfirmasi sebelum akun dihapus permanen
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     *
     * Memvalidasi password pengguna saat ini, melakukan logout, menghapus record user
     * dari database, lalu mengarahkan ke halaman utama (/).
     *
     * @param  Logout  $logout  Instance action class untuk logout sebelum penghapusan akun.
     */
    public function deleteUser(Logout $logout): void
    {
        // Pastikan password yang dimasukkan cocok dengan password akun yang sedang login
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        // Jalankan logout pada user yang sedang login via tap(), lalu hapus data user tersebut
        tap(Auth::user(), $logout(...))->delete();

        // Arahkan kembali ke beranda publik setelah akun terhapus
        $this->redirect('/', navigate: true);
    }
}; ?>

{{-- Seksi hapus akun pada halaman profil --}}
<section class="space-y-6">
    <header>
        <h2 class="text-base font-medium text-red-400" style="letter-spacing: -0.02em;">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-xs" style="color: var(--ds-fog);">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan data di dalamnya akan dihapus secara permanen.') }}
        </p>
    </header>

    {{-- Tombol pemicu yang mengirim event Alpine 'open-modal' untuk membuka dialog konfirmasi --}}
    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-4 py-2 text-xs font-semibold rounded-pill text-red-300 border border-red-500/30 bg-red-500/10 hover:bg-red-500/20 transition"
    >{{ __('Hapus Akun Saya') }}</button>

    {{-- Modal konfirmasi penghapusan akun; otomatis terbuka kembali jika terdapat error validasi --}}
    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        {{-- Form di dalam modal yang memanggil method deleteUser() saat disubmit --}}
        <form wire:submit="deleteUser" class="p-6 space-y-4" style="background: var(--ds-graphite); color: var(--ds-snow); border: 0.5px solid rgba(255,255,255,0.1); border-radius: 12px;">

            <h2 class="text-base font-medium" style="color: var(--ds-snow);">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="text-xs leading-relaxed" style="color: var(--ds-fog);">
                {{ __('Setelah akun dihapus, seluruh data tidak dapat dipulihkan kembali. Masukkan password Anda untuk mengonfirmasi.') }}
            </p>

            {{-- Input password konfirmasi --}}
            <div class="mt-4">
                <label for="password" class="sr-only">{{ __('Password') }}</label>

                <input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="ds-input"
                    placeholder="{{ __('Masukkan Password') }}"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2 text-xs text-red-400" />
            </div>

            {{-- Tombol aksi modal: Batal (menutup modal via event 'close') atau Hapus Akun --}}
            <div class="mt-6 flex justify-end gap-3 pt-3 ds-divider border-t">
                <button type="button" x-on:click="$dispatch('close')" class="ds-btn-ghost text-xs px-4 py-2">
                    {{ __('Batal') }}
                </button>

                <button type="submit" class="px-4 py-2 text-xs font-semibold rounded-pill text-white bg-red-600 hover:bg-red-700 transition">
                    {{ __('Hapus Akun') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>

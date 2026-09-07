<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-base font-medium text-red-400" style="letter-spacing: -0.02em;">
            {{ __('Hapus Akun') }}
        </h2>

        <p class="mt-1 text-xs" style="color: var(--ds-fog);">
            {{ __('Setelah akun Anda dihapus, semua sumber daya dan data di dalamnya akan dihapus secara permanen.') }}
        </p>
    </header>

    <button
        type="button"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="px-4 py-2 text-xs font-semibold rounded-pill text-red-300 border border-red-500/30 bg-red-500/10 hover:bg-red-500/20 transition"
    >{{ __('Hapus Akun Saya') }}</button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6 space-y-4" style="background: var(--ds-graphite); color: var(--ds-snow); border: 0.5px solid rgba(255,255,255,0.1); border-radius: 12px;">

            <h2 class="text-base font-medium" style="color: var(--ds-snow);">
                {{ __('Apakah Anda yakin ingin menghapus akun Anda?') }}
            </h2>

            <p class="text-xs leading-relaxed" style="color: var(--ds-fog);">
                {{ __('Setelah akun dihapus, seluruh data tidak dapat dipulihkan kembali. Masukkan password Anda untuk mengonfirmasi.') }}
            </p>

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

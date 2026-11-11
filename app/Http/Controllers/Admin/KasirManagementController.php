<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Controller CRUD untuk mengelola akun petugas kasir loket oleh Admin.
 */
class KasirManagementController extends Controller
{
    /**
     * Menampilkan daftar semua user yang memiliki role kasir.
     * Data diurutkan dari yang terbaru dan dipaginasi 10 akun per halaman.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Ambil user dengan role 'kasir' saja agar akun penumpang dan admin tidak ikut tampil
        $kasirUsers = User::where('role', 'kasir')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.kasir.index', compact('kasirUsers'));
    }

    /**
     * Menampilkan formulir pendaftaran akun kasir baru.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('admin.kasir.create');
    }

    /**
     * Menyimpan akun kasir baru ke database.
     *
     * @param  \Illuminate\Http\Request  $request  Data input form pendaftaran kasir
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        // Validasi input dengan pesan error kustom berbahasa Indonesia
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama kasir wajib diisi.',
            'email.required' => 'Email kasir wajib diisi.',
            'email.unique' => 'Email ini sudah terdaftar di sistem.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Simpan user baru dengan role dikunci sebagai 'kasir' dan email langsung dianggap terverifikasi
        User::create([
            'nama' => $validated['name'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'role' => 'kasir', // Dikunci paksa sebagai kasir
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.kasir.index')
            ->with('success', 'Akun kasir ' . $validated['name'] . ' berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit data akun kasir.
     *
     * @param  int|string  $id  ID user kasir yang akan diubah
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
        // Pastikan ID yang dicari benar-benar memiliki role kasir (404 jika bukan kasir)
        $kasir = User::where('id', $id)->where('role', 'kasir')->firstOrFail();

        return view('admin.kasir.edit', compact('kasir'));
    }

    /**
     * Memperbarui data akun kasir atau mereset password.
     *
     * @param  \Illuminate\Http\Request  $request  Data perubahan dari form edit
     * @param  int|string                $id       ID user kasir yang diperbarui
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        // Cari akun kasir target; gagalkan dengan 404 jika tidak ditemukan atau bukan role kasir
        $kasir = User::where('id', $id)->where('role', 'kasir')->firstOrFail();

        // Validasi input; Rule::unique mengabaikan ID kasir ini sendiri agar email lama tetap valid saat tidak diubah
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($kasir->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'name.required' => 'Nama kasir wajib diisi.',
            'email.required' => 'Email kasir wajib diisi.',
            'email.unique' => 'Email ini sudah digunakan oleh pengguna lain.',
            'password.min' => 'Password baru minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        // Siapkan data profil yang akan diperbarui
        $updateData = [
            'nama' => $validated['name'],
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ];

        // Jika password diisi, update password baru
        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $kasir->update($updateData);

        return redirect()->route('admin.kasir.index')
            ->with('success', 'Data akun kasir ' . $kasir->nama . ' berhasil diperbarui.');
    }

    /**
     * Menghapus akun kasir.
     *
     * @param  int|string  $id  ID user kasir yang akan dihapus
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        // Pastikan hanya akun dengan role kasir yang dapat dihapus
        $kasir = User::where('id', $id)->where('role', 'kasir')->firstOrFail();

        $nama = $kasir->nama;
        $kasir->delete();

        return redirect()->route('admin.kasir.index')
            ->with('success', 'Akun kasir ' . $nama . ' berhasil dihapus dari sistem.');
    }
}


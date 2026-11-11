{{-- Menggunakan layout utama halaman admin --}}
@extends('layouts.admin')

{{-- Menentukan judul halaman manajemen akun kasir --}}
@section('title', 'Manajemen Akun Kasir')

@section('content')
<div class="space-y-6">
    <!-- Header Halaman & Tombol Tambah -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-brex-ink tracking-brex-24">Manajemen Akun Kasir</h1>
            <p class="text-sm text-brex-pewter mt-1">Daftar staf loket (Kasir) yang memiliki hak akses operasional tiket.</p>
        </div>
        {{-- Tombol menuju halaman pembuatan akun kasir baru --}}
        <a href="{{ route('admin.kasir.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brex-ember text-white text-sm font-medium rounded-brex hover:bg-[#e04f00] transition duration-150 shadow-none self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            <span>Tambah Kasir Baru</span>
        </a>
    </div>

    <!-- Alert Notifikasi -->
    {{-- Menampilkan pesan sukses dari session flash jika operasi simpan/edit/hapus berhasil --}}
    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-brex text-sm flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <!-- Tabel Data Kasir -->
    <div class="bg-brex-paper border border-brex-mist rounded-brex shadow-none overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-brex-fog border-b border-brex-mist text-[12px] font-semibold text-brex-pewter uppercase tracking-wider">
                        <th class="py-3.5 px-6">Kasir</th>
                        <th class="py-3.5 px-6">Email</th>
                        <th class="py-3.5 px-6">No. Telepon / HP</th>
                        <th class="py-3.5 px-6">Status Verifikasi</th>
                        <th class="py-3.5 px-6">Terdaftar Sejak</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-brex-mist text-sm text-brex-graphite">
                    {{-- Perulangan daftar akun kasir; jika kosong akan menampilkan blok @empty --}}
                    @forelse ($kasirUsers as $kasir)
                        <tr class="hover:bg-brex-fog/50 transition-colors">
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    {{-- Avatar inisial huruf pertama nama kasir dalam huruf kapital --}}
                                    <div class="w-9 h-9 rounded-full bg-blue-100 text-blue-700 font-semibold flex items-center justify-center text-sm flex-shrink-0">
                                        {{ strtoupper(substr($kasir->nama, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-brex-ink">{{ $kasir->nama }}</p>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            Role: kasir
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 text-brex-ink">{{ $kasir->email }}</td>
                            <td class="py-4 px-6">{{ $kasir->phone ?? '-' }}</td>
                            <td class="py-4 px-6">
                                {{-- Cek apakah email kasir sudah terverifikasi atau belum --}}
                                @if ($kasir->email_verified_at)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-brex-chip">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded-brex-chip">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Belum Verifikasi
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-xs text-brex-pewter">
                                {{ $kasir->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="py-4 px-6 text-right space-x-2 whitespace-nowrap">
                                <a href="{{ route('admin.kasir.edit', $kasir->id) }}" class="inline-flex items-center px-3 py-1.5 border border-brex-mist text-xs font-medium text-brex-ink rounded-brex hover:bg-brex-fog transition-colors">
                                    Edit / Reset Password
                                </a>
                                {{-- Form hapus akun kasir dengan dialog konfirmasi browser sebelum dikirim --}}
                                <form action="{{ route('admin.kasir.destroy', $kasir->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun kasir {{ $kasir->nama }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-rose-50 text-rose-600 border border-rose-200 text-xs font-medium rounded-brex hover:bg-rose-100 transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-brex-pewter">
                                <div class="max-w-sm mx-auto space-y-2">
                                    <p class="font-medium text-brex-ink">Belum Ada Akun Kasir</p>
                                    <p class="text-xs">Klik tombol "Tambah Kasir Baru" di atas untuk menambahkan staf loket pertama Anda.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Tampilkan navigasi paginasi jika jumlah data melebihi batas per halaman --}}
        @if ($kasirUsers->hasPages())
            <div class="p-4 border-t border-brex-mist">
                {{ $kasirUsers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection


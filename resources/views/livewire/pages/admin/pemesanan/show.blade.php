<?php

use function Livewire\Volt\{layout, title, state, mount, rules};
use App\Models\Pemesanan;
use Carbon\Carbon;

layout('layouts.app');
title('Detail Pemesanan');

state([
    'pemesanan' => null,
    'catatanAdmin' => '',
]);

mount(function (Pemesanan $pemesanan) {
    $this->pemesanan = $pemesanan->load(['pelanggan', 'kendaraanUnit.kendaraan']);
    $this->catatanAdmin = $pemesanan->catatan ?? '';
});

$approve = function () {
    if ($this->pemesanan->status_pemesanan !== 'menunggu_konfirmasi') {
        return;
    }

    $this->pemesanan->update([
        'status_pemesanan' => 'disetujui',
        'catatan' => $this->catatanAdmin
    ]);

    // Update status unit logic
    $today = Carbon::today();
    $startDate = Carbon::parse($this->pemesanan->tanggal_mulai);
    $unit = $this->pemesanan->kendaraanUnit;

    if ($startDate->isSameDay($today) || $startDate->isPast() || $startDate->diffInDays($today) <= 1) {
        $unit->update(['status_unit' => 'disewa']);
    } else {
        $unit->update(['status_unit' => 'dibooking']);
    }

    $this->dispatch('swal:toast', title: 'Pemesanan disetujui.', icon: 'success');
};

$reject = function () {
    if ($this->pemesanan->status_pemesanan !== 'menunggu_konfirmasi') {
        return;
    }

    $this->validate([
        'catatanAdmin' => 'required|min:5'
    ], [
        'catatanAdmin.required' => 'Catatan wajib diisi saat menolak pesanan.',
        'catatanAdmin.min' => 'Catatan penolakan minimal 5 karakter.'
    ]);

    $this->pemesanan->update([
        'status_pemesanan' => 'ditolak',
        'catatan' => $this->catatanAdmin
    ]);

    // Unit back to tersedia
    $this->pemesanan->kendaraanUnit->update(['status_unit' => 'tersedia']);

    $this->dispatch('swal:toast', title: 'Pemesanan ditolak.', icon: 'info');
};

$complete = function () {
    if ($this->pemesanan->status_pemesanan !== 'disetujui') {
        return;
    }

    $this->pemesanan->update([
        'status_pemesanan' => 'selesai',
        'catatan' => $this->catatanAdmin
    ]);

    // Unit back to tersedia
    $this->pemesanan->kendaraanUnit->update(['status_unit' => 'tersedia']);

    $this->dispatch('swal:toast', title: 'Pemesanan diselesaikan.', icon: 'success');
};

$cancel = function () {
    // Admin forces cancel
    if (!in_array($this->pemesanan->status_pemesanan, ['menunggu_konfirmasi', 'disetujui'])) {
        return;
    }

    $this->pemesanan->update([
        'status_pemesanan' => 'dibatalkan',
        'catatan' => $this->catatanAdmin
    ]);

    $this->pemesanan->kendaraanUnit->update(['status_unit' => 'tersedia']);
    
    $this->dispatch('swal:toast', title: 'Pemesanan dibatalkan.', icon: 'warning');
};

?>

<div class="min-h-screen bg-backgroundSoft">
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header & Flash -->
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <a href="{{ route('admin.pemesanan.index') }}" wire:navigate class="inline-flex items-center text-sm font-medium text-textGray hover:text-primary transition-colors mb-4 cursor-pointer">
                        <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Kembali ke Daftar
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pemesanan #{{ str_pad($pemesanan->id, 5, '0', STR_PAD_LEFT) }}</h1>
                </div>
                
                <div>
                    @php
                        $badges = [
                            'menunggu_konfirmasi' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                            'disetujui' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'ditolak' => 'bg-red-100 text-red-800 border-red-200',
                            'dibatalkan' => 'bg-gray-100 text-gray-800 border-gray-200',
                            'selesai' => 'bg-green-100 text-green-800 border-green-200',
                        ];
                        $labels = [
                            'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
                            'disetujui' => 'Disetujui / Aktif',
                            'ditolak' => 'Ditolak',
                            'dibatalkan' => 'Dibatalkan',
                            'selesai' => 'Selesai',
                        ];
                    @endphp
                    <span class="px-4 py-2 text-sm font-bold rounded-xl border {{ $badges[$pemesanan->status_pemesanan] ?? 'bg-gray-100' }}">
                        Status: {{ $labels[$pemesanan->status_pemesanan] ?? strtoupper($pemesanan->status_pemesanan) }}
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Details -->
                <div class="lg:col-span-2 space-y-8">
                    
                    <!-- Customer Details -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-inputBorder relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5">
                            <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <h3 class="text-lg font-bold text-textDark mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            Informasi Pelanggan
                        </h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-textGray font-medium">Nama Lengkap</p>
                                <p class="text-base text-textDark font-bold mt-1">{{ $pemesanan->pelanggan->nama ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-textGray font-medium">Email</p>
                                <p class="text-base text-textDark font-bold mt-1">{{ $pemesanan->pelanggan->email ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-textGray font-medium">No. Telepon</p>
                                <p class="text-base text-textDark font-bold mt-1">{{ $pemesanan->pelanggan->no_telp ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-textGray font-medium">NIK</p>
                                <p class="text-base text-textDark font-bold mt-1">{{ $pemesanan->pelanggan->nik ?? '-' }}</p>
                            </div>
                            <div class="sm:col-span-2">
                                <p class="text-sm text-textGray font-medium">Alamat</p>
                                <p class="text-base text-textDark mt-1">{{ $pemesanan->pelanggan->alamat ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Booking & Vehicle Details -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-inputBorder">
                        <h3 class="text-lg font-bold text-textDark mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            Detail Sewa Kendaraan
                        </h3>
                        
                        <div class="flex items-center gap-4 bg-gray-50 p-4 rounded-xl border border-gray-100 mb-6">
                            <div class="w-16 h-16 bg-white rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                                @if($pemesanan->kendaraanUnit->kendaraan->foto)
                                    <img src="{{ Storage::url($pemesanan->kendaraanUnit->kendaraan->foto) }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <h4 class="text-lg font-bold text-textDark">{{ $pemesanan->kendaraanUnit->kendaraan->nama_kendaraan }}</h4>
                                <div class="text-sm font-medium text-textGray capitalize mt-1 flex items-center gap-3">
                                    <span>{{ $pemesanan->kendaraanUnit->kendaraan->jenis_kendaraan }}</span>
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-0.5 rounded font-mono font-bold">{{ $pemesanan->kendaraanUnit->nomor_plat }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-4">
                            <div>
                                <p class="text-sm text-textGray font-medium">Tanggal Mulai</p>
                                <p class="text-base text-textDark font-bold mt-1">{{ \Carbon\Carbon::parse($pemesanan->tanggal_mulai)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-textGray font-medium">Tanggal Selesai</p>
                                <p class="text-base text-textDark font-bold mt-1">{{ \Carbon\Carbon::parse($pemesanan->tanggal_selesai)->format('d M Y') }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-textGray font-medium">Durasi Sewa</p>
                                <p class="text-base text-textDark font-bold mt-1">{{ \Carbon\Carbon::parse($pemesanan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($pemesanan->tanggal_selesai)) + 1 }} Hari</p>
                            </div>
                            <div>
                                <p class="text-sm text-textGray font-medium">Dibuat Pada</p>
                                <p class="text-base text-textDark font-bold mt-1">{{ $pemesanan->created_at->format('d M Y H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Proof -->
                    <div class="bg-white rounded-2xl p-6 shadow-sm border border-inputBorder">
                        <h3 class="text-lg font-bold text-textDark mb-4 flex items-center gap-2">
                            <svg class="h-5 w-5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" /></svg>
                            Bukti Pembayaran / DP
                        </h3>
                        @if ($pemesanan->hasMedia('bukti_pembayaran'))
                            <div class="rounded-xl overflow-hidden border border-inputBorder max-w-sm">
                                <a href="{{ $pemesanan->getFirstMediaUrl('bukti_pembayaran') }}" target="_blank" class="block hover:opacity-90 transition-opacity">
                                    <img src="{{ $pemesanan->getFirstMediaUrl('bukti_pembayaran') }}" alt="Bukti Pembayaran" class="w-full object-cover">
                                </a>
                            </div>
                            <p class="text-xs text-textGray mt-3 flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Klik gambar untuk melihat ukuran penuh
                            </p>
                        @else
                            <div class="bg-gray-50 rounded-xl p-8 border border-dashed border-gray-300 text-center text-textGray">
                                Belum ada bukti pembayaran yang diunggah.
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Cost & Actions -->
                <div class="space-y-6">
                    
                    <!-- Pricing Summary -->
                    <div class="bg-white rounded-2xl shadow-sm border border-inputBorder overflow-hidden">
                        <div class="p-6 bg-gray-50/50 border-b border-inputBorder">
                            <h3 class="text-lg font-bold text-textDark">Rincian Biaya</h3>
                        </div>
                        <div class="p-6 space-y-4">
                            <div class="flex justify-between items-center text-sm font-medium text-textGray">
                                <span>Harga per Hari</span>
                                <span>Rp {{ number_format($pemesanan->harga_per_hari, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm font-medium text-textGray">
                                <span>Durasi</span>
                                <span>{{ \Carbon\Carbon::parse($pemesanan->tanggal_mulai)->diffInDays(\Carbon\Carbon::parse($pemesanan->tanggal_selesai)) + 1 }} Hari</span>
                            </div>
                            <div class="pt-4 border-t border-dashed border-gray-200 mt-2">
                                <div class="flex justify-between items-center">
                                    <span class="text-base font-bold text-textDark">Total Harga</span>
                                    <span class="text-xl font-black text-primary">Rp {{ number_format($pemesanan->total_harga, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions Panel -->
                    <div class="bg-white rounded-2xl shadow-sm border border-inputBorder p-6">
                        <h3 class="text-lg font-bold text-textDark mb-4">Aksi Admin</h3>
                        
                        <form wire:submit.prevent>
                            <label class="block text-sm font-semibold text-textDark mb-1">Catatan Admin</label>
                            <textarea wire:model="catatanAdmin" rows="3" placeholder="Tambahkan alasan penolakan, instruksi khusus, dll..." 
                                class="block w-full px-4 py-3 rounded-xl bg-gray-50 border border-gray-200 text-sm focus:ring-2 focus:ring-primary focus:outline-none mb-1 shadow-inner focus:bg-white transition-all"></textarea>
                            @error('catatanAdmin') <span class="text-red-500 text-xs font-medium">{{ $message }}</span> @enderror
                            
                            <div class="space-y-3 mt-6">
                                @if($pemesanan->status_pemesanan === 'menunggu_konfirmasi')
                                    <button wire:click="approve" type="button" class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 bg-green-500 hover:bg-green-600 text-white font-bold rounded-xl shadow-sm shadow-green-200 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Setujui Pesanan
                                    </button>
                                    <button wire:click="reject" type="button" class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 font-bold border border-red-200 rounded-xl transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                        Tolak Pesanan
                                    </button>
                                @endif

                                @if($pemesanan->status_pemesanan === 'disetujui')
                                    <button wire:click="complete" type="button" class="w-full inline-flex justify-center items-center gap-2 px-5 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl shadow-sm shadow-blue-200 transition-all">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        Tandai Selesai
                                    </button>
                                @endif

                                @if(in_array($pemesanan->status_pemesanan, ['menunggu_konfirmasi', 'disetujui']))
                                    <div class="pt-4 border-t border-gray-100 mt-2">
                                        <button wire:click="$dispatch('swal:confirm', {
                                                title: 'Batalkan Pesanan?',
                                                text: 'Yakin membatalkan pesanan secara paksa dan mengembalikan status unit?',
                                                icon: 'warning',
                                                method: 'cancel',
                                                id: null
                                            })" type="button" class="w-full inline-flex justify-center items-center text-sm font-medium text-gray-500 hover:text-red-500 transition-colors">
                                            Batalkan Pesanan (Force)
                                        </button>
                                    </div>
                                @endif
                                
                                @if(in_array($pemesanan->status_pemesanan, ['ditolak', 'dibatalkan', 'selesai']))
                                    <div class="p-4 bg-gray-50 rounded-xl text-center border border-gray-100">
                                        <p class="text-sm font-medium text-gray-500">Pemesanan telah {{ strtolower($labels[$pemesanan->status_pemesanan] ?? $pemesanan->status_pemesanan) }}. Tidak ada aksi lebih lanjut untuk pesanan ini.</p>
                                        <button wire:click="saveNote" type="button" class="mt-3 text-xs font-bold text-primary hover:text-primaryDark">Simpan Update Catatan Saja</button>
                                    </div>
                                    <!-- Provide a fallback function to save just note -->
                                    <?php
                                        $saveNote = function () {
                                            $this->pemesanan->update(['catatan' => $this->catatanAdmin]);
                                            $this->dispatch('swal:toast', title: 'Catatan diperbarui.', icon: 'success');
                                        };
                                    ?>
                                @endif
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

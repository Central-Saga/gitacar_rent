<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use App\Models\Pemesanan;
use Illuminate\Support\Facades\Auth;

new #[Layout('components.layouts.landing')] class extends Component {
    public $activePemesanan;
    public $historyPemesanans;

    public function mount()
    {
        $user = Auth::user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }

        $pelanggan = $user->pelanggan;

        if (!$pelanggan) {
            $this->activePemesanan = null;
            $this->historyPemesanans = collect();
            return;
        }

        // Fetch active pemesanan (not completed and not fully cancelled)
        $this->activePemesanan = Pemesanan::with(['kendaraanUnit.kendaraan', 'promo'])
            ->where('pelanggan_id', $pelanggan->id)
            ->whereNotIn('status_pemesanan', ['selesai', 'dibatalkan'])
            ->latest()
            ->first();

        // Fetch history pemesanan
        $this->historyPemesanans = Pemesanan::with(['kendaraanUnit.kendaraan', 'promo'])
            ->where('pelanggan_id', $pelanggan->id)
            ->latest()
            ->get();
    }
};
?>

<style>
  :root {
    --nav-h: 80px;
  }
</style>

<div class="min-h-screen bg-gray-50 pt-[var(--nav-h)] pb-32">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-20 lg:py-24">
        
        <!-- Hero Section -->
        <div class="mb-10" data-aos="fade-up">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pesanan Saya</h1>
            <p class="text-gray-600">Pantau status pesanan aktif Anda dan lihat riwayat transaksi sebelumnya secara detail.</p>
        </div>

        <!-- Active Order Status Tracker -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8 mb-8" data-aos="fade-up" data-aos-delay="100">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Status Pesanan Terbaru</h2>
            
            @if($activePemesanan)
                <div class="relative">
                    <!-- Progress Line Background -->
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-200 -translate-y-1/2 hidden sm:block rounded-full"></div>
                    
                    <!-- Active Progress Line -->
                    @php
                        $progressWidth = '0%';
                        if (in_array($activePemesanan->status_pemesanan, ['menunggu_konfirmasi'])) $progressWidth = '10%';
                        if (in_array($activePemesanan->status_pemesanan, ['disetujui'])) $progressWidth = '50%';
                        if (in_array($activePemesanan->status_pemesanan, ['selesai'])) $progressWidth = '100%';
                    @endphp
                    <div class="absolute top-1/2 left-0 h-1 bg-primary -translate-y-1/2 hidden sm:block transition-all duration-500 rounded-full" style="width: {{ $progressWidth }}"></div>

                    <div class="relative flex flex-col sm:flex-row justify-between items-center z-10 gap-6 sm:gap-0">
                        
                        <!-- Step 1: Sedang Diproses -->
                        @php
                            $step1Active = in_array($activePemesanan->status_pemesanan, ['menunggu_konfirmasi', 'disetujui', 'selesai']);
                            $step1Current = $activePemesanan->status_pemesanan === 'menunggu_konfirmasi';
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center border-4 
                                {{ $step1Active ? 'bg-primary border-teal-100 text-white shadow-md' : 'bg-white border-gray-200 text-gray-400' }} 
                                {{ $step1Current ? 'ring-4 ring-primary/20 scale-110 transition-transform' : '' }}">
                                <i class="fas fa-sync-alt text-2xl {{ $step1Current ? 'animate-spin-slow' : '' }}"></i>
                            </div>
                            <div class="mt-4 text-center">
                                <span class="block text-sm font-bold {{ $step1Active ? 'text-gray-900' : 'text-gray-500' }}">Sedang Diproses</span>
                                @if($step1Current)<span class="text-xs text-primary font-medium mt-1">Pending Approval</span>@endif
                            </div>
                        </div>

                        <!-- Step 2: Siap Di-Pickup -->
                        @php
                            $step2Active = in_array($activePemesanan->status_pemesanan, ['disetujui', 'selesai']);
                            $step2Current = $activePemesanan->status_pemesanan === 'disetujui';
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center border-4 
                                {{ $step2Active ? 'bg-primary border-teal-100 text-white shadow-md' : 'bg-white border-gray-200 text-gray-400' }}
                                {{ $step2Current ? 'ring-4 ring-primary/20 scale-110 transition-transform' : '' }}">
                                <i class="fas fa-store text-2xl"></i>
                            </div>
                            <div class="mt-4 text-center">
                                <span class="block text-sm font-bold {{ $step2Active ? 'text-gray-900' : 'text-gray-500' }}">Siap Di-Pickup</span>
                                @if($step2Current)<span class="text-xs text-primary font-medium mt-1">Silahkan ambil kendaraan</span>@endif
                            </div>
                        </div>

                        <!-- Step 3: Pesanan Selesai -->
                        @php
                            $step3Active = $activePemesanan->status_pemesanan === 'selesai';
                            $step3Current = $activePemesanan->status_pemesanan === 'selesai';
                        @endphp
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 rounded-full flex items-center justify-center border-4 
                                {{ $step3Active ? 'bg-primary border-teal-100 text-white shadow-md' : 'bg-white border-gray-200 text-gray-400' }}
                                {{ $step3Current ? 'ring-4 ring-primary/20 scale-110 transition-transform' : '' }}">
                                <i class="fas fa-check text-2xl"></i>
                            </div>
                            <div class="mt-4 text-center">
                                <span class="block text-sm font-bold {{ $step3Active ? 'text-gray-900' : 'text-gray-500' }}">Pesanan Selesai</span>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="mt-8 p-4 bg-teal-50 rounded-xl border border-teal-100 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-full bg-teal-100 text-primary flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-info-circle"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900">Detail Pesanan Aktif</h4>
                        <p class="text-sm text-gray-600 mt-1">Anda memesan <span class="font-semibold text-gray-800">{{ $activePemesanan->kendaraanUnit->kendaraan->nama_kendaraan }}</span> (Plat: {{ $activePemesanan->kendaraanUnit->nomor_plat }}). 
                        Waktu mulai: {{ $activePemesanan->waktu_mulai->format('d M Y, H:i') }}.</p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <i class="fas fa-box-open text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">Tidak ada pesanan aktif</h3>
                    <p class="text-gray-500 mt-2">Anda belum memiliki pesanan yang sedang berjalan.</p>
                    <a href="{{ route('katalog.mobil') }}" class="inline-block mt-4 px-6 py-2 bg-primary text-white font-medium rounded-full hover:bg-[#248f7f] transition-colors">Pesan Sekarang</a>
                </div>
            @endif
        </div>

        <!-- Transaction History -->
        <div data-aos="fade-up" data-aos-delay="200">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Riwayat Transaksi</h2>
            
            @if($historyPemesanans->count() > 0)
                <div class="space-y-4">
                    @foreach($historyPemesanans as $pesanan)
                        <div x-data="{ expanded: false }" class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden transition-all duration-300 hover:shadow-md">
                            
                            <!-- Accordion Header -->
                            <div @click="expanded = !expanded" class="p-5 sm:p-6 cursor-pointer flex items-start sm:items-center justify-between gap-4">
                                <div class="flex items-start gap-4">
                                    <div class="w-12 h-12 rounded-full flex items-center justify-center flex-shrink-0 
                                        {{ $pesanan->status_pemesanan == 'selesai' ? 'bg-teal-50 text-primary ring-2 ring-primary' : 
                                          ($pesanan->status_pemesanan == 'dibatalkan' || $pesanan->status_pemesanan == 'ditolak' ? 'bg-red-50 text-red-500 ring-2 ring-red-400' : 'bg-gray-50 text-gray-500 ring-2 ring-gray-300') }}">
                                        @if($pesanan->status_pemesanan == 'selesai')
                                            <i class="fas fa-check"></i>
                                        @elseif($pesanan->status_pemesanan == 'dibatalkan' || $pesanan->status_pemesanan == 'ditolak')
                                            <i class="fas fa-times"></i>
                                        @else
                                            <i class="fas fa-spinner"></i>
                                        @endif
                                    </div>
                                    
                                    <div>
                                        <div class="flex flex-col sm:flex-row sm:items-center gap-1 sm:gap-3 mb-1">
                                            <h3 class="font-bold text-gray-900">{{ $pesanan->kendaraanUnit->kendaraan->nama_kendaraan }}</h3>
                                            <span class="text-xs text-gray-500 hidden sm:block">•</span>
                                            <span class="text-xs text-gray-500">{{ $pesanan->created_at->format('d M Y, H:i') }}</span>
                                        </div>
                                        <div class="text-sm text-gray-600 mb-2">
                                            Sewa {{ \Carbon\Carbon::parse($pesanan->waktu_mulai)->diffInDays(\Carbon\Carbon::parse($pesanan->waktu_selesai)) }} Hari 
                                            @if($pesanan->hari_terlambat > 0)
                                                <span class="text-red-500 font-medium">(Terlambat {{ $pesanan->hari_terlambat }} Hari)</span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-bold text-gray-900">Total: Rp {{ number_format($pesanan->total_harga + $pesanan->denda, 0, ',', '.') }}</span>
                                            <span class="px-2 py-0.5 rounded text-xs font-semibold
                                                {{ $pesanan->status_pemesanan == 'selesai' ? 'bg-green-100 text-green-700' : 
                                                  ($pesanan->status_pemesanan == 'dibatalkan' || $pesanan->status_pemesanan == 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                                {{ ucwords(str_replace('_', ' ', $pesanan->status_pemesanan)) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-gray-400 transition-transform duration-300 transform" :class="expanded ? 'rotate-180' : ''">
                                    <i class="fas fa-chevron-down"></i>
                                </div>
                            </div>
                            
                            <!-- Accordion Body -->
                            <div x-show="expanded" x-collapse>
                                <div class="px-5 sm:px-6 pb-6 pt-2 border-t border-gray-100 bg-gray-50/50">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mt-4">
                                        <!-- Detail Sewa -->
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Detail Sewa</h4>
                                            <ul class="space-y-2 text-sm">
                                                <li class="flex justify-between">
                                                    <span class="text-gray-600">Kendaraan</span>
                                                    <span class="font-medium text-gray-900">{{ $pesanan->kendaraanUnit->kendaraan->nama_kendaraan }}</span>
                                                </li>
                                                <li class="flex justify-between">
                                                    <span class="text-gray-600">Plat Nomor</span>
                                                    <span class="font-medium text-gray-900">{{ $pesanan->kendaraanUnit->nomor_plat }}</span>
                                                </li>
                                                <li class="flex justify-between">
                                                    <span class="text-gray-600">Waktu Ambil</span>
                                                    <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($pesanan->waktu_mulai)->format('d M Y, H:i') }}</span>
                                                </li>
                                                <li class="flex justify-between">
                                                    <span class="text-gray-600">Waktu Kembali</span>
                                                    <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($pesanan->waktu_selesai)->format('d M Y, H:i') }}</span>
                                                </li>
                                                @if($pesanan->waktu_kembali)
                                                <li class="flex justify-between">
                                                    <span class="text-gray-600">Dikembalikan Pada</span>
                                                    <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($pesanan->waktu_kembali)->format('d M Y, H:i') }}</span>
                                                </li>
                                                @endif
                                            </ul>
                                        </div>

                                        <!-- Detail Pembayaran -->
                                        <div>
                                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Rincian Biaya</h4>
                                            <ul class="space-y-2 text-sm">
                                                <li class="flex justify-between">
                                                    @php
                                                        $durasi = \Carbon\Carbon::parse($pesanan->waktu_mulai)->diffInDays(\Carbon\Carbon::parse($pesanan->waktu_selesai)) ?: 1;
                                                        $tipe = $pesanan->tipe_harga ?? 'harian';
                                                        $labelTipe = $tipe === 'bulanan' ? 'Bulan' : ($tipe === 'mingguan' ? 'Minggu' : 'Hari');
                                                        $divider = $tipe === 'bulanan' ? 30 : ($tipe === 'mingguan' ? 7 : 1);
                                                        $jumlahUnit = ceil($durasi / $divider);
                                                    @endphp
                                                    <span class="text-gray-600">Harga Sewa (x{{ $jumlahUnit }} {{ $labelTipe }})</span>
                                                    <span class="font-medium text-gray-900">Rp {{ number_format($pesanan->harga_sewa ?? $pesanan->harga_per_hari, 0, ',', '.') }} / {{ strtolower($labelTipe) }}</span>
                                                </li>
                                                @if($pesanan->total_diskon > 0)
                                                <li class="flex justify-between text-primary">
                                                    <span>Diskon Promo{{ $pesanan->promo ? ' (' . $pesanan->promo->kode . ')' : '' }}</span>
                                                    <span class="font-medium">- Rp {{ number_format($pesanan->total_diskon, 0, ',', '.') }}</span>
                                                </li>
                                                @endif
                                                @if($pesanan->denda > 0)
                                                <li class="flex justify-between text-red-600">
                                                    <span>Denda Terlambat ({{ $pesanan->hari_terlambat }} Hari)</span>
                                                    <span class="font-medium">+ Rp {{ number_format($pesanan->denda, 0, ',', '.') }}</span>
                                                </li>
                                                @endif
                                                <li class="pt-2 mt-2 border-t border-gray-200 flex justify-between font-bold text-lg">
                                                    <span class="text-gray-900">Total Akhir</span>
                                                    <span class="text-primary">Rp {{ number_format($pesanan->total_harga + $pesanan->denda, 0, ',', '.') }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    
                                    @if($pesanan->catatan)
                                    <div class="mt-4 p-3 bg-yellow-50 text-yellow-800 rounded-lg text-sm border border-yellow-100">
                                        <strong>Catatan Admin:</strong> {{ $pesanan->catatan }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 text-gray-400">
                        <i class="fas fa-receipt text-2xl"></i>
                    </div>
                    <p class="text-gray-500">Belum ada riwayat transaksi.</p>
                </div>
            @endif
        </div>

    </div>
</div>

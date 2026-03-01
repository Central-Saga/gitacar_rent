<?php

use App\Models\KendaraanUnit;
use App\Models\Pemesanan;
use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

new
    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    class extends Component {
    public function with(): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // 1. Total Unit Aktif
        $totalUnitAktif = KendaraanUnit::where('status_unit', '!=', 'nonaktif')->count();

        // 2. Unit Sedang Disewa
        $unitSedangDisewa = KendaraanUnit::where('status_unit', 'disewa')->count();

        // 3. Pemesanan Menunggu Konfirmasi
        $menungguKonfirmasi = Pemesanan::where('status_pemesanan', 'menunggu_konfirmasi')->count();

        // 4. Pendapatan Bulan Ini (Status Selesai pada bulan ini)
        $pendapatanBulanIni = Pemesanan::whereIn('status_pemesanan', ['selesai'])
            ->whereBetween('updated_at', [$startOfMonth, $endOfMonth])
            ->sum(DB::raw('total_harga + denda'));

        // Data for Revenue Chart (Last 7 Days)
        $revenueDates = [];
        $revenueData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenueDates[] = $date->format('d M');
            $dailyRevenue = Pemesanan::whereIn('status_pemesanan', ['selesai'])
                ->whereDate('updated_at', $date)
                ->sum(DB::raw('total_harga + denda'));
            $revenueData[] = $dailyRevenue;
        }

        // Data for Fleet Status
        $fleetStatusData = [
            'Tersedia' => KendaraanUnit::where('status_unit', 'tersedia')->count(),
            'Dibooking' => KendaraanUnit::where('status_unit', 'dibooking')->count(),
            'Disewa' => KendaraanUnit::where('status_unit', 'disewa')->count(),
            'Maintenance' => KendaraanUnit::where('status_unit', 'maintenance')->count(),
        ];

        // Recent Activity (Last 6 activities)
        $recentActivities = Pemesanan::with(['pelanggan', 'kendaraanUnit.kendaraan'])
            ->latest('updated_at')
            ->take(6)
            ->get();

        // Alerts / Reminders
        $kendaraanSelesaiHariIni = Pemesanan::with(['pelanggan', 'kendaraanUnit.kendaraan'])
            ->whereIn('status_pemesanan', ['disetujui'])
            ->whereDate('waktu_selesai', Carbon::today())
            ->get();

        $kendaraanTerlambat = Pemesanan::with(['pelanggan', 'kendaraanUnit.kendaraan'])
            ->whereIn('status_pemesanan', ['disetujui'])
            ->where('waktu_selesai', '<', Carbon::now())
            ->get();

        $bookingBelumDiverifikasi = Pemesanan::with(['pelanggan', 'kendaraanUnit.kendaraan'])
            ->where('status_pemesanan', 'menunggu_konfirmasi')
            ->get();

        return [
            'totalUnitAktif' => $totalUnitAktif,
            'unitSedangDisewa' => $unitSedangDisewa,
            'menungguKonfirmasi' => $menungguKonfirmasi,
            'pendapatanBulanIni' => $pendapatanBulanIni,
            'revenueDates' => json_encode($revenueDates),
            'revenueData' => json_encode($revenueData),
            'fleetStatusData' => json_encode(array_values($fleetStatusData)),
            'fleetStatusLabels' => json_encode(array_keys($fleetStatusData)),
            'recentActivities' => $recentActivities,
            'kendaraanSelesaiHariIni' => $kendaraanSelesaiHariIni,
            'kendaraanTerlambat' => $kendaraanTerlambat,
            'bookingBelumDiverifikasi' => $bookingBelumDiverifikasi,
        ];
    }
};
?>

<div class="flex flex-col gap-6 w-full h-full pb-10">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-textDark">{{ __('Dashboard Operasional') }}</h1>
            <p class="text-sm text-textGray font-medium mt-1">Ringkasan hari ini,
                {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</p>
        </div>
    </div>

    <!-- 1. Baris Pertama (4 Card Utama) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Unit Aktif -->
        <div
            class="bg-white rounded-2xl border border-inputBorder p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-textGray text-xs font-bold uppercase tracking-wider">Total Unit Aktif</span>
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                    </svg>
                </div>
            </div>
            <div>
                <span class="text-3xl font-black text-textDark">{{ $totalUnitAktif }}</span>
                <p class="text-xs text-textGray mt-1">Kapasitas armada saat ini</p>
            </div>
        </div>

        <!-- Unit Sedang Disewa -->
        <div
            class="bg-white rounded-2xl border border-inputBorder p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-textGray text-xs font-bold uppercase tracking-wider">Sedang Disewa</span>
                <div class="p-2 bg-accentYellow/20 text-accentYellow rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.59 14.37a6 6 0 0 1-5.84 7.38v-4.8m5.84-2.58a14.98 14.98 0 0 0 6.16-12.12A14.98 14.98 0 0 0 9.631 8.41m5.96 5.96a14.926 14.926 0 0 1-5.841 2.58m-.119-8.54a6 6 0 0 0-7.381 5.84h4.8m2.581-5.84a14.927 14.927 0 0 0-2.58 5.84m2.699 2.7c-.103.021-.207.041-.311.06a15.09 15.09 0 0 1-2.448-2.448 14.9 14.9 0 0 1 .06-.312m-2.24 2.39a4.493 4.493 0 0 0-1.757 4.306 4.438 4.438 0 0 0 2.946-2.946 4.493 4.493 0 0 0 4.306-1.758q-1.996 1.405-3.495 1.405t-3.496-1.405q1.405-1.995 1.405-3.495t-1.405-3.495m-4.509 4.51c-.021-.104-.041-.208-.06-.312m2.448 2.448a15.09 15.09 0 0 1-2.448-2.448m7.38-5.84a6 6 0 0 0-5.84-7.38v4.8" />
                    </svg>
                </div>
            </div>
            <div>
                <span class="text-3xl font-black text-textDark">{{ $unitSedangDisewa }}</span>
                <p class="text-xs text-textGray mt-1">Kendaraan berjalan</p>
            </div>
        </div>

        <!-- Menunggu Konfirmasi -->
        <div
            class="bg-white rounded-2xl border border-inputBorder p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
            @if($menungguKonfirmasi > 0)
                <div class="absolute top-0 right-0 w-16 h-16 pointer-events-none">
                    <div
                        class="absolute top-4 -right-6 bg-red-500 text-white text-[10px] font-bold py-1 px-8 rotate-45 transform origin-center shadow-sm">
                        PERLU CEK
                    </div>
                </div>
            @endif
            <div class="flex items-center justify-between mb-4">
                <span class="text-textGray text-xs font-bold uppercase tracking-wider">Menunggu Verifikasi</span>
                <div class="p-2 bg-orange-50 text-orange-600 rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div>
                <span
                    class="text-3xl font-black {{ $menungguKonfirmasi > 0 ? 'text-orange-600' : 'text-textDark' }}">{{ $menungguKonfirmasi }}</span>
                <p class="text-xs text-textGray mt-1">Booking baru</p>
            </div>
        </div>

        <!-- Pendapatan Bulan Ini -->
        <div
            class="bg-white rounded-2xl border border-inputBorder p-6 flex flex-col justify-between shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <span class="text-textGray text-xs font-bold uppercase tracking-wider">Pendapatan (Bulan Ini)</span>
                <div class="p-2 bg-primary/20 text-primaryDark rounded-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                        stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
            </div>
            <div>
                <span class="text-3xl font-black text-textDark">Rp
                    {{ number_format($pendapatanBulanIni, 0, ',', '.') }}</span>
                <p class="text-xs text-primary mt-1 font-medium">Transaksi Selesai</p>
            </div>
        </div>
    </div>

    <!-- 2. Baris Kedua (Grafik & Status Armada) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Grafik Pendapatan -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-inputBorder shadow-sm p-6 overflow-hidden">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-textDark">Grafik Pendapatan (7 Hari Terakhir)</h3>
                <p class="text-xs text-textGray">Statistik pendapatan dari penyewaan yang terselesaikan.</p>
            </div>
            <div class="relative h-64 w-full" x-data="{
                    initChart() {
                        const ctx = document.getElementById('revenueChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: {{ $revenueDates }},
                                datasets: [{
                                    label: 'Pendapatan (Rp)',
                                    data: {{ $revenueData }},
                                    borderColor: '#10B981', // Tailwind Emerald 500 (Primary like)
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: { display: false }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: function(value) {
                                                return 'Rp ' + (value/1000) + 'k';
                                            }
                                        }
                                    }
                                }
                            }
                        });
                    }
                }" x-init="initChart()">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Status Armada (Pie Chart) -->
        <div class="bg-white rounded-2xl border border-inputBorder shadow-sm p-6">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-textDark">Status Armada</h3>
                <p class="text-xs text-textGray">Distribusi kendaraan berdasarkan status.</p>
            </div>
            <div class="h-56 relative w-full flex items-center justify-center" x-data="{
                    initPieChart() {
                        const ctx = document.getElementById('fleetChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: {{ $fleetStatusLabels }},
                                datasets: [{
                                    data: {{ $fleetStatusData }},
                                    backgroundColor: [
                                        '#10B981', // Tersedia (Emerald)
                                        '#F59E0B', // Dibooking (Amber)
                                        '#3B82F6', // Disewa (Blue)
                                        '#EF4444'  // Maintenance (Red)
                                    ],
                                    borderWidth: 0,
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                plugins: {
                                    legend: { position: 'bottom' }
                                },
                                cutout: '70%',
                                responsive: true,
                                maintainAspectRatio: false,
                            }
                        });
                    }
                 }" x-init="initPieChart()">
                <canvas id="fleetChart"></canvas>
            </div>
        </div>
    </div>

    <!-- 3. Baris Ketiga & Keempat (Recent Activity & Reminders) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl border border-inputBorder shadow-sm p-6 flex flex-col"
            style="max-height: 500px">
            <div class="mb-4">
                <h3 class="text-lg font-bold text-textDark">Aktivitas Terbaru</h3>
                <p class="text-xs text-textGray">Aktivitas pemesanan terakhir (Booking, Approval, Return).</p>
            </div>

            <div class="flex-1 overflow-y-auto pr-2">
                @forelse($recentActivities as $activity)
                    <div
                        class="flex items-start gap-4 mb-4 pb-4 border-b border-inputBorder last:border-0 last:mb-0 last:pb-0">
                        <div class="p-2 rounded-full mt-1 shrink-0 bg-backgroundSoft">
                            @if($activity->status_pemesanan === 'menunggu_konfirmasi')
                                <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif($activity->status_pemesanan === 'disetujui')
                                <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @elseif($activity->status_pemesanan === 'selesai')
                                <svg class="w-5 h-5 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            @elseif($activity->status_pemesanan === 'dibatalkan' || $activity->status_pemesanan === 'ditolak')
                                <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                    stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-textDark font-semibold">
                                {{ $activity->pelanggan->user->name ?? 'Pelanggan' }}
                                <span class="font-normal text-textGray">telah membuat pemesanan</span>
                                {{ $activity->kendaraanUnit->kendaraan->nama_kendaraan ?? 'Kendaraan' }}
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs px-2 py-0.5 rounded-full font-medium 
                                        @if($activity->status_pemesanan === 'menunggu_konfirmasi') bg-orange-100 text-orange-700
                                        @elseif($activity->status_pemesanan === 'disetujui') bg-blue-100 text-blue-700
                                        @elseif($activity->status_pemesanan === 'selesai') bg-green-100 text-green-700
                                        @else bg-red-100 text-red-700
                                        @endif">
                                    {{ str_replace('_', ' ', Str::title($activity->status_pemesanan)) }}
                                </span>
                                <span class="text-[11px] text-textBody">{{ $activity->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        <div class="shrink-0 text-right">
                            <a href="{{ route('admin.pemesanan.show', $activity->id) }}"
                                class="text-xs text-primary hover:text-primaryDark font-medium">Lihat</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-6 h-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                        </div>
                        <p class="text-sm text-textGray">Belum ada aktivitas terbaru.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Reminder Penting -->
        <div class="flex flex-col gap-6">
            <div class="bg-white rounded-2xl border border-inputBorder shadow-sm p-6 flex flex-col"
                style="max-height: 500px">
                <div class="mb-4">
                    <h3 class="text-lg font-bold text-red-600 flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd" />
                        </svg>
                        Reminder Penting
                    </h3>
                    <p class="text-xs text-textGray">Hal-hal yang membutuhkan tindakan segera hari ini.</p>
                </div>

                <div class="flex-1 overflow-y-auto pr-2 space-y-4">

                    <!-- Kendaraan Terlambat -->
                    @if($kendaraanTerlambat->count() > 0)
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                            <div class="flex gap-3">
                                <div class="shrink-0">
                                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-red-800">{{ $kendaraanTerlambat->count() }} Kendaraan
                                        Terlambat Mengembalikan</h4>
                                    <p class="text-xs text-red-600 mt-1">Penyewa belum mengembalikan unit melewati batas
                                        waktu.</p>
                                    <div class="mt-2 space-y-2">
                                        @foreach($kendaraanTerlambat->take(3) as $k)
                                            <a href="{{ route('admin.pemesanan.show', $k->id) }}"
                                                class="block p-2 bg-white rounded shadow-sm text-xs hover:bg-red-100 transition">
                                                <span class="font-bold">{{ $k->kendaraanUnit->nomor_plat }}</span> -
                                                {{ $k->pelanggan->user->name }}
                                                <br>
                                                <span class="text-red-500">Tenggat:
                                                    {{ $k->waktu_selesai->format('d M Y H:i') }}</span>
                                            </a>
                                        @endforeach
                                        @if($kendaraanTerlambat->count() > 3)
                                            <p class="text-xs text-red-500 italic">+{{ $kendaraanTerlambat->count() - 3 }}
                                                kendaraan lainnya.</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Kendaraan Selesai Hari Ini -->
                    @if($kendaraanSelesaiHariIni->count() > 0)
                        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded-r-lg">
                            <div class="flex gap-3">
                                <div class="shrink-0">
                                    <svg class="w-5 h-5 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                        stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-blue-800">{{ $kendaraanSelesaiHariIni->count() }}
                                        Jadwal Pengembalian Hari Ini</h4>
                                    <p class="text-xs text-blue-600 mt-1">Harap bersiap untuk menerima pengembalian dari
                                        kendaraan berikut.</p>
                                    <div class="mt-2 space-y-2">
                                        @foreach($kendaraanSelesaiHariIni->take(3) as $k)
                                            <a href="{{ route('admin.pemesanan.show', $k->id) }}"
                                                class="block p-2 bg-white rounded shadow-sm text-xs hover:bg-blue-100 transition">
                                                <span class="font-bold">{{ $k->kendaraanUnit->nomor_plat }}</span> -
                                                {{ $k->pelanggan->user->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Booking Belum Diverifikasi -->
                    @if($bookingBelumDiverifikasi->count() > 0)
                        <div class="bg-orange-50 border-l-4 border-orange-500 p-4 rounded-r-lg">
                            <div class="flex gap-3">
                                <div class="shrink-0">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                    </svg>
                                </div>
                                <div class="w-full">
                                    <h4 class="text-sm font-bold text-orange-800">{{ $bookingBelumDiverifikasi->count() }}
                                        Booking Baru Perlu Diproses</h4>
                                    <a href="{{ route('admin.pemesanan.index') }}"
                                        class="mt-2 inline-block px-3 py-1 bg-orange-500 hover:bg-orange-600 text-white text-xs font-medium rounded transition">
                                        Tinjau Booking Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if($kendaraanTerlambat->count() === 0 && $kendaraanSelesaiHariIni->count() === 0 && $bookingBelumDiverifikasi->count() === 0)
                        <div class="text-center py-6">
                            <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-3">
                                <svg class="w-6 h-6 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <h4 class="text-sm font-bold text-textDark">Semuanya Aman!</h4>
                            <p class="text-xs text-textGray">Tidak ada reminder mendesak saat ini.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import Chart.js if not yet available in the app layout -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
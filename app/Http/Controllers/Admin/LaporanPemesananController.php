<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;

class LaporanPemesananController extends Controller
{
    public function __invoke(Request $request)
    {
        $bulan = $request->input('bulan', date('m'));
        $tahun = $request->input('tahun', date('Y'));

        $pemesanans = Pemesanan::with(['pelanggan', 'kendaraanUnit.kendaraan', 'promo'])
            ->whereMonth('created_at', $bulan)
            ->whereYear('created_at', $tahun)
            ->whereIn('status_pemesanan', ['selesai', 'disetujui'])
            ->get();

        $totalPendapatan = $pemesanans->sum('total_harga');
        $totalDenda = $pemesanans->sum('denda');

        return view('pages.admin.pemesanan.laporan', compact('pemesanans', 'bulan', 'tahun', 'totalPendapatan', 'totalDenda'));
    }
}

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pemesanan - {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Base Print Adjustments */
        html,
        body {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        /* Desain Preview Web (Simulasi Kertas A4) */
        @media screen {
            body {
                background-color: #f3f4f6;
                /* Gray 100 */
                padding: 40px 0;
            }

            .paper {
                width: 210mm;
                min-height: 297mm;
                padding: 15mm;
                margin: 0 auto;
                background: white;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
                border-radius: 4px;
            }
        }

        /* Desain Khusus Cetak (Printer/PDF) */
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm;
            }

            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
            }

            .paper {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }

            .no-print {
                display: none !important;
            }
        }

        /* Table Layout and Column Sizing */
        table {
            width: 100%;
            table-layout: fixed;
        }

        .col-tgl {
            width: 8%;
            text-align: center !important;
        }

        .col-pelanggan {
            width: 17%;
            text-align: left !important;
        }

        .col-kendaraan {
            width: 17%;
            text-align: left !important;
        }

        .col-waktu {
            width: 16%;
            text-align: left !important;
        }

        .col-promo {
            width: 14%;
            text-align: left !important;
        }

        .col-status {
            width: 10%;
            text-align: center !important;
        }

        .col-total {
            width: 18%;
            text-align: right !important;
        }

        .avoid-break {
            break-inside: avoid;
            page-break-inside: avoid;
        }
    </style>
</head>

<body class="text-gray-900 font-sans antialiased">

    <!-- Tombol Navigasi (Hanya muncul di Layar) -->
    <div class="no-print fixed top-10 right-10 flex flex-col gap-3 z-50">
        <button onclick="window.print()"
            class="bg-[#088395] hover:opacity-90 text-white px-6 py-3 rounded-2xl shadow-xl font-bold flex items-center gap-2 transition-all transform hover:scale-105 active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z">
                </path>
            </svg>
            CETAK PDF SEKARANG
        </button>
    </div>

    <!-- Paper Container -->
    <div class="paper flex flex-col relative">

        <!-- Header Laporan -->
        <div class="border-b-[3px] border-gray-800 pb-4 mb-8 flex justify-between items-end avoid-break">
            <div class="flex items-center gap-4">
                <img src="{{ asset('img/logogitacar.png') }}" class="h-16 w-auto object-contain" alt="Logo">
                <div>
                    <h1 class="text-3xl font-black text-[#1e293b] uppercase tracking-tight">GITA CAR RENTAL</h1>
                    <p class="text-gray-500 font-medium mt-1">Laporan Operasional & Keuangan Transaksi</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-[13px] border border-gray-200 px-4 py-1.5 rounded bg-gray-50 font-bold text-gray-800">
                    Periode: {{ \Carbon\Carbon::createFromDate($tahun, $bulan, 1)->translatedFormat('F Y') }}
                </p>
                <p class="text-[10px] text-gray-400 mt-2">Dicetak pada: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
        </div>

        <!-- Ringkasan Keuangan -->
        <div
            class="mb-8 p-6 bg-[#f8fafc] border border-gray-200 rounded-xl flex items-center justify-between avoid-break">
            <div>
                <h2 class="text-[15px] font-black text-[#1e293b] mb-1">Ringkasan Cashflow</h2>
                <p class="text-[11px] text-gray-500">Hasil rekapitulasi performa bulan ini.</p>
            </div>
            <div class="flex gap-8 text-right items-end">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Total Unit</p>
                    <p class="text-2xl font-black text-[#1e293b]">{{ $pemesanans->count() }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Total Denda</p>
                    <p class="text-2xl font-black text-[#ea580c]">Rp {{ number_format($totalDenda, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Total Pendapatan</p>
                    <p class="text-2xl font-black text-[#16a34a]">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Tabel Data -->
        <div class="flex-grow">
            <h2 class="text-xs font-black text-[#1e293b] uppercase tracking-widest mb-4">Rincian Riwayat Pemesanan</h2>
            <div class="border border-gray-800 rounded-lg overflow-hidden">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="bg-[#1e293b] text-white tracking-widest text-center">
                            <th class="col-tgl p-3 text-[9px] font-black border-r border-[#334155] uppercase">Tgl</th>
                            <th
                                class="col-pelanggan p-3 text-[9px] font-black border-r border-[#334155] uppercase text-left">
                                Pelanggan</th>
                            <th
                                class="col-kendaraan p-3 text-[9px] font-black border-r border-[#334155] uppercase leading-tight text-left">
                                Unit<br>Kendaraan</th>
                            <th
                                class="col-waktu p-3 text-[9px] font-black border-r border-[#334155] uppercase leading-tight text-left">
                                Waktu<br>Sewa</th>
                            <th
                                class="col-promo p-3 text-[9px] font-black border-r border-[#334155] uppercase leading-tight text-left">
                                Promo /<br>Denda</th>
                            <th
                                class="col-status p-3 text-[9px] font-black border-r border-[#334155] uppercase leading-tight">
                                Status</th>
                            <th class="col-total p-3 text-[9px] font-black text-right uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="text-[11px]">
                        @forelse($pemesanans as $p)
                            <tr class="border-b border-gray-200">
                                <td class="col-tgl p-2 border-r border-gray-200 text-gray-600 align-top text-center">
                                    {{ $p->created_at->format('d/m/y') }}
                                </td>
                                <td class="col-pelanggan p-2 border-r border-gray-200 align-top">
                                    <div class="font-bold text-gray-800 break-words leading-tight">
                                        {{ $p->pelanggan->nama ?? '-' }}</div>
                                    @if($p->pelanggan?->no_telp)
                                        <div class="text-[9px] text-gray-400 mt-0.5">{{ $p->pelanggan->no_telp }}</div>
                                    @endif
                                </td>
                                <td class="col-kendaraan p-3 border-r border-gray-200 align-top">
                                    <div class="font-bold text-gray-800  leading-tight break-words">
                                        {{ $p->kendaraanUnit->kendaraan->nama_kendaraan ?? '-' }}</div>
                                    <span
                                        class="inline-block mt-1 font-mono text-[9px] bg-[#fef08a] text-[#854d0e] px-1.5 py-0.5 rounded">
                                        {{ $p->kendaraanUnit->nomor_plat ?? '-' }}
                                    </span>
                                </td>
                                <td class="col-waktu p-3 border-r border-gray-200 text-gray-600 leading-relaxed align-top">
                                    <span class="font-bold text-gray-400">M:</span>
                                    {{ $p->waktu_mulai->format('d/m H:i') }}<br>
                                    <span class="font-bold text-gray-400">S:</span>
                                    {{ $p->waktu_selesai->format('d/m H:i') }}
                                </td>
                                <td class="col-promo p-3 border-r border-gray-200 align-top">
                                    @if($p->promo)
                                        <div class="text-green-600 font-bold mb-1">🎁 {{ $p->promo->kode_promo }}</div>
                                    @endif
                                    @if($p->denda > 0)
                                        <div class="text-orange-600 font-bold">⚠️ {{ number_format($p->denda, 0, ',', '.') }}
                                        </div>
                                    @endif
                                    @if(!$p->promo && $p->denda == 0)
                                        <span class="text-gray-300">-</span>
                                    @endif
                                </td>
                                <td class="col-status p-2 border-r border-gray-200 text-center align-middle">
                                    <span class="font-black text-[9px] uppercase tracking-wider text-gray-800">
                                        {{ str_replace('_', ' ', $p->status_pemesanan) }}
                                    </span>
                                </td>
                                <td class="col-total p-3 text-right font-bold text-gray-900 align-middle">
                                    {{ number_format($p->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center text-gray-400 italic">
                                    Belum ada data pemesanan untuk periode ini.
                                </td>
                            </tr>
                        @endforelse

                        @if($pemesanans->count() > 0)
                            <tr class="bg-[#f8fafc] border-t-[3px] border-[#1e293b] avoid-break">
                                <td colspan="6"
                                    class="p-3 text-right font-black uppercase text-[#1e293b] tracking-wider text-[11px]">
                                    TOTAL AKHIR:
                                </td>
                                <td class="p-3 text-right font-black text-[#16a34a] text-sm whitespace-nowrap">
                                    Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Ttd Section -->
        <div class="mt-16 flex justify-end avoid-break">
            <div class="text-center">
                <p class="text-[11px] text-gray-600 mb-20 whitespace-pre-line">
                    Canggu, {{ now()->format('d F Y') }}
                    Mengetahui,
                </p>
                <div class="min-w-[200px]">
                    <p class="font-bold border-b-2 border-gray-800 inline-block px-10 pb-1 text-[#1e293b] text-[13px]">
                        {{ auth()->user()->name ?? 'Admin / Manajemen' }}
                    </p>
                    <p class="text-[9px] text-gray-500 mt-1 uppercase tracking-[0.15em] font-black">Authorized Signature
                    </p>
                </div>
            </div>
        </div>
    </div>

</body>

</html>
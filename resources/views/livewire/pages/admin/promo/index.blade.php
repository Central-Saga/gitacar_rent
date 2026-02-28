<?php

use App\Models\Promo;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id)
    {
        $promo = Promo::findOrFail($id);
        
        // Cek apakah promo sudah pernah dipakai?
        if ($promo->pemesanans()->exists()) {
            $this->dispatch('swal:toast', title: 'Gagal! Promo sudah digunakan di transaksi.', icon: 'error');
            return;
        }

        $promo->delete();
        $this->dispatch('swal:toast', title: 'Promo berhasil dihapus!', icon: 'success');
    }

    public function with(): array
    {
        return [
            'promos' => Promo::where('kode_promo', 'like', '%' . $this->search . '%')
                ->orWhere('deskripsi', 'like', '%' . $this->search . '%')
                ->latest()
                ->paginate(10)
        ];
    }
}
?>

<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-textDark">Data Promo & Diskon</h1>
            <p class="text-textGray text-sm mt-1">Kelola kupon diskon untuk pelanggan.</p>
        </div>
        <a href="{{ route('admin.promo.create') }}" wire:navigate class="bg-primary hover:bg-primaryDark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
            </svg>
            Buat Promo Baru
        </a>
    </div>

    <!-- Search & Filter -->
    <div class="bg-white p-4 rounded-xl shadow-card mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <input wire:model.live="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-primary focus:border-primary sm:text-sm transition duration-150 ease-in-out" placeholder="Cari kode promo...">
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Kode Promo</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Diskon</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Masa Berlaku</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Kuota</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($promos as $promo)
                        <tr class="hover:bg-gray-50 transition-colors duration-200">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold text-primary">{{ $promo->kode_promo }}</div>
                                <div class="text-xs text-textGray truncate max-w-[200px]">{{ $promo->deskripsi ?: '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-textDark">{{ $promo->diskon_persen }}%</div>
                                @if($promo->maksimal_diskon)
                                    <div class="text-xs text-textGray">Max: Rp {{ number_format($promo->maksimal_diskon, 0, ',', '.') }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-textDark">{{ $promo->tanggal_mulai->format('d M Y') }}</div>
                                <div class="text-xs text-textGray">s/d {{ $promo->tanggal_selesai->format('d M Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($promo->kuota_total)
                                    @php
                                        $sisa = $promo->kuota_total - $promo->kuota_terpakai;
                                    @endphp
                                    <div class="text-sm font-medium {{ $sisa > 0 ? 'text-textDark' : 'text-danger' }}">
                                        {{ $sisa }} / {{ $promo->kuota_total }}
                                    </div>
                                    <div class="text-xs text-textGray">Tersisa</div>
                                @else
                                    <div class="text-sm font-medium text-textDark">Unlimited</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($promo->isValid())
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        Tidak Aktif / Habis
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.promo.edit', $promo->id) }}" wire:navigate class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors" title="Edit Data">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </a>
                                    <button wire:click="delete({{ $promo->id }})" wire:confirm="Apakah Anda yakin ingin menghapus promo beserta datanya?" class="p-2 text-danger hover:bg-danger/10 rounded-lg transition-colors" title="Hapus Data">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-400 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-gray-500 text-sm">Tidak ada data promo yang ditemukan.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($promos->hasPages())
            <div class="px-6 py-4 border-t border-gray-200">
                {{ $promos->links() }}
            </div>
        @endif
    </div>
</div>

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
    <div class="mb-10">
        <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Data Promo & Diskon</h1>
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Kelola kupon diskon untuk pelanggan.</p>
            </div>
            <a href="{{ route('admin.promo.create') }}" wire:navigate
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primaryDark text-white text-sm font-semibold rounded-xl transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Buat Promo Baru</span>
            </a>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="mb-8">
        <input wire:model.live="search" type="text" placeholder="Cari kode promo..."
            class="block w-full px-4 py-3 rounded-2xl bg-white text-textDark placeholder-textGray border border-inputBorder focus:ring-2 focus:ring-primary focus:outline-none text-sm font-medium">
    </div>

    <!-- Table -->
    <div class="relative">
        <div class="relative bg-white shadow-sm rounded-2xl overflow-hidden border border-inputBorder">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50 border-b border-inputBorder">
                        <tr>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Kode Promo</th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Diskon</th>
                            <th class="px-8 py-6 text-left text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Masa Berlaku</th>
                            <th class="px-8 py-6 text-center text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Kuota</th>
                            <th class="px-8 py-6 text-center text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Status</th>
                            <th class="px-8 py-6 text-right text-sm font-bold text-gray-700 dark:text-gray-200 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @forelse($promos as $promo)
                            <tr class="hover:bg-gray-50 transition-colors duration-200 group">
                                <td class="px-8 py-6 whitespace-nowrap">
                                    <div class="text-sm font-bold text-primary capitalize">{{ $promo->kode_promo }}</div>
                                    <div class="text-xs text-textGray truncate max-w-[200px] mt-1">{{ $promo->deskripsi ?: '-' }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm font-semibold text-textDark">
                                    {{ $promo->diskon_persen }}%
                                    @if($promo->maksimal_diskon)
                                        <div class="text-xs text-textGray font-normal mt-1">Max: Rp {{ number_format($promo->maksimal_diskon, 0, ',', '.') }}</div>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-sm text-textGray font-medium">
                                    <span class="text-textDark">{{ $promo->tanggal_mulai->format('d M Y') }}</span>
                                    <div class="text-xs mt-1">s/d {{ $promo->tanggal_selesai->format('d M Y') }}</div>
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-center">
                                    @if($promo->kuota_total)
                                        @php
                                            $sisa = $promo->kuota_total - $promo->kuota_terpakai;
                                        @endphp
                                        <div class="text-sm font-bold {{ $sisa > 0 ? 'text-primary' : 'text-danger' }}">
                                            {{ $sisa }} / {{ $promo->kuota_total }}
                                        </div>
                                        <div class="text-xs text-textGray mt-1">Tersisa</div>
                                    @else
                                        <div class="text-sm font-bold text-primary">Unlimited</div>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-center">
                                    @if($promo->isValid())
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                            Tidak Aktif / Habis
                                        </span>
                                    @endif
                                </td>
                                <td class="px-8 py-6 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-end space-x-3">
                                        <a href="{{ route('admin.promo.edit', $promo->id) }}" wire:navigate class="group p-2 text-primary hover:text-primaryDark transition-all duration-200 bg-primaryLight/10 rounded-xl hover:bg-primaryLight/30 border border-primaryLight/20 hover:border-primaryLight/50 form-element" title="Edit Data">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <button wire:click="delete({{ $promo->id }})" wire:confirm="Apakah Anda yakin ingin menghapus promo beserta datanya?" class="group p-2 text-red-500 hover:text-red-700 transition-all duration-200 bg-red-50 rounded-xl hover:bg-red-100 border border-red-100 hover:border-red-200 form-element" title="Hapus Data">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-8 py-16 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="mx-auto h-24 w-24 bg-gray-50 rounded-full flex items-center justify-center mb-6">
                                            <svg class="h-12 w-12 text-textGray" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-bold text-textDark mb-2">Tidak ada data promo</h3>
                                        <p class="text-sm text-textGray mb-6">Mulai dengan menambahkan promo diskon baru.</p>
                                        <a href="{{ route('admin.promo.create') }}"
                                            class="inline-flex items-center gap-2 px-6 py-3 bg-primary hover:bg-primaryDark text-white font-semibold rounded-xl transition-colors"
                                            wire:navigate>
                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            <span>Buat Promo Pertama</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($promos->hasPages())
                <div class="px-8 py-6 bg-gray-50 border-t border-inputBorder">
                    {{ $promos->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

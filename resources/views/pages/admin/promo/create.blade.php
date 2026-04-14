<?php

use App\Models\Promo;
use Livewire\Volt\Component;

new class extends Component {
    public $kode_promo = '';
    public $deskripsi = '';
    public $diskon_persen = '';
    public $maksimal_diskon = '';
    public $kuota_total = '';
    public $tanggal_mulai = '';
    public $tanggal_selesai = '';
    public $is_active = true;

    protected function rules()
    {
        return [
            'kode_promo' => 'required|string|max:50|unique:promos,kode_promo',
            'deskripsi' => 'nullable|string',
            'diskon_persen' => 'required|integer|min:1|max:100',
            'maksimal_diskon' => 'nullable|integer|min:0',
            'kuota_total' => 'nullable|integer|min:1',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'is_active' => 'boolean',
        ];
    }

    public function mount()
    {
        $this->tanggal_mulai = now()->format('Y-m-d');
        $this->tanggal_selesai = now()->addDays(30)->format('Y-m-d');
    }

    public function save()
    {
        $validated = $this->validate();

        // Uppercase kode promo biar konsisten
        $validated['kode_promo'] = strtoupper($validated['kode_promo']);

        if ($validated['maksimal_diskon'] === '')
            $validated['maksimal_diskon'] = null;
        if ($validated['kuota_total'] === '')
            $validated['kuota_total'] = null;

        Promo::create($validated);

        $this->dispatch('swal:toast', title: 'Promo berhasil ditambahkan!', icon: 'success');
        $this->redirectRoute('admin.promo.index', navigate: true);
    }
}
?>

<div class="w-full max-w-none">
    <div class="mb-8">
        <a href="{{ route('admin.promo.index') }}" wire:navigate
            class="inline-flex items-center text-sm font-medium text-textGray hover:text-primary transition-colors mb-4">
            <svg class="h-4 w-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali ke Daftar
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Tambah Promo Baru</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-300 font-medium">Isi formulir di bawah untuk membuat kode
            promo baru.</p>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm p-8">
        <form wire:submit="save" class="space-y-8">

            <!-- Section: Detail Promo & Deskripsi -->
            <div x-data="{ open: true }"
                class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between text-left focus:outline-none">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                        <div
                            class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primaryDark" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        Informasi Promo Utama
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                        :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kode Promo -->
                        <div>
                            <label class="block text-sm font-semibold text-textDark mb-2">Kode Promo <span
                                    class="text-red-500">*</span></label>
                            <input type="text" wire:model="kode_promo"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors uppercase"
                                placeholder="Contoh: MERDEKA2026">
                            @error('kode_promo') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                            <p class="text-xs text-textGray mt-1">Gunakan kombinasi huruf kapital dan angka tanpa spasi.
                            </p>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-semibold text-textDark mb-2">Status Promo <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <select wire:model="is_active"
                                    class="w-full px-4 py-3 pr-10 appearance-none border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors cursor-pointer bg-white">
                                    <option value="1">Aktif</option>
                                    <option value="0">Tidak Aktif</option>
                                </select>
                                <div
                                    class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-gray-500">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </div>
                            @error('is_active') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Besaran Diskon -->
                        <div>
                            <label class="block text-sm font-semibold text-textDark mb-2">Besaran Diskon (%) <span
                                    class="text-red-500">*</span></label>
                            <div class="relative">
                                <input type="number" wire:model="diskon_persen"
                                    class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                    placeholder="Contoh: 10" min="1" max="100">
                                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-medium">%</span>
                                </div>
                            </div>
                            @error('diskon_persen') <span class="text-danger text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Maksimal Diskon -->
                        <div>
                            <label class="block text-sm font-semibold text-textDark mb-2">Maksimal Potongan Rupiah <span
                                    class="text-xs text-textGray font-normal">(Opsional)</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-medium">Rp</span>
                                </div>
                                <input type="number" wire:model="maksimal_diskon"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                    placeholder="Kosongkan jika tanpa batas">
                            </div>
                            @error('maksimal_diskon') <span class="text-danger text-xs mt-1">{{ $message }}</span>
                            @enderror
                            <p class="text-xs text-textGray mt-1">Batas nilai maksimal yang dipotong. Biarkan kosong
                                jika
                                diskon % tidak dibatasi.</p>
                        </div>
                    </div>

                    <!-- Deskripsi Promo -->
                    <div>
                        <label class="block text-sm font-semibold text-textDark mb-2">Deskripsi Promo <span
                                class="text-xs text-textGray font-normal">(Opsional)</span></label>
                        <textarea wire:model="deskripsi" rows="3"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="Contoh: Promo akhir tahun khusus member baru"></textarea>
                        @error('deskripsi') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Pengaturan Waktu & Kuota -->
            <div x-data="{ open: true }"
                class="bg-gray-50 border border-inputBorder rounded-2xl p-6 transition-all duration-300">
                <button type="button" @click="open = !open"
                    class="w-full flex items-center justify-between text-left focus:outline-none">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-3">
                        <div
                            class="w-8 h-8 bg-primaryLight/20 rounded-xl flex items-center justify-center border border-primaryLight/30">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-primaryDark" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path fill-rule="evenodd"
                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        Masa Berlaku & Kuota
                    </h3>
                    <svg class="w-5 h-5 text-gray-400 transform transition-transform duration-200"
                        :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open" x-transition:enter="transition ease-out duration-200"
                    x-transition:enter-start="opacity-0 -translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-150"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 -translate-y-2" class="mt-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Tanggal Mulai -->
                        <div>
                            <label class="block text-sm font-semibold text-textDark mb-2">Penyewaan Dari <span
                                    class="text-red-500">*</span></label>
                            <input type="date" wire:model="tanggal_mulai"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            @error('tanggal_mulai') <span class="text-danger text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Tanggal Selesai -->
                        <div>
                            <label class="block text-sm font-semibold text-textDark mb-2">Sampai Tanggal <span
                                    class="text-red-500">*</span></label>
                            <input type="date" wire:model="tanggal_selesai"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                            @error('tanggal_selesai') <span class="text-danger text-xs mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Kuota Total -->
                        <div>
                            <label class="block text-sm font-semibold text-textDark mb-2">Kuota Pengguna <span
                                    class="text-xs text-textGray font-normal">(Opsional)</span></label>
                            <input type="number" wire:model="kuota_total"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Kosongkan jika unlimited">
                            @error('kuota_total') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                            <p class="text-xs text-textGray mt-1">Batas jumlah klaim promo untuk total customer.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-8 border-t border-gray-200 mt-8">
                <a href="{{ route('admin.promo.index') }}" wire:navigate
                    class="px-6 py-3 bg-gray-100 hover:bg-gray-200 border-none text-textDark font-bold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-primary hover:bg-primaryDark text-white font-bold rounded-xl shadow-sm shadow-primary/30 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Simpan Promo
                </button>
            </div>
        </form>
    </div>
</div>
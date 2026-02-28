<?php

use App\Models\Promo;
use Livewire\Volt\Component;

new class extends Component {
    public Promo $promo;

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
            'kode_promo' => 'required|string|max:50|unique:promos,kode_promo,' . $this->promo->id,
            'deskripsi' => 'nullable|string',
            'diskon_persen' => 'required|integer|min:1|max:100',
            'maksimal_diskon' => 'nullable|integer|min:0',
            'kuota_total' => 'nullable|integer|min:1',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'is_active' => 'boolean',
        ];
    }

    public function mount(Promo $promo)
    {
        $this->promo = $promo;

        $this->kode_promo = $promo->kode_promo;
        $this->deskripsi = $promo->deskripsi;
        $this->diskon_persen = $promo->diskon_persen;
        $this->maksimal_diskon = $promo->maksimal_diskon;
        $this->kuota_total = $promo->kuota_total;
        $this->tanggal_mulai = $promo->tanggal_mulai->format('Y-m-d');
        $this->tanggal_selesai = $promo->tanggal_selesai->format('Y-m-d');
        $this->is_active = $promo->is_active;
    }

    public function save()
    {
        $validated = $this->validate();

        $validated['kode_promo'] = strtoupper($validated['kode_promo']);

        if ($validated['maksimal_diskon'] === '')
            $validated['maksimal_diskon'] = null;
        if ($validated['kuota_total'] === '')
            $validated['kuota_total'] = null;

        // Cek peringatan jika stok kuota baru lebih kecil dari sudah dipakai
        if ($validated['kuota_total'] !== null && $validated['kuota_total'] < $this->promo->kuota_terpakai) {
            $this->addError('kuota_total', 'Kuota total tidak boleh kurang dari kuota yang sudah dipakai (' . $this->promo->kuota_terpakai . ').');
            return;
        }

        $this->promo->update($validated);

        $this->dispatch('swal:toast', title: 'Promo berhasil diperbarui!', icon: 'success');
        $this->redirectRoute('admin.promo.index', navigate: true);
    }
}
?>

<div>
    <div class="mb-6 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-textDark flex items-center gap-2">
                <a href="{{ route('admin.promo.index') }}" wire:navigate class="hover:text-primary transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                Edit Data Promo
            </h1>
            <p class="text-textGray text-sm mt-1 ml-8">Ubah rincian informasi dan status kode promo diskon.</p>
        </div>
    </div>

    <!-- Info Kuota Terpakai (Read Only Reminder) -->
    <div class="mb-4 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                    fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Promo ini sudah digunakan sebanyak <strong>{{ $promo->kuota_terpakai }} kali</strong> oleh
                    pelanggan.
                </p>
            </div>
        </div>
    </div>

    <form wire:submit="save" class="bg-white rounded-xl shadow-card overflow-hidden">
        <div class="p-6 md:p-8 space-y-8">

            <!-- Section: Detail Promo -->
            <div class="border border-gray-100 rounded-lg p-5 bg-gray-50/50">
                <h3 class="text-base font-semibold text-textDark mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd" />
                    </svg>
                    Informasi Promo Utama
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Kode Promo -->
                    <div>
                        <label class="block text-sm font-medium text-textDark mb-2">Kode Promo *</label>
                        <input type="text" wire:model="kode_promo"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors uppercase"
                            placeholder="Contoh: MERDEKA2026">
                        @error('kode_promo') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-textDark mb-2">Status Promo *</label>
                        <select wire:model="is_active"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors appearance-none bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%239CA3AF%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E')] bg-[length:12px_12px] bg-[right_1rem_center] bg-no-repeat pr-10">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                        @error('is_active') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Besaran Diskon -->
                    <div>
                        <label class="block text-sm font-medium text-textDark mb-2">Besaran Diskon (%) *</label>
                        <div class="relative">
                            <input type="number" wire:model="diskon_persen"
                                class="w-full pl-4 pr-10 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Contoh: 10" min="1" max="100">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">%</span>
                            </div>
                        </div>
                        @error('diskon_persen') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Maksimal Diskon -->
                    <div>
                        <label class="block text-sm font-medium text-textDark mb-2">Maksimal Potongan Rupiah <span
                                class="text-xs text-textGray font-normal">(Opsional)</span></label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">Rp</span>
                            </div>
                            <input type="number" wire:model="maksimal_diskon"
                                class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                                placeholder="Kosongkan jika tanpa batas">
                        </div>
                        @error('maksimal_diskon') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Section: Pengaturan Waktu & Kuota -->
            <div class="border border-gray-100 rounded-lg p-5 bg-gray-50/50">
                <h3 class="text-base font-semibold text-textDark mb-4 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                            clip-rule="evenodd" />
                    </svg>
                    Masa Berlaku & Kuota
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Tanggal Mulai -->
                    <div>
                        <label class="block text-sm font-medium text-textDark mb-2">Penyewaan Dari *</label>
                        <input type="date" wire:model="tanggal_mulai"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        @error('tanggal_mulai') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Selesai -->
                    <div>
                        <label class="block text-sm font-medium text-textDark mb-2">Sampai Tanggal *</label>
                        <input type="date" wire:model="tanggal_selesai"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors">
                        @error('tanggal_selesai') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Kuota Total -->
                    <div>
                        <label class="block text-sm font-medium text-textDark mb-2">Kuota Pengguna <span
                                class="text-xs text-textGray font-normal">(Opsional)</span></label>
                        <input type="number" wire:model="kuota_total"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                            placeholder="Kosongkan jika unlimited">
                        @error('kuota_total') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Deskripsi Promo -->
            <div>
                <label class="block text-sm font-medium text-textDark mb-2">Deskripsi Promo <span
                        class="text-xs text-textGray font-normal">(Opsional)</span></label>
                <textarea wire:model="deskripsi" rows="3"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-primary transition-colors"
                    placeholder="Contoh: Promo akhir tahun khusus member baru"></textarea>
                @error('deskripsi') <span class="text-danger text-xs mt-1">{{ $message }}</span> @enderror
            </div>

        </div>

        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end gap-3 flex-wrap">
            <a href="{{ route('admin.promo.index') }}" wire:navigate
                class="px-6 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-100 font-medium transition-colors text-sm w-full sm:w-auto text-center">
                Batal
            </a>
            <button type="submit"
                class="px-6 py-2.5 bg-primary text-white rounded-lg hover:bg-primaryDark font-medium transition-colors shadow-sm text-sm w-full sm:w-auto">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>
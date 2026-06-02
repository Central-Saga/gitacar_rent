<?php

use App\Models\Pemesanan;
use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component {
    public int $count = 0;

    public function mount(): void
    {
        $this->refreshCount();
    }

    #[On('pemesanan-updated')]
    public function refreshCount(): void
    {
        $this->count = Pemesanan::query()
            ->whereIn('status_pemesanan', ['menunggu_konfirmasi', 'disetujui'])
            ->count();
    }
};
?>

<span
    wire:poll.60s="refreshCount"
    @class([
        'ms-auto inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 text-[11px] font-semibold rounded-full',
        'bg-primary text-white' => $count > 0,
        'bg-zinc-200 text-zinc-500 dark:bg-zinc-700 dark:text-zinc-300' => $count === 0,
    ])
>
    {{ $count > 99 ? '99+' : $count }}
</span>

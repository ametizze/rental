<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\AssetCategory;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    #[Url] public string $q = '';
    #[Url] public ?int $category = null;
    #[Url] public ?string $status = null;

    public function updatingQ()
    {
        $this->resetPage();
    }
    public function updatingCategory()
    {
        $this->resetPage();
    }
    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $tid = session('tenant_id');

        $categories = AssetCategory::where('tenant_id', $tid)->orderBy('name')->get();

        $assets = Asset::query()
            ->when($tid, fn($q) => $q->where('tenant_id', $tid))
            ->when($this->q, fn($q) => $q->where(function ($w) {
                $w->where('code', 'like', "%{$this->q}%")
                    ->orWhere('make', 'like', "%{$this->q}%")
                    ->orWhere('model', 'like', "%{$this->q}%")
                    ->orWhere('serial_number', 'like', "%{$this->q}%");
            }))
            ->when($this->category, fn($q) => $q->where('category_id', $this->category))
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->with(['photos' => fn($q) => $q->latest()->limit(1)])
            ->orderBy('code')
            ->paginate(10);

        return view('livewire.assets.index', compact('assets', 'categories'));
    }
}

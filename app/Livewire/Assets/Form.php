<?php

namespace App\Livewire\Assets;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AssetPhoto;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Livewire\WithFileUploads;

class Form extends Component
{
    use WithFileUploads;

    #[Locked] public ?int $assetId = null;

    public ?int $category_id = null;
    public string $code = '';
    public ?string $make = null;
    public ?string $model = null;
    public ?string $serial_number = null;
    public ?int $year = null;
    public string $status = 'available';
    public ?string $price_per_day = null; // human input, casts to cents
    public ?string $description = null;

    // inline category creation (minimal)
    public ?string $newCategory = null;

    // photos
    public array $photos = []; // temporary uploads
    public array $existingPhotos = [];

    public function mount(?Asset $asset = null): void
    {
        if ($asset && $asset->exists) {
            $this->assetId = $asset->id;
            $this->category_id   = $asset->category_id;
            $this->code          = $asset->code;
            $this->make          = $asset->make;
            $this->model         = $asset->model;
            $this->serial_number = $asset->serial_number;
            $this->year          = $asset->year;
            $this->status        = $asset->status;
            $this->price_per_day = $asset->price_per_day !== null ? number_format($asset->price_per_day, 2, '.', '') : null;
            $this->description   = $asset->description;
            $this->existingPhotos = $asset->photos()->orderBy('id')->get()->map(fn($p) => [
                'id' => $p->id,
                'path' => $p->path,
                'caption' => $p->caption
            ])->toArray();
        }
    }

    public function rules(): array
    {
        $tid = session('tenant_id');
        return [
            'code' => [
                'required',
                'max:30',
                Rule::unique('assets')->where(fn($q) => $q->where('tenant_id', $tid))->ignore($this->assetId)
            ],
            'category_id' => ['nullable', 'integer'],
            'make' => ['nullable', 'max:120'],
            'model' => ['nullable', 'max:120'],
            'serial_number' => ['nullable', 'max:120'],
            'year' => ['nullable', 'integer', 'between:1900,' . (date('Y') + 1)],
            'status' => ['required', 'in:available,rented,maintenance'],
            'price_per_day' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable'],
            'photos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'newCategory' => ['nullable', 'max:120'],
        ];
    }

    public function saveCategory(): void
    {
        $this->validateOnly('newCategory');
        $tid = session('tenant_id');
        if (!$tid || !$this->newCategory) return;

        $cat = AssetCategory::firstOrCreate(
            ['tenant_id' => $tid, 'name' => trim($this->newCategory)],
            []
        );
        $this->category_id = $cat->id;
        $this->newCategory = null;
        $this->dispatch('toast', body: 'Category created.');
    }

    public function removePhoto(int $photoId): void
    {
        $this->existingPhotos = array_values(array_filter($this->existingPhotos, fn($p) => $p['id'] !== $photoId));
        // real delete happens on save() to keep things atomic/minimal
    }

    public function save()
    {
        $tid = session('tenant_id');
        if (!$tid) {
            $this->addError('tenant', 'Select a tenant.');
            return;
        }

        $data = $this->validate();
        $payload = [
            'tenant_id' => $tid,
            'category_id' => $this->category_id,
            'code' => $this->code,
            'make' => $this->make,
            'model' => $this->model,
            'serial_number' => $this->serial_number,
            'year' => $this->year,
            'status' => $this->status,
            'description' => $this->description,
        ];
        // cents conversion via accessor-compatible field
        if ($this->price_per_day !== null && $this->price_per_day !== '') {
            $payload['price_per_day_cents'] = (int) round(((float) $this->price_per_day) * 100);
        } else {
            $payload['price_per_day_cents'] = null;
        }

        $asset = Asset::updateOrCreate(['id' => $this->assetId], $payload);
        $this->assetId = $asset->id;

        // Delete photos removed in UI
        $keepIds = collect($this->existingPhotos)->pluck('id')->all();
        $asset->photos()->whereNotIn('id', $keepIds)->delete();

        // Upload new photos
        foreach ($this->photos as $upload) {
            $path = $upload->store("assets/{$tid}", 'public');
            AssetPhoto::create([
                'tenant_id' => $tid,
                'asset_id'  => $asset->id,
                'path'      => $path,
            ]);
        }
        $this->photos = [];

        session()->flash('success', 'Asset saved.');
        return redirect()->route('assets.show', $asset);
    }

    public function render()
    {
        $tid = session('tenant_id');
        $categories = AssetCategory::where('tenant_id', $tid)->orderBy('name')->get();
        return view('livewire.assets.form', compact('categories'));
    }
}

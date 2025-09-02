<?php

namespace App\View\Components\Nav;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Component;

class Main extends Component
{
    public function __construct(
        public ?array $items = null,
        public ?bool  $showSwitcher = null
    ) {}

    public function render(): View|Closure|string
    {
        $items = $this->items ?? $this->defaultItems();
        $items = array_values(array_filter($items, fn($i) => isset($i['route']) && Route::has($i['route'])));

        $current = request()->route()?->getName() ?? '';
        foreach ($items as &$i) {
            $name = $i['route'];
            $i['active'] = $current === $name || str_starts_with($current, strtok($name, '.') . '.');
        }

        return view('components.nav.main', [
            'items'        => $items, // never null
            'showSwitcher' => $this->showSwitcher ?? $this->shouldShowTenantSwitcher(),
        ]);
    }

    private function defaultItems(): array
    {
        return [
            ['label' => __('messages.dashboard'),  'route' => 'home'],
            ['label' => __('messages.customers'),  'route' => 'customers.index'],
            ['label' => __('messages.assets'),     'route' => 'assets.index'],
            ['label' => __('messages.rentals'),    'route' => 'rentals.index'],
        ];
    }

    private function shouldShowTenantSwitcher(): bool
    {
        if (!auth()->check()) return false;

        if (function_exists('is_platform_admin') && is_platform_admin(auth()->id())) {
            return true;
        }

        try {
            return DB::table('user_tenants')->where('user_id', auth()->id())->count() > 1;
        } catch (\Throwable) {
            return false;
        }
    }
}

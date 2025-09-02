<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    public function index()
    {
        return view('assets.index');
    }
    public function create()
    {
        return view('assets.form');
    }

    public function show(\App\Models\Asset $asset)
    {
        if (session('tenant_id') && $asset->tenant_id !== session('tenant_id')) abort(404);
        return view('assets.show', compact('asset'));
    }
    public function edit(\App\Models\Asset $asset)
    {
        if (session('tenant_id') && $asset->tenant_id !== session('tenant_id')) abort(404);
        return view('assets.form', compact('asset'));
    }
}

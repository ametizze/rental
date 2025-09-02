@extends('layouts.app')
@section('title', 'Asset Details')
@section('content')
    <livewire:assets.show :asset="$asset" />
@endsection

@extends('layouts.app')
@section('title', 'Assets')
@section('content')
    <livewire:assets.form :asset="$asset ?? null" />
@endsection

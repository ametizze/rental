@extends('layouts.app')
@section('title', 'Edit customer')
@section('content')
    <livewire:customers.form :customer="$customer" />
@endsection

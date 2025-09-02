@extends('layouts.app')
@section('title', 'Customer')
@section('content')
    <livewire:customers.show :customer="$customer" />
@endsection

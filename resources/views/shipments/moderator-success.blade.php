@extends('layouts.app')

@section('title', 'شحنة جديدة')

@section('content')
<div class="container text-center mt-5">
    <h2 class="mb-4">✅ تم إضافة شحنة جديدة بنجاح!</h2>

    <a href="{{ route('shipments.create') }}" class="btn btn-primary">
        ➕ إضافة شحنة أخرى
    </a>
</div>
@endsection

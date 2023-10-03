@extends('layouts.app')
@section('content')
    @foreach($artisans as $artisan)
    <div class="mockup-code">
        <pre data-prefix="$"><code>{{ $artisan }}</code></pre>
    </div>
        <div class="mb-4"></div>
    @endforeach
@endsection

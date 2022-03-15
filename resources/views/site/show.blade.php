<!-- create.blade.php -->

@extends('layout')

@section('content')
<style>
    .uper {
        margin-top: 40px;
    }
</style>
<div class="card uper">
    <div class="card-header">
        Site Crawl Status
    </div>
    <div class="card-body">
        <h5 class="card-title">Site Data for {{ $site->url }}</h5>
        <p class="card-text" id="load-status">Please wait while we extract processing status</p>
        <a href="{{ route('site.getdata', ['site' => $site->id]) }}" class="btn btn-primary">See Details</a>
    </div>
    <script>
        setTimeout(function() {
            updateStatus("{{$site->id}}")
        }, 3000)
    </script>
</div>
@endsection
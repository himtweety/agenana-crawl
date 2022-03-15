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
        Input url and depth for crawling
    </div>
    <div class="card-body">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div><br />
        @endif
        <form method="post" action="{{ route('site.store') }}">
            <div class="form-group">
                @csrf
                <label for="name">Site Url:</label>
                <input type="text" class="form-control" name="url" />
            </div>
            <div class="form-group">
                <label for="price">Depth Of Crawl :</label>
                <input type="text" class="form-control" name="max_depth" />
            </div><br />
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Crawl Website</button>
            </div>
        </form>
    </div>
</div>
@endsection
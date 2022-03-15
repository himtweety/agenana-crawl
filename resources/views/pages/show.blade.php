<!-- create.blade.php -->

@extends('layout')

@section('content')
<style>
    .uper {
        margin-top: 40px;
    }

    #pages {
        font-family: Arial, Helvetica, sans-serif;
        border-collapse: collapse;
        width: 100%;
    }

    #pages td,
    #pages th {
        border: 1px solid #ddd;
        padding: 8px;
    }

    #pages tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    #pages tr:hover {
        background-color: #ddd;
    }

    #pages th {
        padding-top: 12px;
        padding-bottom: 12px;
        text-align: left;
        background-color: #04AA6D;
        color: white;
    }
</style>
<div class="card uper">
    <div class="card-header">
        Crawled Pages for Site
    </div>
    <div class="card-body">

        <div class="container">
            <div class="row">
                <div class="d-felx justify-content-center">

                    <fieldset class="form-group">
                        <div class="row">
                            <div class="form-group row">
                                <label class="col-sm-12">
                                    <p class="card-text" id="load-status">Please wait while we extract processing status</p>
                                </label>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Unique/Total External Links</label>
                            <div class="col-sm-10">
                                {{$siteData["unique_external"]}} / {{$siteData["all_external"]}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Unique/Total Internal Links</label>
                            <div class="col-sm-10">
                                {{$siteData["unique_internal"]}} / {{$siteData["all_internal"]}}
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">Unique Images/Total Images</label>
                            <div class="col-sm-10">
                                {{$siteData["unique_images"]}} / {{$siteData["total_images"]}}
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <table width="100%" id="pages">
                <tr>
                    <th>Id</th>
                    <th>Page Url</th>
                    <th>Page Path</th>
                    <th>Total Links</th>
                    <th>Unique Links</th>
                    <th>Tital Images</th>
                    <th>Unique Images</th>
                    <th>loadTime (seconds)</th>
                    <th>External/Internal</th>
                </tr>
                @foreach($pages as $page)
                <tr>
                    <td>{{$page->id}}</td>
                    <td>{{$page->base_url}}</td>
                    <td>{{$page->path}}</td>
                    <td>{{$page->total_links}}</td>
                    <td>{{$page->unique_links}}</td>
                    <td>{{$page->total_images}}</td>
                    <td>{{$page->unique_images}}</td>
                    <td>{{$page->request_time}}</td>
                    <td>@if ($page->external == 1)
                        External
                        @else
                        Internal
                        @endif</td>
                </tr>
                @endforeach
                <tr>
                    <td colspan="9" class=" justify-content-center">
                        <div class="d-felx justify-content-center">

                            {{ $pages->links() }}

                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>
</div>
<script>
    setTimeout(function() {
        updateStatus("{{$site->id}}")
    }, 3000)
</script>
@endsection
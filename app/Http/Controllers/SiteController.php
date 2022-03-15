<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class SiteController extends Controller
{
    public function create()
    {
        return view('site.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'url' => 'required|url|max:255',
            'max_depth' => 'required|numeric|min:1|max:6',
        ]);
        $siteObj = Site::create($validatedData);

        $urlData = parse_url($siteObj->url);

        $pageData = [
            'scheme' => $urlData['scheme'],
            'host' => $urlData['host'],
            "path" => $urlData["path"],
            "base_url" => $urlData['scheme'] . "://" . $urlData["host"],
            "site_id" => $siteObj->id,
            "level" => 0
        ];
        Page::create($pageData);

        return redirect()->route('site.result', ['site' =>  $siteObj->id]);
    }


    // Controller method definition...
    public function show(Site $site)
    {
        $pagesData = Page::select(['status', DB::raw("count(id) as count")])->where('site_id', $site->id)->groupBy('status')->get()->toArray();


        return view('site.show', ['site' => $site, 'pageData' => $pagesData]);
    }
    // Controller method definition...
    public function showPagesCrawlStatus(Site $site)
    {
        $pagesData = Page::select(['status', DB::raw("count(id) as count")])->where('site_id', $site->id)->groupBy('status')->get();
        $dataObject = ['completed' => 0, 'pending' => 0, "failed" => 0];
        foreach ($pagesData as $resp) {
            if ($resp->status == true) {
                $dataObject['completed'] = $resp->count;
            } else {
                $dataObject['pending'] = $resp->count;
            }
        }
        return Response::json($dataObject);
    }
}

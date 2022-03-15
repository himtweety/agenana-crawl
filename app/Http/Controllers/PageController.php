<?php

namespace App\Http\Controllers;

use App\Models\Image;
use App\Models\Link;
use App\Models\Page;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    //

    public function get_data(Site $site)
    {
        $pages = Page::where('site_id', $site->id)->paginate(25);

        // stats
        $siteData = [
            'all_internal' => 0,
            'all_external' => 0,
            'unique_internal' => 0,
            'unique_external' => 0,
            'total_images' => 0,
            'unique_images' => 0,
        ];

        $totalExternalInternalLinks = Link::select(['external', DB::raw("count(id) as count")])->where('site_id', $site->id)->groupBy('external')->get();
        foreach ($totalExternalInternalLinks as $resp) {
            if ($resp->external == true) {
                $siteData['all_external'] = $resp->count;
            } else {
                $siteData['all_internal'] = $resp->count;
            }
        }
        $uniqueExternalInternalLinks = Link::select(['external', DB::raw("count(distinct(path)) as count")])->where('site_id', $site->id)->groupBy('external')->get();
        foreach ($uniqueExternalInternalLinks as $resp) {
            if ($resp->external == true) {
                $siteData['unique_external'] = $resp->count;
            } else {
                $siteData['unique_internal'] = $resp->count;
            }
        }
        $totalImages = Image::select([DB::raw("count(*) as count")])->where('site_id', $site->id)->first();
        $uniqueImages = Image::select([DB::raw("count(distinct(src)) as count")])->where('site_id', $site->id)->first();
        $siteData['total_images'] = $totalImages->count;
        $siteData['unique_images'] = $uniqueImages->count;
        return view('pages.show', compact('site', 'siteData', 'pages'));
    }
}

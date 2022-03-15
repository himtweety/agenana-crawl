<?php

namespace App\Listeners;

use App\Events\PageCreated;
use App\Models\Image;
use App\Models\Link;
use App\Models\Page;
use App\Models\Site;
use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;

class PageCrawlOnCreate implements ShouldQueue
{
    /**
     * The page instance.
     *
     * @var \App\Models\Page
     */
    protected $page;
    /**
     * Create a new event listener.
     *
     * @return void
     */
    public function __construct(Page $page)
    {
        //
        $this->page = $page;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\PageCreated  $event
     * @return void
     */
    public function handle(PageCreated $event)
    {
        //
        Log::debug('Page Created Crawl Process started if it is internal link');
        $pageObject = $event->page;
        $siteObject = Site::find($pageObject->site_id);
        $maxDepth = $siteObject->max_depth;
        Log::debug('Site ID and Depth:' . $siteObject->max_depth . "/");
        $urlToCrawl = $pageObject->base_url . $pageObject->path;
        try {
            //code...

            Log::debug('Site ID and Depth:' . $siteObject->max_depth . "/");
            if (!$pageObject->external && $pageObject->level < $maxDepth) {
                $urlToCrawl = $pageObject->base_url . $pageObject->path;
                $response = Http::timeout(10)->withOptions([
                    'debug' => false,
                    'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                        return $stats->getTransferTime();
                    }
                ])->get($urlToCrawl);
                if ($response->successful()) {
                    $body = $response->body();

                    $dom = new DOMDocument('1.0', 'UTF-8');
                    $internalErrors = libxml_use_internal_errors(true);
                    $dom->loadHTML($body);
                    libxml_use_internal_errors($internalErrors);

                    $xpath = new DOMXPath($dom);
                    $node = $xpath->query('body')->item(0);
                    $htmlToText = $node->textContent;
                    $wordlength = collect(explode(" ", $htmlToText))->filter()->values()->count();
                    $list = $dom->getElementsByTagName("title");
                    $title = "";
                    if ($list->length > 0) {
                        $title = $list->item(0)->textContent;
                    }

                    $pageObject->title = $title;
                    $pageObject->word_length = $wordlength;
                    $pageObject->request_time = $response->transferStats->getTransferTime();
                    $pageObject->status = true;
                    $pageObject->save();
                    if ($pageObject->level == 0) {
                        $siteObject->title = $title;
                        $siteObject->status = 2;
                        $siteObject->save();
                    }
                    $hrefs = $this->storeLinkInformation($dom, $pageObject, $siteObject);
                    $images = $this->storeImagesInformation($dom, $pageObject);
                    if (!empty($hrefs)) {
                        // insertInto Pages table to crawl child pages
                        $fileterdArray = collect($hrefs)->filter(function ($link) use ($maxDepth) {
                            return ($link['level'] < $maxDepth) ?  !$link['external'] : false;
                        })->map(function ($hrefElement) {
                            $checkExistingPage = Page::where('path', $hrefElement['path'])
                                ->where('site_id', $hrefElement['site_id'])->first();
                            if (empty($checkExistingPage)) {
                                Page::create($hrefElement);
                            }
                        });
                    }
                } else {
                    Log::debug('DebugError: Crawl failed unable to request page');
                }
                $checkPendingCount = Page::where('site_id', $siteObject->id)->where('status', false)->count();
                $pageObject->status = true;
                $pageObject->save();
                if ($checkPendingCount == 0) {
                    $siteObject->title = $title;
                    $siteObject->status = 1;
                    $siteObject->save();
                }
            }
        } catch (\Exception $ex) {
            $pageObject->status = true;
            $pageObject->save();
            $checkPendingCount = Page::where('site_id', $siteObject->id)->where('status', false)->count();
            if ($checkPendingCount == 0) {
                $siteObject->title = $title;
                $siteObject->status = 1;
                $siteObject->save();
            }
            Log::debug('DebugError:' . $ex->getMessage());
        }

        exit;
    }
    /**
     * 
     */
    private function storeLinkInformation($domObject, $parentPageObject, $siteObject)
    {
        $tags = $domObject->getElementsByTagName('a');
        $linkArray = collect();
        $hrefs = [];
        foreach ($tags as $tag) {
            $hrefs[] =  $tag->getAttribute('href');
        }
        $parentPageObject->total_links = collect($hrefs)->values()->count();
        $parentPageObject->unique_links = collect($hrefs)->unique()->values()->count();
        $parentPageObject->save();
        $hrefs = collect($hrefs)->unique()->values()->map(function ($href) use ($parentPageObject, $siteObject) {
            $externalLink = false;
            $urlData = parse_url(trim(strtolower($href)));
            if (!empty($urlData["path"])) {

                $linkObject = [
                    "path" => $urlData["path"],
                    "page_id" => $parentPageObject->id,
                    "site_id" => $parentPageObject->site_id,
                    "level" => $parentPageObject->level + 1,
                    "href" => $href
                ];
                if (!empty($urlData['host'])) {
                    $linkObject['scheme'] = $urlData['scheme'];
                    $linkObject['host'] = $urlData['host'];
                    $linkObject['base_url'] = $urlData['scheme'] . "://" . $urlData["host"];
                }
                if (empty($urlData['host'])) {
                    $linkObject['scheme'] = $parentPageObject->scheme;
                    $linkObject['host'] = $parentPageObject->host;
                    $linkObject['base_url'] = $parentPageObject->scheme . "://" . $parentPageObject->host;
                }

                if ($linkObject['host'] != $parentPageObject->host) {
                    $externalLink = true;
                }
                $linkObject["external"] = $externalLink;
                return $linkObject;
            }
        });
        //insert page links
        Link::insert($hrefs->filter()->all());
        return $hrefs->filter()->all();
    }
    /**
     * 
     */
    private function storeImagesInformation($domObject, $parentPageObject)
    {
        $tags = $domObject->getElementsByTagName('img');
        $imagesCollection = collect();
        foreach ($tags as $tag) {
            $images[] =  $tag->getAttribute('data-src');
            $imageArray = [
                "site_id" => $parentPageObject->site_id,
                "page_id" => $parentPageObject->id,
                "src" => $tag->getAttribute('src'),
                "data-src" => $tag->getAttribute('data-src')
            ];
            if (empty($imageArray['src']))
                $imageArray["src"] = $imageArray["data-src"];
            $imagesCollection->push($imageArray);
        }
        $parentPageObject->total_images = collect($images)->values()->count();
        $parentPageObject->unique_images = collect($images)->unique()->values()->count();
        $parentPageObject->save();
        Image::insert($imagesCollection->all());
        return $images;
    }
}

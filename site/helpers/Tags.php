<?php

namespace Statamic\SiteHelpers;

use Statamic\Extend\Tags as AbstractTags;
use Statamic\API\URL;
use Statamic\API\Data;
use Statamic\Data\Services\PagesService;

class Tags extends AbstractTags
{
    /**
     * The {{ site:language_url }} tag
     *
     * @return mixed
     */
    public function languageUrl()
    {
        //
        $context = $this->context;
        $url = "";
        $route = "";
        $slug = "";
        $content_id = $context["id"];
        $content_uri = $context["uri"];
        $locale = $this->getParam('locale');
        if($locale == "default"){
            $test = Data::find($context["id"])->in("en");
            $url = $test->uri();
            return $url;
        }
        $contentObject = Data::find($context["id"])->in($locale);
        $data = $contentObject->get()->dataForLocale($locale);
        if( key_exists('slug',$data)  ){
            $slug = $data['slug'];
        }
        if( key_exists('is_entry',$context)  ){
            $collection = $context["collection"];
            $collectionRoute = $context["settings"]["routes"]["collections"][$collection];
            if( is_array($collectionRoute) ){
                $route = $collectionRoute[$locale];
            } elseif( is_string($collectionRoute) ) {
                $route = $collectionRoute;
            }
            if($slug != ""){
                $localized_url = str_replace("{slug}",$slug, $route );
            }
        }
        if( key_exists('is_page',$context)  ){
            $localized_url = app(PagesService::class)
                ->localizedUris($locale)
                ->get($content_id, $content_uri );
        }
        $url = URL::prependSiteUrl($localized_url , $locale);
        return $url ;
    }
}

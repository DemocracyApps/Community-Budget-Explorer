<?php namespace DemocracyApps\GB\Http\Controllers\Sites;

/**
 *
 * This file is part of the Government Budget Explorer (GBE).
 *
 *  The GBE is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GBE is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with the GBE.  If not, see <http://www.gnu.org/licenses/>.
 */

use DemocracyApps\GB\Http\Requests;
use DemocracyApps\GB\Http\Controllers\Controller;

use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Services\JsonProcessor;
use DemocracyApps\GB\Sites\Card;
use DemocracyApps\GB\Sites\CardSet;
use DemocracyApps\GB\Sites\Layout;
use DemocracyApps\GB\Sites\Page;
use DemocracyApps\GB\Sites\PageComponent;
use DemocracyApps\GB\Sites\Component;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Http\Request;
use Util;

class SitesController extends Controller {

    private static function bodySections($body)
    {
        $bodies = explode('<!--br-->', $body);
        return $bodies;
    }

	public function page($slug, $pageName=null)
    {
        $siteData = Site::where('slug','=',$slug)->first();
        $government = GovernmentOrganization::where('id','=',$siteData->government)->first();

        $site = new \stdClass();
        $site->name = $siteData->name;
        $site->id = 0;
        $site->slug = $siteData->slug;
        $site->startPage = -1;
        $site->baseUrl = url('/'.$slug);
        $site->apiUrl  = Util::apiPath() . "/organizations/" . $government->id;
        $site->ajaxUrl = Util::ajaxPath('sites', 'base');
        $site->properties = $siteData->properties;
        $site->categoryMap = null;

        if ($siteData->hasProperty('map') && $siteData->getProperty('map') != null) {
            $jp = new JsonProcessor();
            $mapFileName = $siteData->getProperty('map');
            $path = public_path() . '/data/maps/' . $mapFileName;
            $specification = \File::get($path);

            $str = $jp->minifyJson($specification);
            $cfig = $jp->decodeJson($str, true);
            if ($cfig != null) {
                $site->categoryMap = $cfig;
            }
        }
        $pages = $siteData->getPages();
        if ($pageName==null) $pageName = $pages[0]->shortName;

        $jp = new JsonProcessor();
        // We need to generate unique IDs for each configuration object
        $pageId = 0;
        $componentId = 0;
        $data = array();
        $cardStore = array();
        foreach ($pages as $page) {
            $page->id = $pageId++;
            if ($page->shortName == $pageName) {
                $site->startPage = $page->id;
            }
            $layout = ($page->layout == null)?null:Layout::find($page->layout);
            if ($layout != null) {
                $str = $jp->minifyJson($layout->specification);
                $cfig = $jp->decodeJson($str, true);
                if (!$cfig) {
                    throw new \Exception("Unable to parse layout specification " . $layout->name);
                }
                $page->layout = $cfig;
            }

            // Get the page components
            $pComponents = PageComponent::where('page','=',$page->tableId)->orderBy('id')->get();
            $page->components = array();
            $pd = new \Parsedown();
            foreach ($pComponents as $pc) {
                if ($pc->target != null) {
                    if (!array_key_exists($pc->target, $page->components)) $components[$pc->target] = array();
                    $c = new \stdClass();
                    $c->id = $componentId++;
                    $componentDefinition = Component::find($pc->component);
                    $c->componentName = $componentDefinition->name;
                    $c->componentType = $componentDefinition->type;
                    $c->componentData = null;
                    $c->componentProps = null;
                    $c->componentState = null;
                    if ($pc->properties != null) {
                        $c->componentData = array();
                        $c->componentProps = array();
                        $c->componentState = array();

                        if ($pc->hasProperty('props')) {
                            $props = $pc->getProperty('props');
                            // component has some properties to be set
                            foreach ($props as $key => $value) {
                                $c->componentProps[$key] = $value;
                            }
                        }
                        if ($pc->hasProperty('data')) {
                            $dataList = $pc->getProperty('data');
                            foreach ($dataList as $key => $dataItem) {
                                if ($dataItem['type'] == 'card') {
                                    $cId = $dataItem['items'][0];
                                    if (array_key_exists($cId, $cardStore)) {
                                        $card = $cardStore[$cId];
                                    }
                                    else {
                                        $storedCard = Card::find($cId);
                                        $card = $storedCard->asSimpleObject(['dataType' => 'card']);
                                        $card->body = array($pd->text($card->body));
                                        $cardStore[$cId] = array($card);
                                    }
                                    $data[] = $card;
                                    $c->componentData[$key] = array('type'=> 'card', 'ids'=>array($card->id));
                                } else if ($dataItem['type'] == 'cardset') {
                                    $csId = $dataItem['items'][0];
                                    $cardIdList = array();
                                    $cards = Card::where('card_set', '=', $csId)->orderBy('ordinal')->get();
                                    foreach ($cards as $card) {
                                        if (! array_key_exists($card->id, $cardStore)) {
                                            $cardStore[$card->id] = $card->asSimpleObject(['dataType' => 'card']);
                                            $bodies = self::bodySections($cardStore[$card->id]->body);
                                            $cardStore[$card->id]->body = array();
                                            foreach ($bodies as $body) {
                                                $cardStore[$card->id]->body[] = $pd->text($body);
                                            }
                                            $data[] = $cardStore[$card->id];
                                        }
                                        else {
                                            $card = $cardStore[$card->id];
                                        }
                                        $cardIdList[] = $card->id;
                                    }
                                    $c->componentData[$key] = array('type'=>'card', 'ids'=>$cardIdList);
                                } else if ($dataItem['type'] == 'dataset') {
                                    $dsId = $dataItem['items'][0]+0; // make it a number
                                    $ds = new \stdClass();
                                    $ds->dataType = 'dataset';
                                    $ds->id = $dsId;

                                    $data[] = $ds;
                                    $c->componentData[$key] = array('type'=>'dataset', 'ids'=>array($ds->id));

                                } else if ($dataItem['type'] == 'multidataset') {
                                    $idList = array();
                                    foreach ($dataItem['items'] as $item) {
                                        $dsetId = $item + 0; // make it a number
                                        $ds = new \stdClass();
                                        $ds->dataType = 'dataset';
                                        $ds->id = $dsetId;
                                        $data[] = $ds;
                                        $idList[] = $dsetId;
                                    }
                                    $c->componentData[$key] = array('type'=>'dataset', 'ids'=>$idList);
                                }
                            }
                        }
                    }
                    $page->components[$pc->target][] = $c;
                }
            }
        }

        // Putting site in an array to work around stupid issue in Jeff Way's PHPToJavaScriptTransformer
        // Stdclass objects are converted to JSON if they're in an array, but error out if not.
        return view('sites.appPage', array('site'=>[$site], 'pages'=>$pages, 'data' => $data, 'doAvb'=>true));
    }

    private function buildCardObject(Card $storedCard)
    {
        $card = new \stdClass();
        $card->dataType = 'card';
        $card->id = $storedCard->id;
        $card->title = $storedCard->title;
        $card->body = $storedCard->body;
        $card->image = $storedCard->image;
        $card->link = $storedCard->link;
        return $card;
    }

}

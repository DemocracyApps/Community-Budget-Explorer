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

class SitesController extends Controller {

	public function page($slug, $pageName, Request $request)
    {
        $site = Site::where('slug','=',$slug)->first();

        $page = Page::where('short_name','=',$pageName)->where('site','=',$site->id)->first();

        $government = GovernmentOrganization::where('id','=',$site->government)->first();

        $layout = ($page->layout == null)?null:Layout::find($page->layout);

        $jp = new JsonProcessor();

        $str = $jp->minifyJson($layout->specification);
        $cfig = $jp->decodeJson($str, true);
        if ( ! $cfig) {
            throw new \Exception("Unable to parse layout specification " . $layout->name);
        }
        $layout->specification = $cfig;

        $pages = Page::where('site','=',$site->id)->where('show_in_menu','=',true)->orderBy('ordinal')->get();

        // Get the page components
        $pComponents = PageComponent::where('page','=',$page->id)->get();
        $components = array();
        foreach ($pComponents as $pc) {
            if ($pc->target != null) {
                if (! array_key_exists($pc->target, $components)) $components[$pc->target] = array();
                $c = new \stdClass();
                $componentDefinition = Component::find($pc->component);
                $c->componentName = $componentDefinition->name;
                $c->componentType = $componentDefinition->type;
                $c->data = null;
                if ($pc->properties != null) {
                    $c->data = array();
                    $props = $jp->decodeJson($pc->properties, true);
                    if (array_key_exists('data', $props)) {
                        foreach ($props['data'] as $key => $dataItem) {
                            if ($dataItem['type'] == 'card') {
                                $storedCard = Card::find($dataItem['items'][0]);
                                $card = $storedCard->asSimpleObject(['dataType'=> 'card']);
                                $c->data[$key] = $card;
                            }
                            else if ($dataItem['type'] == 'cardset') {
                                $tmp = Cardset::find($dataItem['items'][0]);

                                $cardset = $tmp->asSimpleObject(['dataType'=>'cardset']);
                                $cardset->cards = array();
                                $cardObjects = Card::where('card_set', '=', $cardset->id)->orderBy('ordinal')->get();
                                foreach($cardObjects as $obj) {
                                    $cardset->cards[] = $obj->asSimpleObject();
                                }
                                $c->data[$key] = $cardset;
                            }
                            else if ($dataItem['type'] == 'dataset') {
                                $ds = new \stdClass();
                                $ds->dataType = 'dataset';
                                $ds->id = [$dataItem['items'][0]];
                                $c->data[$key] = $ds;
                            }
                            else if ($dataItem['type'] == 'dataset_list') {
                                $datasetList = new \stdClass();
                                $datasetList->dataType = 'dataset_list';
                                $datasetList->idList = array();
                                foreach ($dataItem['items'] as $dsetId) {
                                    $datasetList->idList[] = $dsetId;
                                }

                                $c->data[$key] = $datasetList;
                            }
                        }
                    }
                }
                $components[$pc->target][] = $c;
            }
        }
//dd($components);
        return view('sites.page', array('site'=>$site, 'government'=>$government, 'pages'=>$pages, 'page'=>$page, 'layout'=>$layout,
                                        'components'=>$components));
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

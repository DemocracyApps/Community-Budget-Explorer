<?php namespace DemocracyApps\GB\Sites;
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

use DemocracyApps\GB\Budget\AccountCategory;
use DemocracyApps\GB\Budget\AccountChart;
use DemocracyApps\GB\Budget\Dataset;
use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Organizations\MediaOrganization;
use DemocracyApps\GB\Users\User;
use DemocracyApps\GB\Utility\EloquentPropertiedObject;

class Site extends EloquentPropertiedObject
{

    protected $table = 'sites';
    const UNKNOWN = 0;
    const GOVERNMENT = 1;
    const MEDIA = 2;
    const INDIVIDUAL = 3;

    protected static $ownerTypeNames = ['Unknown', 'Government', 'Media', 'Individual'];

    /**
     * @return array
     */
    public function getPages()
    {
        $pageList = Page::where('site','=',$this->id)->where('show_in_menu','=',true)->orderBy('ordinal')->get();
        $pages = [];
        foreach ($pageList as $item) {
            $page = new \stdClass();
            $page->tableId = $item->id;
            $page->title = $item->title;
            $page->shortName = $item->short_name;
            $page->menuName = $item->menu_name;
            $page->description = $item->description;
            $page->layout = $item->layout;
            $page->properties = $item->properties;
            $pages[] = $page;
        }
        return $pages;
    }

    public function userHasAccess(User $user, $requiredLevel)
    {
        $hasAccess = false;
        $owner = null;
        if ($this->owner_type == self::GOVERNMENT) {
            $owner = GovernmentOrganization::find($this->owner);
        } else if ($this->owner_type == self::MEDIA) {
            $owner = MediaOrganization::find($this->owner);
        }
        if ($owner != null) {
            if ($owner->userHasAccess($user, $requiredLevel)) $hasAccess = true;
        }
        return $hasAccess;
    }

    public function getCardSets()
    {
        return CardSet::where('site', '=', $this->id)->orderBy('id')->get();
    }

    public function getCardsByCardSet()
    {
        $setList = CardSet::where('site', '=', $this->id)->orderBy('id')->get();

        $cardSets = [];
        foreach ($setList as $set) {
            $cardSets[$set->id] = new \stdClass();
            $cardSets[$set->id]->name = $set->name;
            $cardSets[$set->id]->id = $set->id;
            $cardSets[$set->id]->cards = Card::where('card_set', '=', $set->id)->orderBy('ordinal')->get();
        }
        return $cardSets;
    }

    public function getDatasets()
    {
        return Dataset::where('government_organization', '=', $this->government)->orderBy('year')->get();
    }

    public function getAccountCategories()
    {
        $chart = AccountChart::where('government_organization', '=', $this->government)->first();
        $acct_categories = AccountCategory::where('chart','=',$chart->id)->get();
        $categories = array();
        foreach ($acct_categories as $cat) {
            $categories[] = $cat->name;
        }

//        $ds = Dataset::where('government_organization', '=', $this->government)->orderBy('year', 'desc')->first();
//        $cOrder = json_decode($ds->category_order);
//        for ($i=0; $i<sizeof($cOrder); ++$i) {
//            $cat = AccountCategory::find($cOrder[$i]);
//            $categories[] = $cat->name;
//        }
        $categories[] = "Account";
        return $categories;
    }
}
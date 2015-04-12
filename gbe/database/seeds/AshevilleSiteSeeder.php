<?php

use DemocracyApps\GB\Sites\Card;
use DemocracyApps\GB\Sites\CardSet;
use DemocracyApps\GB\Sites\Page;
use DemocracyApps\GB\Sites\PageComponent;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Database\Seeder;

class AshevilleSiteSeeder extends Seeder
{

    public function run()
    {
        // Create the Asheville site
        $site = new Site();
        $site->name = "The City of Asheville Budget Site";
        $site->owner_type = Site::GOVERNMENT;
        $site->owner = 1; // Asheville government;
        $site->government = 1; // Ditto
        $site->slug = 'asheville';
        $site->published = true;
        $site->save();

        $cardset = new CardSet();
        $cardset->site = $site->id;
        $cardset->name = 'Slides';
        $cardset->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 1;
        $card->title = 'Budget Goals';
        $card->body = "These are the goals for the budget";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 2;
        $card->title = 'Revenue Highlights';
        $card->body = "These are the big changes to revenue";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 3;
        $card->title = 'Spending Highlights';
        $card->body = "These are the big changes to spending";
        $card->save();

        $page = new Page();
        $page->site = $site->id;
        $page->title = "First Page";
        $page->short_name = 'First';
        $page->ordinal = 1;
        $page->show_in_menu = true;
        $page->description = "The first page of the site.";
        $page->layout = 2;
        $page->save();

        $c = new PageComponent();
        $c->component = 1;
        $c->page = $page->id;
        $c->save();
        $c = new PageComponent();
        $c->component = 2;
        $c->page = $page->id;
        $c->save();

        $page = new Page();
        $page->site = $site->id;
        $page->title = "Second Page";
        $page->short_name = 'Second';
        $page->ordinal = 2;
        $page->show_in_menu = true;
        $page->description = "The second page of the site.";
        $page->layout = 1;
        $page->save();

        $page = new Page();
        $page->site = $site->id;
        $page->title = "Third Page";
        $page->short_name = 'Third';
        $page->ordinal = 3;
        $page->show_in_menu = true;
        $page->description = "The third page of the site.";
        $page->layout = 1;
        $page->save();

    }

}
<?php

use DemocracyApps\GB\Sites\Card;
use DemocracyApps\GB\Sites\CardSet;
use DemocracyApps\GB\Sites\Component;
use DemocracyApps\GB\Sites\Layout;
use DemocracyApps\GB\Sites\Page;
use DemocracyApps\GB\Sites\PageComponent;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Database\Seeder;

class AshevilleSiteSeeder extends Seeder
{

    public function run()
    {
        $slideShowComponent = Component::where('name','=','SlideShow')->first();
        $simpleCardComponent = Component::where('name','=','SimpleCard')->first();
        $barchartExplorerComponent = Component::where('name','=','BarchartExplorer')->first();
        $multiYearTableComponent = Component::where('name','=','MultiYearTable')->first();

        // Create the Asheville site
        $site = new Site();
        $site->name = "Asheville Budget 2014-2015";
        $site->owner_type = Site::GOVERNMENT;
        $site->owner = 1; // Asheville government;
        $site->government = 1; // Ditto
        $site->slug = 'asheville';
        $site->published = true;
        $site->save();

        $cardset = new CardSet();
        $cardset->site = $site->id;
        $cardset->name = 'Highlights';
        $cardset->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 1;
        $card->title = 'City Council Budget Goals';
        $card->body = "These are the goals for the budget";

        $picName = uniqid('pic') . '.jpg';
        $path = public_path().'/img/cards/'.$picName;
        \Image::make(public_path().'/img/init/slide1.jpg')->save($path);
        $card->image = '/img/cards/'.$picName;
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 2;
        $card->title = 'Revenue Highlights';
        $card->body = "These are the big changes to revenue";
        $picName = uniqid('pic') . '.jpg';
        $path = public_path().'/img/cards/'.$picName;
        \Image::make(public_path().'/img/init/slide2.jpg')->save($path);
        $card->image = '/img/cards/'.$picName;

        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 3;
        $card->title = 'Spending Highlights';
        $card->body = "These are the big changes to spending";
        $picName = uniqid('pic') . '.jpg';
        $path = public_path().'/img/cards/'.$picName;
        \Image::make(public_path().'/img/init/slide3.jpg')->save($path);
        $card->image = '/img/cards/'.$picName;
        $card->save();

        /*
         * Set up the home page
         */
        $page = new Page();
        $page->site = $site->id;
        $page->title = "Welcome to the 2015-16 Asheville Budget Explorer!";
        $page->short_name = 'Home';
        $page->ordinal = 1;
        $page->show_in_menu = true;
        $page->description = "The first page of the site.";
        $layout = Layout::where('name','=','DefaultHome')->first();
        $page->layout = $layout->id;
        $page->save();

        $c = new PageComponent();
        $c->component = $slideShowComponent->id;
        $c->page = $page->id;
        $c->target="Top";
        $data = array();
        $data['type'] = 'cardset';
        $data['items'] = array("$cardset->id");
        $dataBundle = array();
        $dataBundle['mycardset'] = $data;
        $c->setProperty('data', $dataBundle);
        $c->save();

        $c = new PageComponent();
        $c->component = $simpleCardComponent->id;
        $c->page = $page->id;
        $c->save();

        $c = new PageComponent();
        $c->component = $simpleCardComponent->id;
        $c->page = $page->id;
        $c->save();

        $c = new PageComponent();
        $c->component = $simpleCardComponent->id;
        $c->page = $page->id;
        $c->save();

        $page = new Page();
        $page->site = $site->id;
        $page->title = "Investigate What's Changed";
        $page->short_name = "What's New";
        $page->ordinal = 2;
        $page->show_in_menu = true;
        $page->description = "The second page of the site.";
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();

        $page = new Page();
        $page->site = $site->id;
        $page->title = "Detailed Breakdown of Spending & Revenue";
        $page->short_name = "Break It Down";
        $page->ordinal = 3;
        $page->show_in_menu = true;
        $page->description = "The second page of the site.";
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();

        $page = new Page();
        $page->site = $site->id;
        $page->title = "Some Helpful Resources";
        $page->short_name = 'Resources';
        $page->ordinal = 4;
        $page->show_in_menu = true;
        $page->description = "The third page of the site.";
        $page->layout = 1;
        $page->save();

        $page = new Page();
        $page->site = $site->id;
        $page->title = "About This Site";
        $page->short_name = "About";
        $page->ordinal = 5;
        $page->show_in_menu = true;
        $page->description = "The second page of the site.";
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();


    }

}
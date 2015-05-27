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
        $treemapComponent = Component::where('name','=','Treemap')->first();
        $cardTableComponent = Component::where('name','=','CardTable')->first();
        $historyAreaChartComponent = Component::where('name','=','HistoryAreaChart')->first();
        $whatsnewpageComponent = Component::where('name','=','WhatsNewPage')->first();
        $showmepageComponent = Component::where('name','=','ShowMePage')->first();
        $navCardsComponent = Component::where('name','=','NavCards')->first();

        // Create the Asheville site
        $site = new Site();
        $site->name = "Asheville Budget 2014-2015";
        $site->owner_type = Site::GOVERNMENT;
        $site->owner = 1; // Asheville government;
        $site->government = 1; // Ditto
        $site->slug = 'asheville';
        $site->published = true;
        $site->save();


        $this->createHomePage($site, $slideShowComponent, $navCardsComponent);

        $this->createWhatsNewPage($site, $whatsnewpageComponent);

        $this->createShowMePage($site, $showmepageComponent);

        $this->createDocMapPage($site, $cardTableComponent);

        $this->createAboutPage($site, $simpleCardComponent);
    }

    private function createHomePage($site, $slideShowComponent, $navCardsComponent)
    {
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
         * Now set up the navigation cards
         */
        $cardset2 = new CardSet();
        $cardset2->site = $site->id;
        $cardset2->name = 'NavCards';
        $cardset2->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset2->id;
        $card->ordinal = 1;
        $card->link = "whatsnew";
        $card->title = "What's New?";
        $card->body = "Discover what's changed since last year";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset2->id;
        $card->ordinal = 1;
        $card->link = "showme";
        $card->title = "Show Me The Money";
        $card->body = "Explore the sources and uses of public funds";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset2->id;
        $card->ordinal = 1;
        $card->link = "docmap";
        $card->title = "Budget Doc Breakdown";
        $card->body = "Navigate the City's budget document";
        $card->save();


        /*
         * Set up the home page
         */
        $page = new Page();
        $page->site = $site->id;
        $page->title = "Welcome to the 2015-16 Asheville Budget Explorer!";
        $page->short_name = 'Overview';
        $page->menu_name = 'Overview';
        $page->ordinal = 1;
        $page->show_in_menu = true;
        $page->description = null;
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
        $c->component = $navCardsComponent->id;
        $c->page = $page->id;
        $c->target="Bottom";
        $data = array();
        $data['type'] = 'cardset';
        $data['items'] = array("$cardset2->id");
        $dataBundle = array();
        $dataBundle['mycardset'] = $data;
        $c->setProperty('data', $dataBundle);
        $c->save();
    }

    private function createWhatsNewPage($site, $whatsnewpageComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "Investigate What's Changed";
        $page->short_name = "whatsnew";
        $page->menu_name = "What's New?";
        $page->ordinal = 2;
        $page->show_in_menu = true;
        $page->description = null;
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();

        $c = new PageComponent();
        $c->component = $whatsnewpageComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $c->save();

    }

    private function createShowMePage($site, $showmepageComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "Detailed Breakdown of Spending & Revenue";
        $page->short_name = "showme";
        $page->menu_name="Show Me The Money";
        $page->ordinal = 3;
        $page->show_in_menu = true;
        $page->description = null;
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();

        $c = new PageComponent();
        $c->component = $showmepageComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $c->save();
    }

    private function createDocMapPage($site, $cardTableComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "Budget Document Breakdown";
        $page->short_name = 'docmap';
        $page->menu_name = "Budget Doc Breakdown";
        $page->ordinal = 4;
        $page->show_in_menu = true;
        $page->description = "Explore the document by clicking in the sections below.";
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();


        // Create the cards for the resources table
        $cardset = new CardSet();
        $cardset->site = $site->id;
        $cardset->name = 'Budget Breakdown';
        $cardset->save();

        $c = new PageComponent();
        $c->component = $cardTableComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $data = array();
        $data['type'] = 'cardset';
        $data['items'] = array("$cardset->id");
        $dataBundle = array();
        $dataBundle['mycardset'] = $data;
        $c->setProperty('data', $dataBundle);
        $c->setProperty('props', ["maxColumns" => "2"]);
        $c->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 1;
        $card->title = 'Budget Process';
        $card->body = "Budget preparation affords departments the opportunity to reassess their goals and objectives and the strategies for accomplishing them. Even though the proposed budget is presented City Council in May and adopted in June, its preparation begins at least six months prior with projections of City revenues, expenditures, and overall financial capacity.
<!--br-->
Read more [about](http://www.google.com):
* Financial forecasting
* City Council Strategic Planning
* Departmental Budget Development
* City Manager Review
* Budget Adoption
* Budget Amendments and Revisions
* Basis of Budgeting";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 2;
        $card->title = 'Budget Calendar';
        $picName = uniqid('pic') . '.png';
        $path = public_path().'/img/cards/'.$picName;
        \Image::make(public_path().'/img/init/2014budgetcalendar.png')->save($path);
        $card->image = '/img/cards/'.$picName;

        $card->body = "
Check out the full process for building out this budget.

Full-size version [here](#).";

        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 3;
        $card->title = 'Budget Structure';
        $card->body = "A body";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 4;
        $card->title = 'Organizational Structure';
        $card->body = "A body";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 5;
        $card->title = 'Budget Process';
        $card->body = "A body";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 1;
        $card->title = "City Manager's Message";
        $card->body = "A body";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 6;
        $card->title = 'Financial Policies';
        $card->body = "A body";
        $card->save();

        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = 7;
        $card->title = 'Comprehensive Annual Financial Reports (CAFR)';
        $card->body = "A body";
        $card->save();
    }

    private function createAboutPage($site, $simpleCardComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "About This Site";
        $page->short_name = "About";
        $page->menu_name = "About";
        $page->ordinal = 5;
        $page->show_in_menu = false;
        $page->description = "The second page of the site.";
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();
    }
}
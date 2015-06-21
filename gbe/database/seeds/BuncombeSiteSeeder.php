<?php

use DemocracyApps\GB\Organizations\GovernmentOrganization;
use DemocracyApps\GB\Sites\Card;
use DemocracyApps\GB\Sites\CardSet;
use DemocracyApps\GB\Sites\Component;
use DemocracyApps\GB\Sites\Layout;
use DemocracyApps\GB\Sites\Page;
use DemocracyApps\GB\Sites\PageComponent;
use DemocracyApps\GB\Sites\Site;
use Illuminate\Database\Seeder;

class BuncombeSiteSeeder extends Seeder
{

    public function run()
    {
        $slideShowComponent = Component::where('name','=','SlideShow')->first();
        $simpleCardComponent = Component::where('name','=','SimpleCard')->first();
        $cardTableComponent = Component::where('name','=','CardTable')->first();
        $whatsnewpageComponent = Component::where('name','=','WhatsNewPage')->first();
        $showmepageComponent = Component::where('name','=','ShowMePage')->first();
        $navCardsComponent = Component::where('name','=','NavCards')->first();

        $government = GovernmentOrganization::where('name','=','Buncombe County, NC')->first();

        $site = new Site();
        $site->name = "Buncombe County Budget 2015-2016";
        $site->owner_type = Site::GOVERNMENT;
        $site->owner = $government->id;
        $site->government = $government->id;
        $site->slug = 'bc';
        $site->published = true;
        $site->live = false;
        $site->setProperty('map', 'serviceareas.json');
        $site->setProperty('reverseRevenueSign', false);
        $site->save();

        $this->createShowMePage($site, 1, $showmepageComponent, $simpleCardComponent);

        $this->createWhatsNewPage($site, 2, $whatsnewpageComponent);

        $this->createSchoolsPage($site, 3, $showmepageComponent, $simpleCardComponent);

        $this->createDocumentsPage($site, 4, $cardTableComponent);

        $this->createAboutPage($site, 5, $simpleCardComponent);
    }

    private function createCard ($site, $cardset, $ordinal, $title, $fields) {
        $card = new Card();
        $card->site = $site->id;
        $card->card_set = $cardset->id;
        $card->ordinal = $ordinal;

        $card->title = $title;
        if (array_key_exists('body', $fields)) $card->body = $fields['body'];
        if (array_key_exists('link', $fields)) $card->link = $fields['link'];
        if (array_key_exists('image', $fields) && $fields['image'] != null) {
            $picName = uniqid('pic') . '.jpg';
            $path = public_path().'/img/cards/'.$picName;
            \Image::make($fields['image'])->save($path);
            $card->image = '/img/cards/'.$picName;
        }
        $card->save();
        return $card;
    }

    private function createShowMePage($site, $ord, $showmepageComponent, $simpleCardComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "Map of Buncombe County Spending & Revenue";
        $page->short_name = "moneymap";
        $page->menu_name="Map of the Money";
        $page->ordinal = $ord;
        $page->show_in_menu = true;
        $page->description = null;
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();

        $cardset = new CardSet();
        $cardset->site = $site->id;
        $cardset->name = 'ShowMe Page Cards';
        $cardset->save();
        $ordinal = 1;
        $card1 = $this->createCard($site, $cardset, $ordinal++, 'What is this?',
            [
                'body' => 'This site was created by <a href="http://democracyapps.us" target="_blank">DemocracyApps</a>
as a public service. We have used only materials normally provided by Buncombe County during the public budget process.
The purpose of this site is to help support the public conversation in Buncombe County
about what our priorities are as a community and how we fund efforts to achieve those priorities.'
            ]
        );

        $card2 = $this->createCard($site, $cardset, $ordinal++, 'Notes',
            [
                'body' => 'Here are some notes'            ]
        );

        $c = new PageComponent();
        $c->component = $simpleCardComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $data = array();
        $data['type'] = 'card';
        $data['items'] = array("$card1->id");
        $dataBundle = array();
        $dataBundle['mycard'] = $data;
        $c->setProperty('data', $dataBundle);
        $c->setProperty('props', ["headerTag" => "0"]);
        $c->save();

        $c = new PageComponent();
        $c->component = $showmepageComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $c->save();

        $c = new PageComponent();
        $c->component = $simpleCardComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $data = array();
        $data['type'] = 'card';
        $data['items'] = array("$card2->id");
        $dataBundle = array();
        $dataBundle['mycard'] = $data;
        $c->setProperty('data', $dataBundle);
        $c->setProperty('props', ["headerTag" => "0"]);
        $c->save();
    }

    private function createWhatsNewPage($site, $ord, $whatsnewpageComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "Investigate What's Changed";
        $page->short_name = "whatsnew";
        $page->menu_name = "What's Changed?";
        $page->ordinal = $ord;
        $page->show_in_menu = true;
        $page->description = null;
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();

        $c = new PageComponent();
        $c->component = $whatsnewpageComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $c->setProperty('props', ["detailSelectorOn" => "No"]);
        $c->save();

    }

    private function createSchoolsPage($site, $ord, $showmepageComponent, $simpleCardComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "Public Schools Spending & Revenue";
        $page->short_name = "schools";
        $page->menu_name="Schools";
        $page->ordinal = $ord;
        $page->show_in_menu = true;
        $page->description = null;
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();

        $cardset = new CardSet();
        $cardset->site = $site->id;
        $cardset->name = 'Schools Page Cards';
        $cardset->save();
        $ordinal = 1;
        $card1 = $this->createCard($site, $cardset, $ordinal++, 'What is this?',
            [
                'body' => 'Say something'
            ]
        );
        $card2 = $this->createCard($site, $cardset, $ordinal++, 'Notes?',
            [
                'body' => 'Say something'
            ]
        );

        $c = new PageComponent();
        $c->component = $simpleCardComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $data = array();
        $data['type'] = 'card';
        $data['items'] = array("$card1->id");
        $dataBundle = array();
        $dataBundle['mycard'] = $data;
        $c->setProperty('data', $dataBundle);
        $c->setProperty('props', ["headerTag" => "0"]);
        $c->save();

        $c = new PageComponent();
        $c->component = $showmepageComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $c->save();

        $c = new PageComponent();
        $c->component = $simpleCardComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $data = array();
        $data['type'] = 'card';
        $data['items'] = array("$card2->id");
        $dataBundle = array();
        $dataBundle['mycard'] = $data;
        $c->setProperty('data', $dataBundle);
        $c->setProperty('props', ["headerTag" => "0"]);
        $c->save();
    }

    private function createDocumentsPage($site, $ord, $cardTableComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "Budget Source Documents";
        $page->short_name = 'docs';
        $page->menu_name = "Budget Documents";
        $page->ordinal = $ord;
        $page->show_in_menu = true;
        $page->description = "This page lists links to the budget documents provided by the County during
the budget process.";
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

        $ordinal = 1;

        $this->createCard($site, $cardset, $ordinal++, 'Full Proposed 2015-2016 Budget',
            [
                'link' => "/docs/asheville/FY 2015- 16 Proposed Budget.pdf",
                'image'=> null,
                'body' => "
Use the button below to download the full 2015-16 City of Asheville Proposed Budget document."
            ]
        );

    }

    private function createAboutPage($site, $ord, $simpleCardComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "About This Site";
        $page->short_name = "About";
        $page->menu_name = "About";
        $page->ordinal = $ord;
        $page->show_in_menu = true;
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();

        $cardset = new CardSet();
        $cardset->site = $site->id;
        $cardset->name = 'About Page Cards';
        $cardset->save();
        $ordinal = 1;

        $card1 = $this->createCard($site, $cardset, $ordinal++, 'About This Site',
            [
                'body' => "_Don't tell me what you value, show me your budget, and I'll tell you what you value. - Joe Biden_

The purpose of this site is to help support the public conversation
in Asheville about what our priorities are as a community and how we fund efforts to
achieve those priorities.

This site is built on a free, open-source platform developed by [DemocracyApps](http://democracyapps.us), a local civic tech
startup.

The site is entirely a volunteer effort. We would like to acknowledge the active support and help of
volunteers from [Code for Asheville](http://www.codeforasheville.org/) and
the [Asheville Coders League](http://avlcoders.org/). A tip of the hat
to [Involution Studios](http://www.goinvo.com/) for the amazing _Show Me The Money_ visualization. We
would also like to thank the staff of the City
of Asheville for their ongoing cooperation and support."
            ]
        );
        $card2 = $this->createCard($site, $cardset, $ordinal++, 'Contact us',
            [
                'body' => "<iframe src=\"https://docs.google.com/forms/d/1gtQxsqx_HYwHh65046wsAavrlJcgMLYlQJ-tLtfsBF4/viewform?embedded=true\" width=\"760\" height=\"700\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\">Loading...</iframe>"
            ]
        );

        $c = new PageComponent();
        $c->component = $simpleCardComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $data = array();
        $data['type'] = 'card';
        $data['items'] = array("$card1->id");
        $dataBundle = array();
        $dataBundle['mycard'] = $data;
        $c->setProperty('data', $dataBundle);
        $c->setProperty('props', ["headerTag" => "0"]);
        $c->save();
        $c = new PageComponent();
        $c->component = $simpleCardComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $data = array();
        $data['type'] = 'card';
        $data['items'] = array("$card2->id");
        $dataBundle = array();
        $dataBundle['mycard'] = $data;
        $c->setProperty('data', $dataBundle);
        $c->setProperty('props', ["headerTag" => "2"]);
        $c->save();

    }
}
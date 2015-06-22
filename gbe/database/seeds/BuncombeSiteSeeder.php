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

        $this->createWhatsNewPage($site, 2, $whatsnewpageComponent, $simpleCardComponent);

        $this->createSchoolsPage($site, 3, $showmepageComponent, $simpleCardComponent);
        
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
The purpose of this site is to help support public conversation in Buncombe County about the community\'s priorities
and how they are funded.'
            ]
        );

        $card2 = $this->createCard($site, $cardset, $ordinal++, 'Notes',
            [
                'body' => 'The data for this visualization was taken from the proposed budget spreadsheets included
in County Commission meeting materials
for <a href="https://www.buncombecounty.org/Governing/Commissioners/Agenda.aspx?agendaID=97">June 2, 2015</a>
and <a href="https://www.buncombecounty.org/Governing/Commissioners/Agenda.aspx?agendaID=77">June 3, 2014</a>.
These are budget rather than actual amounts even for FY 2014. Actuals are included in a final budget report that is
issued after the budget is adopted, but since the categories used there are different from those used in the
spreadsheet, it was not practical to map 2014 actuals to the 2016 budget. You may download the data used for
this page [here](/docs/buncombe/Budget_Spreadsheets_2016-2014.xlsx).
The final budget reports for earlier years may be
found <a href="https://www.buncombecounty.org/Governing/Depts/Administration/budget-management/operating-budget.aspx">here</a>'
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
        $c->setProperty('props', ["headerTag" => "2"]);
        $c->save();
    }

    private function createWhatsNewPage($site, $ord, $whatsnewpageComponent, $simpleCardComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "What's Changing Between 2015 and 2016?";
        $page->short_name = "whatsnew";
        $page->menu_name = "What's Changing?";
        $page->ordinal = $ord;
        $page->show_in_menu = true;
        $page->description = null;
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();


        $cardset = new CardSet();
        $cardset->site = $site->id;
        $cardset->name = 'What\'s Changed Page Cards';
        $cardset->save();
        $ordinal = 1;

        $card1 = $this->createCard($site, $cardset, $ordinal++, 'What is this?',
            [
                'body' => 'The chart and table below show the biggest changes in spending or revenue
between the FY 2015 and FY 2016 budgets. The chart shows only the top 10 in each category. The table shows all differences,
ordered by decreasing amount.'
            ]
        );

        $card2 = $this->createCard($site, $cardset, $ordinal++, 'Notes',
            [
                'body' => 'The data for this visualization was taken from the proposed budget spreadsheets included
in County Commission meeting materials
for <a href="https://www.buncombecounty.org/Governing/Commissioners/Agenda.aspx?agendaID=97">June 2, 2015</a>
and <a href="https://www.buncombecounty.org/Governing/Commissioners/Agenda.aspx?agendaID=77">June 3, 2014</a>.
These are budget rather than actual amounts even for FY 2014. Actuals are included in a final budget report that is
issued after the budget is adopted, but since the categories used there are different from those used in the
spreadsheet, it was not practical to map 2014 actuals to the 2016 budget. You may download the data used for
this page [here](/docs/buncombe/Budget_Spreadsheets_2016-2014.xlsx).
The final budget reports for earlier years may be
found <a href="https://www.buncombecounty.org/Governing/Depts/Administration/budget-management/operating-budget.aspx">here</a>'
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
        $c->component = $whatsnewpageComponent->id;
        $c->page = $page->id;
        $c->target="main";
        $c->setProperty('props', ["detailSelectorOn" => "No"]);
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
                'body' => 'Funding for public schools represents a large portion of the Buncombe County budget. Since
the County budget itself includes little detail on this portion, this page presents the actual budget request information
from Buncombe County Schools and Asheville City Schools. Note that the County only provides a portion of the total schools
budget - State, Federal and other funds make up the difference. All of these are included in the data shown below.'
            ]
        );
        $card2 = $this->createCard($site, $cardset, $ordinal++, 'Note',
            [
                'body' => 'The data used here are the budget <i>requests</i> from the school systems and so the total
amounts requested from the County differ from the actual amounts assigned in the County budget.
The original documents may be found in the County Commission meeting materials
for <a href="https://www.buncombecounty.org/Governing/Commissioners/Agenda.aspx?agendaID=97">June 2, 2015</a>. You
may download the data used for this page [here](/docs/buncombe/Schools_Budget_Requests_2016.xlsx).'
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
        $c->setProperty('props', ["headerTag" => "2"]);
        $c->save();
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
                'body' => '_Don\'t tell me what you value, show me your budget, and I\'ll tell you what you value. - Joe Biden_

The purpose of this site is to help support the public conversation
in Buncombe County about community priorities and how they are funded.

This site is built on [communitybudgets.org](http://communitybudgets.org), a free, open-source platform developed
by [DemocracyApps](http://democracyapps.us), a local civic tech startup.

If you are interested in getting a site set up for your community, have a question, or would like to
help, please contact
us <a href="https://docs.google.com/forms/d/10c7muM4_DTY4rhUnV3D9M7l7o5m4Z7f0P237u9R_Hj4/viewform?usp=send_form">here</a>.'
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
    }
}
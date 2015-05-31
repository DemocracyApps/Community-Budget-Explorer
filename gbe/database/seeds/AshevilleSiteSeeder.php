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
        $cardTableComponent = Component::where('name','=','CardTable')->first();
        $whatsnewpageComponent = Component::where('name','=','WhatsNewPage')->first();
        $showmepageComponent = Component::where('name','=','ShowMePage')->first();
        $navCardsComponent = Component::where('name','=','NavCards')->first();

        // Create the Asheville site
        $site = new Site();
        $site->name = "Asheville Budget 2015-2016";
        $site->owner_type = Site::GOVERNMENT;
        $site->owner = 1; // Asheville government;
        $site->government = 1; // Ditto
        $site->slug = 'asheville';
        $site->published = true;
        $site->setProperty('map', 'serviceareas.json');
        $site->save();


        $this->createHomePage($site, $slideShowComponent, $navCardsComponent);

        $this->createWhatsNewPage($site, $whatsnewpageComponent);

        $this->createShowMePage($site, $showmepageComponent);

        $this->createDocMapPage($site, $cardTableComponent);

        $this->createAboutPage($site, $simpleCardComponent);
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

    private function createHomePage($site, $slideShowComponent, $navCardsComponent)
    {
        $cardset = new CardSet();
        $cardset->site = $site->id;
        $cardset->name = 'Highlights';
        $cardset->save();
        $ordinal = 1;

        $this->createCard($site, $cardset, $ordinal++, 'Revenue Highlights',
            [
                'link' => "/docs/asheville/Revenue_Summary.pdf",
                'image'=> public_path().'/img/init/slide2.jpg',
                'body' => "
* 1.5 cent property tax increase to 47.5 cents per $100 assessed value
* 4% increase in sales tax revenue
* 12% decrease in licensing and permitting revenue
* 5.4% inter-governmental revenue increase in General Fund
* No appropriation from unassigned fund balance"
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, 'Spending Highlights',
            [
                'link' => "/docs/asheville/Expenditure_Summary.pdf",
                'image'=> public_path().'/img/init/slide3.jpg',
                'body' => "
* 3.6% overall increase in spending
* $1M increase in Public Safety
* $354,000 increase in Environment & Transportation
* $250,000 increase for seasonal/temporary staff living wage"
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, 'City Council Budget Goals',
            [
                'link' => "/docs/asheville/Council_Budget_Goals.pdf",
                'image'=> public_path().'/img/init/slide1.jpg',
                'body' => "
* Classification and Compensation Study, Managed Savings
* Asheville Police Department Management Goals & Strategic Plan
* Reducing Taxpayer Subsidy of Programs
* Continuing Sound Financial Management by Addressing Long-Term Liabilities"
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, 'Staffing Highlights',
            [
                'link' => "/docs/asheville/Staffing_Summary.pdf",
                'image'=> public_path().'/img/init/slide4.jpg',
                'body' => "
* 14.25 FTE positions added in General Fund
* 10.37 FTE positions added in enterprise funds
* Living wage extended to seasonal/temporary staff
* 1% across-the-board pay raise"
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, 'Budget Highlights By Fund',
            [
                'link' => "/docs/asheville/Fund_Highlights_Summary.pdf",
                'image'=> public_path().'/img/init/slide5.jpg',
                'body' => "
* Water Resources: Rate changes expected to generate $465,000 new revenue
* Stormwater: 5% rate adjustment expected to generate $240,000 new revenue
* Transit: Fully funded Sunday service plus minor route adjustments
* Parking Services: No rate change; current revenue up & trend expected to continue
* Street Cut Utility: $240,000 spending increase"
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, 'Capital Improvements',
            [
                'link' => "/docs/asheville/CapitalImprovementProgramAndDebt.pdf",
                'image'=> public_path().'/img/init/slide7.jpg',
                'body' => "
* Capital Expenditures program begun in FY2014 is now in full swing
* $26.2M proposed spending for FY2016
* $16.9M was spent during FY2014 & FY2015
* $64.3M planned over the next 4 budget cycles (FY2017-FY2020)"
            ]
        );

        /*
         * Now set up the navigation cards
         */
        $cardset2 = new CardSet();
        $cardset2->site = $site->id;
        $cardset2->name = 'NavCards';
        $cardset2->save();

        $ordinal = 1;
        $this->createCard($site, $cardset2, $ordinal++, "What's New?",
            [
                'link' => "whatsnew",
                'body' => "Discover what's changed since last year"
            ]
        );

        $this->createCard($site, $cardset2, $ordinal++, 'Show Me The Money',
            [
                'link' => "showme",
                'body' => "Explore the sources and uses of public funds"
            ]
        );
        $this->createCard($site, $cardset2, $ordinal++, 'Budget Document Map',
            [
                'link' => "docmap",
                'body' => "Navigate the City's budget document"
            ]
        );

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
        $page->description = "Explore the full budget document and city website financial materials below.";
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
Use the button below to download the full 2015-16 City of Asheville Proposed Budget document.

For a searchable version of the PDF, [click here](/docs/asheville/FY2015-16AshevilleBudget-searchable.pdf)"
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Table of Contents",
            [
                'link' => "/docs/asheville/1-TableOfContents.pdf",
                'image'=> public_path()."/img/init/TOC.png",
                'body' => "Click on the thumbnail or below to explore the Table of Contents for the entire 2015-2016 Proposed Budget."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "City Manager's Message",
            [
                'link' => "/docs/asheville/2-ManagersLetter_FY2016.pdf",
                'image'=> null,
                'body' => "
May 26, 2015

Honorable Mayor Manheimer and City Council Members:

It is my pleasure to respectfully submit to you the City Manager's Proposed Budget for the fiscal year
beginning July 1, 2015 and ending June 30, 2016 with a total operating budget of $154 million. The proposed
budget includes a total General Fund Budgetof $103 million, a 3.6% increase over the adopted fiscal year
2014-2015 budget. The proposed budget includes an increase to the property tax rate of 1.5 cents per $1000
of valuation, bringing the total millage rate to 47.5 cents."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Organizational Structure",
            [
                'link' => "/docs/asheville/3-Introduction.pdf",
                'image'=> public_path()."/img/init/Org_Chart.PNG",
                'body' => "
Understand the organizational structure underlying the City's operating budget. Click [here](http://www.ashevillenc.gov/Portals/0/city-documents/communityrelations/about_city_government/COA%20Organizational%20Chart%20041615.pdf) for a full-size version
of the organization chart at left."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Budget Process",
            [
                'link' => "/docs/asheville/Budget_Process.pdf",
                'image'=> null,
                'body' => "
Budget preparation affords departments the opportunity to reassess their goals and objectives
and the strategies for accomplishing them. Even though the proposed budget may be heard by
City Council in May and adopted in June, its preparation begins at least six months prior
with projections of City reserves, revenues, and financial capacity.
<!--br-->
Read more about:
* Financial forecasting
* City Council Strategic Planning
* Departmental Budget Development
* City Manager Review
* Budget Adoption
* Budget Amendments and Revisions
* Basis of Budgeting"
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "FY 2015-16 Budget Calendar",
            [
                'link' => "/docs/asheville/Budget_Calendar.pdf",
                'image'=> public_path()."/img/init/BudgetCalendar.png",
                'body' => "Examine the detailed calendar of steps over the past year that led to the proposed budget."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Financial Policies",
            [
                'link' => "/docs/asheville/Financial_Policy.pdf",
                'image'=> null,
                'body' => "
The City of Asheville financial policies establish general guidelines for the fiscal management of the City.
These guidelines, influenced by the North Carolina Local Government Budget and Fiscal Control Act and
sound financial principles, provide the framework for budgetary and fiscal planning. Operating independently
of changing circumstances and conditions, these policies assist the decision-making processes of the City Council
and City administration."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Budget Summary",
            [
                'link' => "/docs/asheville/4-BudgetSummary.pdf",
                'image'=> public_path()."/img/init/budget_summary.png",
                'body' => "Click on the thumbnail or below to explore the budget summary tables
and highlights of revenue, expenditure, and staffing for all funds."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "General Government",
            [
                'link' => "/docs/asheville/5-GeneralGovernment.pdf",
                'image'=> public_path()."/img/init/general_government.png",
                'body' => "Click on the thumbnail or below to explore budget details for Finance and Management
            Services, Administrative Services, General Services, Economic Development, City Attorney's Office,
            Information Technology Services, and Human Resources."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Public Safety",
            [
                'link' => "/docs/asheville/6-PublicSafety.pdf",
                'image'=> public_path()."/img/init/public_safety.png",
                'body' => "Click on the thumbnail or below to explore budget details for Police and Fire & Rescue."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Environment & Transportation",
            [
                'link' => "/docs/asheville/7-EnvironmentAndTransportation.pdf",
                'image'=> public_path()."/img/init/environment_transportation.png",
                'body' => "Click on the thumbnail or below to explore budget details the Water Resources Fund,
Multi-Modal Transportation and Capital Projects, Public Works, the Stormwater Fund, the Street Cut Utility Fund,
Transportation, the Transit Services Fund, and the Parking Services Fund."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Culture & Recreation",
            [
                'link' => "/docs/asheville/8-CultureRecreation.pdf",
                'image'=> public_path()."/img/init/culture_recreation.png",
                'body' => "Click on the thumbnail or below to explore budget details for Parks, Recreation & Cultural Affairs, and for the US Cellular Center."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Community Development",
            [
                'link' => "/docs/asheville/9-CommunityDevelopment.pdf",
                'image'=> public_path()."/img/init/community_development.png",
                'body' => "Click on the thumbnail or below to explore budget details Planning and Urban
Design, the Housing Trust Fund, Building Safety and Development Services."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Capital Improvements/Debt Management",
            [
                'link' => "/docs/asheville/10-CapitalImprovementProgramAndDebt.pdf",
                'image'=> public_path()."/img/init/capital_debt.png",
                'body' => "Click on the thumbnail or below to explore budget details for the General
Capital Fund, the Water Resources Capital Fund, and the Parking Services Capital Fund, including a project-by-project listing."
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Comprehensive Annual Financial Reports (CAFR)",
            [
                'link' => "http://www.ashevillenc.gov/Departments/Finance/ComprehensiveAnnualFinancialReports.aspx",
                'image'=> public_path()."/img/init/cafr.png",
                'body' => "
State law requires that all municipal governments publish a complete set of financial statements
presented in conformity with generally accepted accounting principles (GAAP).
* [Fiscal Year Ended June 30, 2014](http://www.ashevillenc.gov/Portals/0/city-documents/finance/2014CAFR.pdf)
* [Fiscal Year Ended June 30, 2013](http://www.ashevillenc.gov/Portals/0/city-documents/finance/CAFR%2012172013.pdf)
* [Fiscal Year Ended June 30, 2012](http://www.ashevillenc.gov/Portals/0/images/departments/finance/CAFR%202013%20with%20changes.pdf)
* [Fiscal Year Ended June 30, 2011](http://www.ashevillenc.gov/Portals/0/city-documents/finance/cafr/CAFR%202011.pdf)
* [Fiscal Year Ended June 30, 2010](http://www.ashevillenc.gov/Portals/0/city-documents/finance/cafr/CAFR2010.pdf)
* [Fiscal Year Ended June 30, 2009](http://www.ashevillenc.gov/Portals/0/city-documents/finance/cafr/2009%20COA%20CAFR.pdf)
* [Fiscal Year Ended June 30, 2008](http://www.ashevillenc.gov/Portals/0/city-documents/finance/cafr/CAFR%202008.pdf)
* [Fiscal Year Ended June 30, 2007](http://www.ashevillenc.gov/Portals/0/city-documents/finance/CAFR%202007.pdf)
"
            ]
        );

        $this->createCard($site, $cardset, $ordinal++, "Finance Committe Meeting Materials",
            [
                'link' => "http://www.ashevillenc.gov/Departments/CityClerk/CouncilCommittees/FinanceCommittee/MeetingMaterials.aspx",
                'image'=> null,
                'body' => "Meeting agendas, meeting minutes and supporting documents can be found on the
City of Asheville website."
            ]
        );

    }

    private function junkcreateAboutPage($site, $simpleCardComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "About This Site";
        $page->short_name = "About";
        $page->menu_name = "About";
        $page->ordinal = 5;
        $page->show_in_menu = true;
        $layout = Layout::where('name','=','One-Column')->first();
        $page->layout = $layout->id;
        $page->save();

        $cardset = new CardSet();
        $cardset->site = $site->id;
        $cardset->name = 'About Page Cards';
        $cardset->save();
        $ordinal = 1;

        $card1 = $this->createCard($site, $cardset, $ordinal++, "Capital Improvements/Debt Management",
            [
                'link' => "/docs/asheville/10-CapitalImprovementProgramAndDebt.pdf",
                'image'=> public_path()."/img/init/capital_debt.png",
                'body' => "Click on the thumbnail or below to explore budget details for the General
Capital Fund, the Water Resources Capital Fund, and the Parking Services Capital Fund, including a project-by-project listing."
            ]
        );

        $card2 = $this->createCard($site, $cardset, $ordinal++, "Capital Improvements/Debt Management",
            [
                'link' => "/docs/asheville/10-CapitalImprovementProgramAndDebt.pdf",
                'image'=> public_path()."/img/init/capital_debt.png",
                'body' => "Click on the thumbnail or below to explore budget details for the General
Capital Fund, the Water Resources Capital Fund, and the Parking Services Capital Fund, including a project-by-project listing."
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
        $c->setProperty('props', ["headerTag" => "2"]);
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

    private function createAboutPage($site, $simpleCardComponent)
    {
        $page = new Page();
        $page->site = $site->id;
        $page->title = "About This Site";
        $page->short_name = "About";
        $page->menu_name = "About";
        $page->ordinal = 5;
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
the [Asheville Coders League](http://avlcoders.org/). We would also like to thank the staff of the City
of Asheville for their ongoing cooperation and support."
            ]
        );
        $card2 = $this->createCard($site, $cardset, $ordinal++, 'Contact us',
            [
                'body' => "<iframe src=\"https://docs.google.com/forms/d/1gtQxsqx_HYwHh65046wsAavrlJcgMLYlQJ-tLtfsBF4/viewform?embedded=true\" width=\"760\" height=\"500\" frameborder=\"0\" marginheight=\"0\" marginwidth=\"0\">Loading...</iframe>"
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
import React from 'react';
import Site from './components/Site';
var dispatcher = require('./common/BudgetAppDispatcher');
var ActionTypes = require('./constants/ActionTypes');

/*
 * Since we don't know in advance which components will be used by the layout,
 * we need to import all of them and then create a map by name that it can use to load
 * them wherever needed. I would like to find a better way to do this, though ...
 */
import SimpleCard from './components/SimpleCard';
import MultiYearTable from './components/MultiYearTable';
import SlideShow from './components/SlideShow';

var reactComponents = {};
reactComponents['SimpleCard'] = SimpleCard;
reactComponents['SlideShow'] = SlideShow;
reactComponents['MultiYearTable'] = MultiYearTable;

/*
 * The stores, one for card-related data, the other for financial datasets.
 */
var configStore = require('./stores/ConfigStore');
var stateStore = require('./stores/StateStore');
var cardStore = require('./stores/CardStore');
var datasetStore = require('./stores/DatasetStore');

/*
 * Let's set up a couple standard sections in the configuration store
 */
configStore.createSection('common');
configStore.createSection('pages');
configStore.createSection('components');

/*****************************************************
 * Process the incoming parameters ...
 *****************************************************/

var i;

//  Site
//   The site object contains a few global bits of information such as the site name (and short-name, or slug),
//   the start page, and several URLs (base URL, API URL, base ajax URL). It also contains a hash named 'properties'
//   that may be used later to pass in extended information.

// Note that site is an array just to work around a bug in Jeff Way's PHPToJavascriptTransformer library.
configStore.storeConfiguration('common', 'site', GBEVars.site[0]);
//console.log("Site: " + JSON.stringify(GBEVars.site[0]));

//  Pages
//   This is an array of configurations of the pages on the site. Each page has title, description, layout
//   and a list of components that are to be placed in the layout. These components are instantiated as
//   React components (in the BootstrapLayout component) and contain indices for accessing any associated data.
var pages = [];
for (i=0; i<GBEVars.pages.length; ++i) {
    var page = GBEVars.pages[i];
    //console.log("Page: " + JSON.stringify(page));
    page.storeId = stateStore.registerComponent('page', page.shortName, {});
    for (var key in page.components) {
        if (page.components.hasOwnProperty(key)) {
            page.components[key].forEach(function (c) {
                c.storeId = stateStore.registerComponent('components', c.id, {}); // Should use a common ID generator, but no time right now
                configStore.registerComponent(c.storeId, 'components', c.id, {});
                //console.log("Registered component " + c.componentName + " with storeId " + c.storeId);
            });
        }
    }
    configStore.storeConfiguration('pages', page.id, page);
    pages.push(page.id);
}

//  Data
//   This is an array of data objects. In the case of cards, the data object contains the
//   actual data. In the case of datasets, the object contains the dataset ID needed to make an API
//   request. These need to be thrown into the card and dataset stores.

while (GBEVars.data.length > 0) {
    var datum = GBEVars.data.shift();
    //console.log("DataItem: " + JSON.stringify(datum));
    if (datum.dataType == "card") {
        cardStore.storeCard(datum);
    }
    else if (datum.dataType == "dataset") {
        var ds = datasetStore.registerDataset(datum.id);
    }
}

var props = {
    site: GBEVars.site[0],
    pages: pages,
    configurationId: GBEVars.site[0].id,
    reactComponents: reactComponents
};

var layout = React.render(<Site {...props}/>, document.getElementById('app'));


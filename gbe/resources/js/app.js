import React from 'react';

var cardStore = require('./stores/MainCardStore');
var datasetStore = require('./stores/MainDataSetStore');

// This is the layout manager for the page
import BootstrapLayout from './components/BootstrapLayout';

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
 * Set up the stores.
 *
 * The card store currently just has static data - we'll simply initialize it here.
 * The only reason we're doing this as a store rather than props is that we may wish to later build
 * dynamic content that lets cards embed dataset data or something.
 *
 * The dataset store just kicks off API requests with callbacks that will emit change events when the
 * data is actually received.
 */

console.log("Process the components for data");
/*
 * Each key in GBEVars.components refers to a target cell in the layout and is associated with an array
 * of components that will be placed in that cell. Here we just want to extract the data associated with
 * each component - the component will retrieve it later via a key. We probably should move this logic
 * up to the server, though.
 */
$.each(GBEVars.components, function (key, pageComponentsArray) {
    for (var i=0; i<pageComponentsArray.length; ++i) {
        var pageComponent = pageComponentsArray[i];
        console.log("Dealing with a component " + pageComponent.componentName);
        if (pageComponent.data != null) {
            for (var key in pageComponent.data) {
                if (pageComponent.data.hasOwnProperty(key)) {
                    if (pageComponent.data[key].dataType == 'card') {
                        pageComponent.data[key] = {
                            type: 'card',
                            storeId: cardStore.storeItem(pageComponent.data[key])
                        };
                    }
                    else if (pageComponent.data[key].dataType == 'cardset') {
                        pageComponent.data[key] = {
                            type: 'cardset',
                            storeId: cardStore.storeItem(pageComponent.data[key])
                        }
                    }
                    else if (pageComponent.data[key].dataType == 'dataset') {
                        pageComponent.data[key] = {
                            type: 'dataset',
                            id: datasetStore.registerDataset(pageComponent.data[key].id)
                        }
                    }
                    else if (pageComponent.data[key].dataType == 'dataset_list') {
                        var idList = [];
                        for (var i = 0; i < pageComponent.data[key].idList.length; ++i) {
                            idList.push(datasetStore.registerDataset(pageComponent.data[key].idList[i]));
                        }
                        pageComponent.data[key] = {
                            type: 'dataset_list',
                            idList: idList
                        }
                    }
                }
            }
            //$.each(pageComponent.componentProps.data, function (key, data) {
            //    console.log("Processing the data associated with " + key);
            //    if (data.type == 'card' || data.type == 'cardset') {
            //        data.storeId = cardStore.importCards(key, data);
            //    }
            //});
        }
    }
});

/*
 * The layout specific gives the grid. The components property has the component specifications by grid cell ID,
 * and reactComponents has the actual React components keyed by component name.
 */
var props = {layout:GBEVars.layout.specification, components:GBEVars.components, reactComponents:reactComponents}
var layout = React.render(<BootstrapLayout {...props}/>, document.getElementById('app'));


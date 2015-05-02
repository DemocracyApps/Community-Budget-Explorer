//$.each(GBEVars.components, function (key, pageComponentsArray) {
//    for (var i=0; i<pageComponentsArray.length; ++i) {
//        var pageComponent = pageComponentsArray[i];
//        if (pageComponent.data != null) {
//            for (var key in pageComponent.data) {
//                if (pageComponent.data.hasOwnProperty(key)) {
//                    if (pageComponent.data[key].dataType == 'card') {
//                        pageComponent.data[key] = {
//                            type: 'card',
//                            id: cardStore.storeCard(pageComponent.data[key])
//                        };
//                    }
//                    else if (pageComponent.data[key].dataType == 'cardset') {
//                        pageComponent.data[key] = {
//                            type: 'cardset',
//                            id: cardStore.storeCardSet(pageComponent.data[key])
//                        }
//                    }
//                    else if (pageComponent.data[key].dataType == 'dataset') {
//                        pageComponent.data[key] = {
//                            type: 'dataset',
//                            id: datasetStore.registerDataset(pageComponent.data[key].id, key)
//                        }
//                    }
//                    else if (pageComponent.data[key].dataType == 'multidataset') {
//                        var idList = [];
//                        for (var i = 0; i < pageComponent.data[key].idList.length; ++i) {
//                            idList.push(datasetStore.registerDataset(pageComponent.data[key].idList[i], key));
//                        }
//                        // Need to create a composite set and get the id to that
//                        var compositeId =  datasetStore.registerDatasetCollection(idList, key);
//                        pageComponent.data[key] = {
//                            type: 'multidataset',
//                            id: compositeId, // ID of the composite set
//                            idList: idList
//                        }
//
//                    }
//                }
//            }
//        }
//    }
//});

* [ ] Finish the dataserver
  * [ ] Create a method in Datasource to accept a new input after execute or fetch with replaces the existing dataset rather than creating a new one. Make sure Dataset->save() works on update. Have DataSourcesController->execute and \Console\Commmands\Fetch call the Datasource method to save rather than doing it themselves (as now in the controller)
  * [ ] Create a CSV pre-processor that converts data from simple array to a map, like you'd get with JSON.
  * [ ] Move the upload command from its own controller to the datasource controller. 
  * [ ] Create a SimpleBudgetProcessor that works like SimpleProjectProcessor and call that from upload instead.
  * [ ] Make sure that file upload can take either JSON or CSV.
  * [ ] Kill the datasource 'register' route and replace with a simple post to /api/v1/datasources, not a special URL.
  * [ ] Make sure that we do the right thing on error from API or file upload. Especially make sure we leave the datasets untouched if we fail to get updated data.
  * [ ] Record/display date of last fetch, status, etc.
* [ ] Create a rough initial component for displaying project data.
  * [ ] Update the project field mapping from the final data feed version
  * [ ] Create the project listing page
  * [ ] Create the project detail page
* [ ] Finish the new front end
  * [ ] See below


NEXT:
* Think about how to let individual components/pages set url params (and at least remember chart vs table)
* Store revenue and expense as separate artifacts
* create the budget doc breakdown page
* Figure out how to connect to actual dataserver.
* Figure out how to put it on a server

LATER:

* I've made the createArtifact routine take an array of transforms, however, there are a couple issues:
    1. We need to actually test that it works on a multi-step thing (e.g., differences)
    2. We need to make sure that we're settled on what each transform takes (i.e., that a differencer can operate both
    directly on the same thing the HistoryTable sends and on the OUTPUT of that).
* Mustache templates for the treemap are included right now in the main index.html file. Before they were included
  via a php require, but still in the main template. Need a better way!
* Need a better way to include the CSS for the treemap

OTHER NOTES:

- Not sure that it makes sense to pass componentId AND childId into the subcomponent (see ShowmePage including HistoryTable). I think we pass in that sub-components ID as componentId and leave it at that. Every component gets an id, but it's only used to store things like state in a unique place. The question is, do subcomponents like HistoryTable get a componentState that they can use. I'm thinking only those that actually have a definition at the site level do. Other components can use the artifact cache, but that's all. Note that the generator passed in could be a simple function to just copy the arguments for storage.

- Consider making the last parameter in computeArtifact an array of generators, each of
which takes the output of the previous. That way we can have a pipeline.






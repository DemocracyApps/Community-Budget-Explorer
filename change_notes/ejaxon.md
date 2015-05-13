5/12/2015

- 

5/11/2015

- Move js and css directories from gbe/resources/ to gbe/resources/assets to match Elixir's expected default location 
  after an update.
  

5/10/2015

- Made changes to DataModel.js. Rather than returning the rows of data, it returns an object. 
  The object has 3 members for now:
    categories: the list of category names that remain in the dataset
    dataHeaders: the list of headers for each of the included datasets
    data: the rows of data.
- Changed the reduce options to difference, percent_difference, and average.


5/9/2015

- Externalized	the dataModelId	state variable in MultiYearTable. Otherwise, when you switch pages,
  you lose the local state and the next instance of the component creates a new dataModel
- Changed stateId property for all components (and pages) to storeId.
- Added registration of components to ConfigStore. Using the StateStore-generated ID, for now,
  but there should be a separate generator.
- Added a BarchartExplorer component - it will support the difference explorer on last year's avlbudget.org, 
  but will also be more general than that. Will document when complete.
- Component JSON definition files now have a new (required) 'props' property (in addition to 'data'). The 'props'
  property lets the component designer add configuration parameters that can be set by the user when
  adding to a page (or can simply expose them, without allowing them to be changed). The property is a simple
  object containing name-value pairs. The names are the names of the properties, the values are objects that have
  two required properties: 'value' and 'configurable'. The value property is, of course, the value that will be passed in
  to the component, by default. The 'configurable' property, if true, adds to the configuration dialog for the
  page component (i.e., in addition to selecting data, the user can set property values). Right now only a 'select'
  type of configurable property is supported - see the new BarchartExplorer.json component definition file for usage.
  The value configured by the user overrides that set by default.

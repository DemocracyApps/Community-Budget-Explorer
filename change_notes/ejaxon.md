5/10/2015 by ejaxon

- Externalized	the dataModelId	state variable in MultiYearTable. Otherwise, when you switch pages,
  you lose the local state and the next instance of the component creates a new dataModel
- Changed stateId property for all components (and pages) to storeId.
- Added registration of components to ConfigStore. Using the StateStore-generated ID, for now,
  but there should be a separate generator.

 
import React from 'react';

/*
 * The BootstrapLayout takes 3 properties:
 *
 *  - layout:                  A JSON specification of the Bootstrap grid layout. Each cell of the
 *                             layout has a tag that is used in the components array to map
 *                             components to cells.
 *  - components:              Associative array of component specifications keyed by grid cell ID (see above).
 *  - reactComponents:         The actual React components keyed by component name.
 *
 */

/*
 * Each components is a is a PHP associative array translated to a Javascript object. Each array key is a
 * tag of a target cell in the layout and the value associated with it is an array of components
 * that are to be placed in that cell.
 *
 * The component specification consists of the React component name and a specification of the data which is
 * to be passed to the component. A component may have any number of data elements associated with it, each
 * with its own key for later retrieval. There are 4 types of data element:
 *
 *      - Dataset      - The ID of a single budget dataset on the server which the dataset store will retrieve
 *                       and present through an internal interface.
 *      - MultiDataset - The IDs of several individual datasets that combine into a multi-period dataset.
 *                       Again, the dataset store will retrieve and combine them for presentation through the
 *                       internal interface.
 *      - Card         - A card is a generic container for non-numeric data. It consists of a title, a body, an
 *                       image URL and a link (only the title is required). How this information is used is
 *                       entirely up to the component.
 *      - Cardset      - An ordered set of cards. Sample uses from last year's avlbudget.org site might include
 *                       the "What's New" slideshow, the resources table, etc.
 *
 * In the following loop we extract the data element information associated with each component so that it can be
 * managed by one of the data stores. The component gets an identifier that it uses to retrieve data through its state.
 */
var BootstrapLayout = React.createClass({

    getInitialState: function() {
        return {
            components:{}
        };
    },

    renderComponent: function (component, index) {
        var comp = this.props.reactComponents[component.componentName];
        return React.createElement(comp, {key:index, data:component.data});
    },

    buildColumn: function (column, index) {
        var clist = [];
        if (column.id in this.props.components) {
            clist = this.props.components[column.id];
        }
        console.log("In buildColumn with list of " + clist.length);
        return (
                <div id={column.id} key={column.id} className={column.class}>
                    <b>Layout cell: {column.id}</b>
                    {clist.map(this.renderComponent)}
                </div>
        );
    },

    buildRow: function (row, index) {
        return (
          <div key={"row_" + index} className="row">
              { row.columns.map(this.buildColumn) }
          </div>
      );
    },

    render: function() {
        if (this.props.layout == null) {
            return <div key="bootstrapLayout" >Nothing</div>
        }
        else {
            return (
                <div key="bootstrapLayout">
                    <h1> Page Title </h1>

                    {this.props.layout.rows.map(this.buildRow)}

                </div>
            );
        }
    }
});

export default BootstrapLayout;





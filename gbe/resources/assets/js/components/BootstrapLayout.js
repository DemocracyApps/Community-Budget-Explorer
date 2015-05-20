import React from 'react';

/*
 * The BootstrapLayout takes 3 properties:
 *
 *  - layout:                  A JSON specification of the Bootstrap grid layout. Each cell of the
 *                             layout has a tag that is used in the components array to map
 *                             components to cells.
 *  - components:              Associative array of component specifications keyed by grid cell ID (see layout).
 *  - reactComponents:         The actual React components keyed by component name.
 *
 */

var BootstrapLayout = React.createClass({

    renderComponent: function (component, index) {
        console.log("Looking up component type " + component.componentName);
        if (this.props.reactComponents[component.componentName]) {
            console.log("Yes we have it");
        }
        else {
            console.log("no, we do not");
        }
        var comp = this.props.reactComponents[component.componentName];
        var componentData = (component.componentData.length == 0)?{}:component.componentData;
        var componentProps = (component.componentProps.length == 0)?{}:component.componentProps;
        return React.createElement(comp, {
            key:index,
            componentData:componentData,
            componentProps:componentProps,
            storeId:component.storeId
        });
    },

    buildColumn: function (column, index) {
        var clist = [];
        if (column.id in this.props.components) {
            clist = this.props.components[column.id];
        }
        let className = column.class + " component-div"
        return (
                <div id={column.id} key={column.id} className={className} style={column.style}>
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
                    {this.props.layout.rows.map(this.buildRow)}
                </div>
            );
        }
    }
});

export default BootstrapLayout;





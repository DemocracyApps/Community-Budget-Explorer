import React from 'react';
var rd3 = require('react-d3');
var Treemap = rd3.Treemap;

var SimpleTreemap = React.createClass({

    propTypes: {
        componentData: React.PropTypes.object.isRequired,
        componentProps: React.PropTypes.object.isRequired,
        storeId: React.PropTypes.number.isRequired
    },

    clickHandler: function (context) {
       alert("Yo! Index = " + context.index + ", label = " + context.label + ", value = " + context.value);
    },
    render: function() {

        var treemapData = [
            {label: "China", value: 1364},
            {label: "India", value: 1296},
            {label: "Brazil", value: 703},
            {label: "Indonesia", value: 303},
            {label: "United States", value: 203}
        ];

        return (
            <div >
                <Treemap
                    data={treemapData}
                    width={450}
                    height={250}
                    textColor="#484848"
                    fontSize="10px"
                    title="Treemap"
                    hoverAnimation={true}
                    eventHandlers={{onClick: this.clickHandler}}
                    />
            </div>
        )
    }
});

export default SimpleTreemap;

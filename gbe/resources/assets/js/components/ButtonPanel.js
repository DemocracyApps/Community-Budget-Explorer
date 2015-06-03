/*
	React component to create a panel of buttons for the 'page toolbar' of the app.
*/

import React from 'react';

// Define PropTypes once for all components below.
var types = React.PropTypes;
var PANEL_PROP_TYPES = {
	// store id
    storeId: types.number.isRequired,
	// number of columns to take up in the toolbar
	columns: types.number.isRequired,
	// title of the panel
	title: types.string.isRequired
};

// Generic ButtonPanel.
// Try to use one of the subclasses below if you can.
class ButtonPanel extends React.Component {
	constructor(props) {
		super(props);
		this.state = this.getStateFromStore();
	}

	// Return the current state from the store.
	// You should have:
	//		- `value`  		Selected button's current value.
	//		- `options`		Array of  { title:"Button Title", value:"ButtonValue" }
	//						If you want a disabled button, leave off the value.
	getStateFromStore() {
		return { value : undefined, options:[] };
	}

	// Return the current map of options to display.

	// onChange handler, override this in your subclass.
	onButtonClicked(newValue) {
		console.error("Should be setting value to `"+value+"`.")
	}

	render() {
		this.state = this.getStateFromStore();
		// Render buttons first.
		var buttons = this.state.options.map(function(option) {
			var callback;
			if (option.value) {
				callback = this.onButtonClicked.bind(this, option.value);
			}
			else {
				callback = function noop(){};
			}

			var className = "btn btn-default"
			if (option.value === undefined) {
				className += " disabled";
			} else if (this.state.value === option.value) {
				className += " btn-primary active";
			}

			return (<button key={option.title} className={className} onClick={callback}>{option.title}</button>);
		}, this);

        return (
            <div className={"col-xs-" + this.props.columns}>
                <div className="small"><strong>{this.props.title}:</strong></div>
                <div className="btn-group" role="group" aria-label={this.props.title}>
                	{buttons}
                </div>
            </div>);
	}
};

ButtonPanel.propTypes = PANEL_PROP_TYPES;
ButtonPanel.defaultProps = {
	storeId: -1,
	columns: 4,
	title: "Untitled Panel"
}


export default ButtonPanel

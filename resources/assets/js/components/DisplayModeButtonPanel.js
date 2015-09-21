/*
	React component to create an 'Account Type' panel of buttons for the 'page toolbar' of the app.
*/

import React from 'react';

var dispatcher = require('../common/BudgetAppDispatcher');
var stateStore = require('../stores/StateStore');
var ActionTypes = require('../constants/ActionTypes');


var ButtonPanel = require('./ButtonPanel');

class DisplayModeButtonPanel extends ButtonPanel {
	constructor(props) {
		super(props);
		this.state = this.getStateFromStore();
	}

	getStateFromStore() {
		return { value : stateStore.getValue(this.props.storeId, 'displayMode'),
				 options: [
					{ value:"chart", title:"Charts"},
					{ value:"table", title:"Table"}
				 ]
			};
	}

	onButtonClicked(newValue) {
		 dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'displayMode', value: newValue}]
            }
        });
        // update state to make the button change appearance
        this.setState(this.getStateFromStore());
	}

}
DisplayModeButtonPanel.propTypes = ButtonPanel.PANEL_PROP_TYPES;
DisplayModeButtonPanel.defaultProps = {
	storeId: -1,
	columns: 4,
	title: "Display"
}


export default DisplayModeButtonPanel

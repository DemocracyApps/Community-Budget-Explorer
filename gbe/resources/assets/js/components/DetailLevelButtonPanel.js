/*
	React component to create an 'Account Type' panel of buttons for the 'page toolbar' of the app.
*/

import React from 'react';

var dispatcher = require('../common/BudgetAppDispatcher');
var stateStore = require('../stores/StateStore');
var ActionTypes = require('../constants/ActionTypes');


var ButtonPanel = require('./ButtonPanel');

class DetailLevelButtonPanel extends ButtonPanel {
	constructor(props) {
		super(props);
		this.state = this.getStateFromStore();
	}

	getStateFromStore() {
		return { value : stateStore.getValue(this.props.storeId, 'selectedLevel'),
				 options: [
					{ value:1, title:this.props.categories[0]},
					{ value:2, title:this.props.categories[1]},
					{ value:3, title:this.props.categories[2]}
				 ]
			};
	}

	onButtonClicked(newValue) {
		 dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'selectedLevel', value: newValue}]
            }
        });
        // update state to make the button change appearance
        this.setState(this.getStateFromStore());
	}

}
DetailLevelButtonPanel.propTypes = ButtonPanel.PANEL_PROP_TYPES;
DetailLevelButtonPanel.defaultProps = {
	storeId: -1,
	columns: 4,
	title: "Detail Level"
}


export default DetailLevelButtonPanel

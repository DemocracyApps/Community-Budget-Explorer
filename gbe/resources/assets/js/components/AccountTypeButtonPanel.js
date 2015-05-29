/*
	React component to create an 'Account Type' panel of buttons for the 'page toolbar' of the app.
*/

import React from 'react';

var dispatcher = require('../common/BudgetAppDispatcher');
var stateStore = require('../stores/StateStore');
var AccountTypes = require('../constants/AccountTypes');
var ActionTypes = require('../constants/ActionTypes');


var ButtonPanel = require('./ButtonPanel');

class AccountTypeButtonPanel extends ButtonPanel {
	constructor(props) {
		super(props);
		this.state = this.getStateFromStore();
	}

	getStateFromStore() {
		return { value : stateStore.getValue(this.props.storeId, 'accountType'),
				 options: [
					{ value:AccountTypes.EXPENSE, title:"Spending" },
					{ value:AccountTypes.REVENUE, title:"Revenue" }
				 ]
			};
	}

	onButtonClicked(newValue) {
		 dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'accountType', value: newValue}]
            }
        });
        // update state to make the button change appearance
        this.setState(this.getStateFromStore());
	}

}
AccountTypeButtonPanel.propTypes = ButtonPanel.PANEL_PROP_TYPES;
AccountTypeButtonPanel.defaultProps = {
	storeId: -1,
	columns: 4,
	title: "Account Type"
}


export default AccountTypeButtonPanel

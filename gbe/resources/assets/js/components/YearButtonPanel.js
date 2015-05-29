/*
	React component to create an 'Account Type' panel of buttons for the 'page toolbar' of the app.
*/

import React from 'react';

var dataModelStore = require('../stores/DataModelStore');
var dispatcher = require('../common/BudgetAppDispatcher');
var stateStore = require('../stores/StateStore');
var ActionTypes = require('../constants/ActionTypes');


var ButtonPanel = require('./ButtonPanel');

class YearButtonPanel extends ButtonPanel {
	constructor(props) {
		super(props);
		this.state = this.getStateFromStore();
	}

	getStateFromStore() {
		var dataModelId = stateStore.getValue(this.props.storeId, 'dataModelId');
		var accountType = stateStore.getValue(this.props.storeId, 'accountType');
		var currentYear = stateStore.getValue(this.props.storeId, 'currentYear');

		var dm = dataModelStore.getModel(dataModelId);
		var newData = dm.checkData({
			accountTypes:[accountType],
			startPath: [],
			nLevels: 4
		}, false);

		var options;
		if (newData == null) {
			options = [{title:"(loading...)"}];
		}
		else {
            if (currentYear < 0 && newData != null) currentYear = newData.periods.length-1;
			options = newData.periods.map(function(year, index) {
				return {value:index, title:year}
			});
		}

		return { value: currentYear, options: options };
	}

	onButtonClicked(newValue) {
		 dispatcher.dispatch({
            actionType: ActionTypes.COMPONENT_STATE_CHANGE,
            payload: {
                id: this.props.storeId,
                changes: [{name: 'currentYear', value: newValue}]
            }
        });
        // update state to make the button change appearance
        this.setState(this.getStateFromStore());
	}

}
YearButtonPanel.propTypes = ButtonPanel.PANEL_PROP_TYPES;
YearButtonPanel.defaultProps = {
	storeId: -1,
	columns: 6,
	title: "Year"
}


export default YearButtonPanel

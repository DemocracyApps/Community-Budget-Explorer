import React from 'react';

var configStore = require('../stores/ConfigStore');
var stateStore = require('../stores/StateStore');
var dispatcher = require('../common/BudgetAppDispatcher');
var ActionTypes = require('../constants/ActionTypes');

var SiteNavigation = React.createClass({

    propTypes: {
        site: React.PropTypes.object.isRequired,
        pages: React.PropTypes.array.isRequired
    },

    render: function() {

        var menuItem = function(pageId, index) {

            var page = configStore.getConfiguration('pages', pageId);

            var selectPage = function(e) {
                dispatcher.dispatch({
                    actionType: ActionTypes.STATE_CHANGE,
                    payload: {
                        changes: [
                            {
                                name: "site.currentPage",
                                value: page.id
                            }
                        ]
                    }
                });

            };

            return (
                <li key={index} role="presentation">
                    <a  id="menuPage_{page.name}" href="#"
                        onClick={selectPage}>{page.shortName}</a>
                </li>
            )
        }.bind(this);

        return (
            <ul className="nav nav-pills">
                <li role="presentation"><a href="/">Home</a></li>
                {this.props.pages.map(menuItem)}
            </ul>
        );
    }
});

export default SiteNavigation;

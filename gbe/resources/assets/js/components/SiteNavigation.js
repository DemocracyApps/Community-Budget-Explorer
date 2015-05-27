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

    goHome: function () {
        console.log("Go home");
        console.log("To: " + this.props.pages[0]);
        dispatcher.dispatch({
            actionType: ActionTypes.STATE_CHANGE,
            payload: {
                changes: [
                    {
                        name: "site.currentPage",
                        value: this.props.pages[0]
                    }
                ]
            }
        });
    }.bind(this),

    render: function() {

        var navItem = function(pageId, index) {
            var currentPage = stateStore.getValue('site.currentPage');
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
            var itemClass = (page.id == currentPage)?"active":"";
            return (
                <li key={index} className={itemClass}>
                    <a  id="menuPage_{page.name}" href="#"
                        onClick={selectPage}>{page.menuName}</a>
                </li>
            )
        }.bind(this);

        return (
            <nav className="navbar navbar-default">
                <div className="container-fluid">
                    <div className="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                            <span className="sr-only">Toggle navigation</span>
                            <span className="icon-bar"></span>
                            <span className="icon-bar"></span>
                            <span className="icon-bar"></span>
                        </button>
                        <a className="navbar-brand" href="#" onClick={this.goHome}>
                            <span style={{fontWeight:"600"}}>{this.props.site.name}</span></a>
                    </div>
                    <div id="navbar" className="navbar-collapse collapse">

                        <ul className="nav navbar-nav">
                            {this.props.pages.map(navItem)}
                        </ul>
                    </div>
                </div>
            </nav>
        );
    }
});

export default SiteNavigation;

import React from 'react';
import BootstrapLayout from './components/BootstrapLayout';
import SimpleCard from './components/SimpleCard';
import TaskApp from './components/TaskApp';

var componentsMap = {};
componentsMap['SimpleCard'] = SimpleCard;
componentsMap['Other'] = TaskApp;
var props = {layout:GBEVars.layout.specification, components:GBEVars.components, componentsMap:componentsMap}
var layout = React.render(<BootstrapLayout {...props}/>, document.getElementById('app'));
React.render(<TaskApp/>, document.getElementById('tasks'));

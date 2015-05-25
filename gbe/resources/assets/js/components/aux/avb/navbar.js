/*
File: navbar.js

Description:
    Navigation bar compoent for visual budget application

Authors:
    Ivan DiLernia <ivan@goinvo.com>
    Roger Zhu <roger@goinvo.com>

 Modified May 2015 by Eric Jackson <eric.jackson@democracyapps.us>

License:
    Copyright 2013, Involution Studios <http://goinvo.com>

    Licensed under the Apache License, Version 2.0 (the "License");
    you may not use this file except in compliance with the License.
    You may obtain a copy of the License at

      http://www.apache.org/licenses/LICENSE-2.0

    Unless required by applicable law or agreed to in writing, software
    distributed under the License is distributed on an "AS IS" BASIS,
    WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
    See the License for the specific language governing permissions and
    limitations under the License.
*/

var avb_navbar = function(){

	/*
	*	Input change event
	*/
	var searchChange = function(){
	    var  keyword = $(this).val();

	    // displays search results
	    function showResults(){
	    	// switch to table view
	        if(avb.navigation !== avb.table){
	            setMode('l');
	        };
	        // allow to go back
	        pushUrl(avb.section, avb.thisYear, 'l', avb.root.hash);
	        // show search results
	        avb.navigation.initialize($('#avb-wrap'), search(keyword));
	    }

	    // have a 300ms timeout from the time the user
	    // stops typing
	    clearTimeout(timer);
	    timer = setTimeout( showResults, 300);
	};

	/*
	*	Searches all datasets for keywords
	*
	*	@param {string} keyword - keyword to be searched
	*	@return {array} - array containing all matched nodes
	*/
	var search = function(keyword){
	    var result = [];
	    // aggregate search results from all sections
	    $.each(avb.sections, function(){
	        var searchSection = this;
	        var newResult = searchObject(keyword, avb.data[this], avb.data);
	        // remember where searched element was found
	        $.each(newResult, function() {this.section = capitalize(searchSection)});
	        result = result.concat(newResult);
	    });
	    return result;
	};

	/*
	*	Recursively searches dataset
	*
	*	@param {string} keyword - keyword to be searched
	*	@param {object} object - object to be searched
	*	@param {object} parent - object parent
	*
	*/

	var searchObject = function(keyword, object, parent){
		var index = object.key.toLowerCase().indexOf(keyword.toLowerCase());
		// ignore matches in mid word
		if (index !== 0 && object.key[index-1] !== ' ') index = -1;
		// each matched object has to report its parent name
		if(index != -1) { object.parent = parent.key}
		// results
	    var result = index !== -1 ? [object] : [];
	    // propagate recursively
	    if(object.sub !== undefined) {
	    	// propagate to all children
	        for(var i=0; i<object.sub.length; i++) {
	        	// aggregate children results
	            result = result.concat(searchObject(keyword, object.sub[i], object));
	        }
	    }
	    return result;
	};

	/*
	*	Removes right-handside portion of navbar
	*	Used in pages that do not have a map/table interactions
	*	(glossary, data...)
	*/
	var minimize = function(){
		$('#navbar-links .entry').last().remove();
	};

	/*
	*	Initialize navigation bar
	*/
	var initialize = function(thisYear, firstYear, lastYear){
		var skipMobileStuff = true;
		// year dropdown (non-mobile browsers)
		var $dropdown = $('#yeardrop-container');
		var $dropdownLabel = $('#yeardrop-label');
		var $dropdownList = $('#yeardrop-list');

		// year selector (mobile browsers)
		var $selector = $('#yeardrop-container-mobile');

		//if(!jQuery.browser.mobile) {
		if (skipMobileStuff) {

			/*
			*	Desktop browser
			*/

			$dropdownList.html('');
			// add dropdown element for each year
			for(var i=firstYear; i<=lastYear; i++) {
				// render button
				$dropdownList.append(Mustache.render($('#dropdown-template').html(), [i]));
				// attach click action
				$dropdownList.find('li').last().click(function(event) {
					event.preventDefault();
					// get year
					var year = parseInt($(this).text());
					// change dropdown active entry to selected
					$dropdownLabel.html(year + ' <b class="caret"></b>');
					// change year
					changeYear(year);
					// close dropdown
					$dropdown.removeClass('open');
				});
			}
			// set current year and show dropdown
			$dropdownLabel.html(thisYear + ' <b class="caret"></b>');
			$dropdown.show();

		} else {

			/*
			*	Mobile browser
			*/

			$selector.html('');
			// add option for each available year
			for(var i=firstYear; i<=lastYear; i++) {
				var html = '<option'
				+ ((i == thisYear) ? ' selected="selected"' : ' ')
				+ 'value="' + i + '">' + i + '</option>';
				$selector.append(html);
			}
			// change year when selection changes
			$selector.change(function(){
				changeYear(parseInt($selector.val()));
			});

			//show selector
			$selector.show();
			$('#yeardrop').css({'vertical-align' : 'top'});
		}

		// Mobile browser don't have enough h space for long titles
		// in navbar, shorten names
		//if(jQuery.browser.mobile) {
		if (!skipMobileStuff) {
			$('#navbar-map').text('Map');
			$('#navbar-table').text('Table');
			$('#navbar-funds').text('Funds');
		}
        //// hide homepage when search box is selected
        //$('#searchbox').bind('click touchstart',function () {
	    //    if ($('#avb-home').is(":visible")) avb.home.hide();
	    //});
	};

return{
	initialize : initialize,
	searchChange : searchChange,
	minimize : minimize
}
}();

export default avb_navbar;

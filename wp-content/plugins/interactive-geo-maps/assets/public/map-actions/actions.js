/**
 * Display content below the map
 */
 function igm_display_below(id, data) {
	iMapsActions.contentBelow(id, data, false);
	window.dispatchEvent(new Event('resize'));
}

/**
 * Display content below the map and scroll
 */
function igm_display_below_scroll(id, data) {
	iMapsActions.contentBelow(id, data, true);
	window.dispatchEvent(new Event('resize'));
}

/**
 * Display content above the map
 */
function igm_display_above(id, data) {
	iMapsActions.contentAbove(id, data, false);
	window.dispatchEvent(new Event('resize'));
}

/**
 * Display post or page below
 */
function igm_display_page_below(id, data) {
	iMapsActions.pageBelow(id, data, false);
}

/**
 * Display post or page below
 */
function igm_display_page_below_and_scroll(id, data) {
	iMapsActions.pageBelow(id, data, true);
}

/**
 * Display content to the right of the map with 1/3 space
 */
function igm_display_right_1_3(id, data) {
	iMapsActions.contentRight(
		id,
		data,
		"igm_content_left_2_3",
		"igm_content_right_1_3"
	);
	window.dispatchEvent(new Event('resize'));
}

/**
 * lightbox functions
 */
function igm_lightbox(id, data) {
	iMapsActions.lightboxAction(id, data, "inline");
	window.dispatchEvent(new Event('resize'));
}

function igm_lightbox_image(id, data) {
	iMapsActions.lightboxAction(id, data, "image");
}

function igm_lightbox_iframe(id, data) {
	iMapsActions.lightboxAction(id, data, "external");
}

iMapsActions = {};

// check if there's a live filter
iMapsActions.init = function () {

	var liveFilters = document.querySelectorAll('.igm-live-filter');
	for (var i = 0; i < liveFilters.length; i++) {
		iMapsActions.buildFilter(liveFilters[i]);
	}

	var liveFilterDropdowns = document.querySelectorAll('.igm-live-filter-dropdown');
	for (var i = 0; i < liveFilterDropdowns.length; i++) {
		iMapsActions.buildDropdownFilter(liveFilterDropdowns[i]);
	}

	var dropdowns = document.querySelectorAll('.igm_select_choices');
	if (dropdowns.length > 0) {
		for (var i = 0; i < dropdowns.length; i++) {
			iMapsActions.buildDropdown(dropdowns[i]);
		}
	} else {

		dropdowns = document.querySelectorAll('.igm_select');
		for (var i = 0; i < dropdowns.length; i++) {

			dropdowns[i].addEventListener('change', function (event) {
				var select = event.target;
				var id = select.getAttribute('data-map-id');
				id = parseInt(id);
				if (typeof iMapsManager !== 'undefined') {
					iMapsManager.select(id, select.value, true);
					return;
				}
				if (select.getAttribute('data-url')) {

					var hash = select.getAttribute('data-url').split('#')[1];
					if (hash) {
						var tempURL = select.getAttribute('data-url').replace('#' + hash, '');
						window.open(tempURL + '?mregion=' + select.value + '#' + hash, "_self");
					} else {
						window.open(select.getAttribute('data-url') + '?mregion=' + select.value, "_self");
					}
				}
			});

		}

	}

	// map entries list
	var lists = document.querySelectorAll('.igm_entries_list');
	for (var i = 0; i < lists.length; i++) {
		iMapsActions.buildLists(lists[i]);
	}
};

iMapsActions.buildDropdown = function (el) {

	var noResults = el.getAttribute('data-noresults');
	var select = el.getAttribute('data-select');

	var opts = {
		noResultsText: noResults,
        position: 'bottom',
		itemSelectText: select,
		resetScrollPosition: false,
        searchChoices: true,
        fuseOptions: {
            threshold: 0.4,
            findAllMatches: true,
            shouldSort: true
        },
        searchFloor: 2,
		searchResultLimit: 50, // maybe set this as an option?
	};

	var choices = new Choices(el, opts);

	choices.passedElement.element.addEventListener('change', function () {
		var id = el.getAttribute('data-map-id');
		id = parseInt(id);
		if (typeof iMapsManager !== 'undefined') {
			iMapsManager.select(id, this.value, true);
		}

		if (el.getAttribute('data-url')) {

			var hash = el.getAttribute('data-url').split('#')[1];
			if (hash) {
				var tempURL = el.getAttribute('data-url').replace('#' + hash, '');
				window.open(tempURL + '?mregion=' + el.value + '#' + hash, "_self");
			} else {
				window.open(el.getAttribute('data-url') + '?mregion=' + el.value, "_self");
			}
		}

	});
};

iMapsActions.buildLists = function (el) {
	var mapID = el.getAttribute('data-map-id');
	var liEntries = el.querySelectorAll('li');
	for (var index = 0; index < liEntries.length; index++) {
		var liEl = liEntries[index];
		liEl.addEventListener('click', function (event) {
			if (typeof iMapsManager !== 'undefined') {
				var entryCode = event.target.getAttribute('data-code');
				iMapsManager.select(mapID, entryCode, true);
				return;
			}
			if (event.target.getAttribute('data-url')) {
				window.open(event.target.getAttribute('data-url') + '?mregion=' + event.target.getAttribute('data-code'), "_self");
			}
		});
		liEl.addEventListener('mouseover', function (event) {
			if (typeof iMapsManager !== 'undefined') {
				var entryCode = event.target.getAttribute('data-code');
				iMapsManager.highlight(mapID, entryCode);
			}
		});
		liEl.addEventListener('mouseout', function (event) {
			if (typeof iMapsManager !== 'undefined') {
				iMapsManager.clearHighlighted(mapID);
			}
		});
	}
};

iMapsActions.buildDropdownFilter = function (el) {

	var mainID = el.getAttribute('data-base-map-id');
	var keepBase = el.getAttribute('data-keep-base-map');

	if (typeof keepBase !== 'undefined' && keepBase === '1') {
		keepBase = true;
	} else {
		keepBase = false;
	}

	mainID = parseInt(mainID);
	el.addEventListener('change', function (event) {

		var thisMapID = event.target.value;
		thisMapID = parseInt(thisMapID);

		iMapsManager.filteredMap = thisMapID;

		if (thisMapID === mainID) {
			iMapsManager.activeMap = mainID;
			if (typeof iMapsManager !== 'undefined') {
				iMapsManager.filteredMap = false;
				iMapsManager.showAllSeries(thisMapID);


			}
		} else {
			if (typeof iMapsManager !== 'undefined') {
				iMapsManager.hideAllSeries(mainID, keepBase);
				var thisSeries = iMaps.maps[mainID].seriesById[thisMapID];
				if (thisSeries && thisSeries.length > 0) {
					for (var ix = 0; ix < thisSeries.length; ix++) {
						var serie = thisSeries[ix];
						serie.show();
					}
				}
			}
		}

		// change this to a iMapsManager function that goes home and triggers event
		// let's try to avoid using iMaps object in this file.
		if(typeof iMaps.maps[thisMapID] !== 'undefined'){
			iMaps.maps[thisMapID].map.goHome();
			iMaps.maps[thisMapID].map.dispatchImmediately("zoomlevelchanged");
		}

	});

};

iMapsActions.buildFilter = function (el) {
	var mainID = el.getAttribute('data-base-map-id');
	var keepBase = el.getAttribute('data-keep-base-map');

	if (typeof keepBase !== 'undefined' && keepBase === '1') {
		keepBase = true;
	} else {
		keepBase = false;
	}

	mainID = parseInt(mainID);
	var liMaps = el.querySelectorAll('li');
	for (var index = 0; index < liMaps.length; index++) {
		var liEl = liMaps[index];
		liEl.addEventListener('click', function (element) {

			// remove active from all other entries
			for (var ind = 0; ind < liMaps.length; ind++) {
				var otherLi = liMaps[ind];
				otherLi.classList.remove('igm-live-filter-active');
			}
			// add active to this one
			element.target.classList.add('igm-live-filter-active');

			var thisMapID = element.target.getAttribute('data-map-id');
			thisMapID = parseInt(thisMapID);

			iMapsManager.filteredMap = thisMapID;

			if (thisMapID === mainID) {
				if (typeof iMapsManager !== 'undefined') {
					iMapsManager.filteredMap = false;
					iMapsManager.showAllSeries(thisMapID);
                    iMapsManager.resetDrilldown( thisMapID );
				}
			} else {
				if (typeof iMapsManager !== 'undefined') {
					iMapsManager.hideAllSeries(mainID, keepBase);
					var thisSeries = iMaps.maps[mainID].seriesById[thisMapID];
					if (thisSeries && thisSeries.length > 0) {
						for (var ix = 0; ix < thisSeries.length; ix++) {
							var serie = thisSeries[ix];
							serie.show();
						}
					}
				}
			}

			if(typeof iMaps.maps[mainID] !== 'undefined'){
				iMaps.maps[mainID].map.goHome();
				iMaps.maps[mainID].map.dispatchImmediately("zoomlevelchanged");
			}

		});
	}
};

iMapsActions.lightbox = false;
iMapsActions.lightboxIsRunning = false;
iMapsActions.lightboxAction = function (id, data, type) {
	var elements = [],
		width = iMapsActionOptions.lightboxWidth,
		height = iMapsActionOptions.lightboxHeight,
		opts = {};

	if (type === "inline") {

		data.content = '#' + iMapsActions.getIDfromData(data);

		elements.push({
			href: data.content,
			type: type,
			width: width,
			height: height
		});
	} else if (type === "external") {

		// iframe
		if (height === 'auto') {
			height = parseInt(window.innerHeight * 0.8);
		}

		elements.push({
			href: data.content,
			type: type,
			width: width,
			height: height
		});
	}

	// open image
	else {

		// check if there's multiple images (divided by comma)
		if (data.content !== '' && data.content.includes(",")) {
			let images = data.content.split(",");
			images.forEach(function (url, ix) {
				elements.push({
					href: url,
					type: type,
					width: width,
					height: height
				});
			});

		} else {
			elements.push({
				href: data.content,
				type: type,
				width: width,
				height: height
			});
		}
	}

	opts = {
		touchNavigation: false,
		draggable: false,
		keyboardNavigation: false,
		loopAtEnd: false,
		loop: false,
		zoomable: false,
		elements: elements,
		closeButton: false, // changed when we added the custom close button
        closeOnOutsideClick: true // changed when we added the custom close button
	};

	// fix for lightbox closing on bigger touch devices
	if (window.innerWidth > 768 && iMapsActions.isTouchScreendevice()) {
		opts.closeOnOutsideClick = false;
	}

	if (!iMapsActions.lighbox) {
		iMapsActions.lightbox = GLightbox(opts);

	}

    // add custom close button
    iMapsActions.lightbox.on('open', function(){
        let close = document.querySelector('.ginner-container .gslide-media .igm_close');
        if( ! close ) {
            close = document.createElement('span');
            close.classList.add('igm_close');
            close.innerHTML = '╳';
            let containers = document.querySelectorAll('.ginner-container .gslide-media');
            containers.forEach(function(el){
                let clone = close.cloneNode(true);
                clone.addEventListener('click', function(){
                    iMapsActions.lightbox.close();
                });

                el.prepend(clone);
            });
        } 

    });

	if( data.content !== '' && iMapsActions.lightbox && ! iMapsActions.lightboxIsRunning ){
		iMapsActions.lightbox.open();
		iMapsActions.lightboxIsRunning = true;
	} else {
		console.log('Empty Action Content or Incorrect Request - Lightbox not triggered');
	}
	
	iMapsActions.lightbox.on('close', function(){
		iMapsManager.clearSelected(id);
        iMaps.maps[id].map.lastClickedEntry = false;
		iMapsActions.lightboxIsRunning = false;
	});

};

iMapsActions.contentBelow = function (id, data, scroll) {
	// go 2 steps up to find map wrapper.
	var originalTop,
		what2display,
		what2hide,
		mapContainer = document.getElementById("map_" + id).parentNode.parentNode
		.parentNode,
		mapContentContainer = mapContainer.parentNode.querySelector(
			".igm_content_below"
		),
		footerContent = document.getElementById("igm-hidden-footer-content");

	data.content = "[id='" + iMapsActions.getIDfromData(data) + "']";

	if (mapContentContainer === null) {
		mapContentContainer = document.createElement("div");
		mapContentContainer.classList.add("igm_content_below");

		mapContainer.parentNode.insertBefore(
			mapContentContainer,
			mapContainer.nextSibling
		);
	}

	// hide
	what2hide = mapContentContainer.firstChild;
	if (what2hide) {
		if( what2hide.style ){
			what2hide.style.display = 'none';
		}
		footerContent.appendChild(what2hide);
	}

	// display this
	what2display = document.querySelector(data.content);
	if (what2display) {

		mapContentContainer.appendChild(what2display);
		if( what2display.style ){
			what2display.style.display = 'block';
		}
		
	}

	if (scroll) {
		originalTop = Math.floor(
			mapContentContainer.getBoundingClientRect().top - 100
		);
		window.scrollBy({
			top: originalTop,
			left: 0,
			behavior: "smooth"
		});
	}
};

iMapsActions.contentAbove = function (id, data, scroll) {
	// go 2 steps up to find map wrapper.
	var mapContainer = document.getElementById("map_" + id).parentNode.parentNode
		.parentNode,
		mapContentContainer = mapContainer.parentNode.querySelector(
			".igm_content_above"
		),
		what2display,
		what2hide,
		footerContent = document.getElementById("igm-hidden-footer-content");

	data.content = "[id='" + iMapsActions.getIDfromData(data) + "']";


	if (mapContentContainer === null) {
		mapContentContainer = document.createElement("div");
		mapContentContainer.classList.add("igm_content_above");

		mapContainer.parentNode.insertBefore(
			mapContentContainer,
			mapContainer.parentNode.childNodes[0]
		);
	}

	// hide
	what2hide = mapContentContainer.firstChild;
	if (what2hide) {
		what2hide.style.display = 'none';
		footerContent.appendChild(what2hide);
	}

	// display this
	what2display = document.querySelector(data.content);
	if (what2display) {
		mapContentContainer.appendChild(what2display);
		what2display.style.display = 'block';
	}

	if (scroll) {
		originalTop = Math.floor(
			mapContentContainer.getBoundingClientRect().top - 100
		);
		window.scrollBy({
			top: originalTop,
			left: 0,
			behavior: "smooth"
		});
	}
};
iMapsActions.contentRight = function (id, data, mapClass, contentClass) {
	// go 2 steps up to find map wrapper.
	var what2display,
		what2hide,
		mapContainer = document.getElementById("map_" + id).parentNode.parentNode
		.parentNode,
		mapContentContainer = mapContainer.parentNode.querySelector(
			"." + contentClass
		),
		mapBox = mapContainer.parentNode.querySelector("." + mapClass),
		footerContent = document.getElementById("igm-hidden-footer-content");

	data.content = "[id='" + iMapsActions.getIDfromData(data) + "']";

	if (mapBox === null) {
		mapBox = mapContainer.parentNode.querySelector(".map_box");
		mapBox.classList.add(mapClass);
	}

	if (mapContentContainer === null) {
		mapContentContainer = document.createElement("div");
		mapContentContainer.classList.add(contentClass);

		mapContainer.parentNode.insertBefore(
			mapContentContainer,
			mapContainer.nextSibling
		);
	}

	// hide
	what2hide = mapContentContainer.firstChild;
	if (what2hide) {
		what2hide.style.display = 'none';
		footerContent.appendChild(what2hide);
	}

	// display this
	what2display = document.querySelector(data.content);

	if (what2display) {
		mapContentContainer.appendChild(what2display);
		what2display.style.display = 'block';
	}

};

iMapsActions.pageBelow = function (id, data, scroll) {

	if (data.content === '') {
		return;
	}

	var pageId = parseInt(data.content);
	var url = iMapsActionOptions.restURL + 'pages/' + pageId;
	var originalTop;

	// go 2 steps up to find map wrapper.
	var mapContainer = document.getElementById("map_" + id).parentNode.parentNode
		.parentNode,
		mapContentContainer = mapContainer.parentNode.querySelector(
			".igm_content_below"
		);

	var ourRequest = new XMLHttpRequest();
	ourRequest.open('GET', url);
	ourRequest.onload = function () {
		if (ourRequest.status >= 200 && ourRequest.status < 400) {
			var responseData = JSON.parse(ourRequest.response);

			mapContentContainer.innerHTML = responseData.content.rendered;

			if (scroll) {
				originalTop = Math.floor(
					mapContentContainer.getBoundingClientRect().top - 100
				);
				window.scrollBy({
					top: originalTop,
					left: 0,
					behavior: "smooth"
				});
			}

		} else {
			console.log('We connected to the server, but it returned an error.');
		}
	};

	ourRequest.onerror = function () {
		console.log('Connection error');
	};

	ourRequest.send();

};

iMapsActions.getIDfromData = function (data) {
	var id;

	if (typeof data.originalID !== 'undefined') {

		id = data.originalID.replace(/\s/g, "");
		id = id.replace(/,/g, "_");
		id = iMapsActions.wpFeSanitizeTitle(id);
		//data.content = "#" + id + '_' + data.mapID;
		// we use this format, because of ids that start with numbers
		data.content = id + '_' + data.mapID;
	} else {

		if (Number.isInteger(data.id)) {
			data.id = data.id.toString();
		}

		id = data.id.replace(/\s/g, "");
		id = id.replace(/,/g, "_");
		id = iMapsActions.wpFeSanitizeTitle(id);
		// data.content = "#" + id + '_' + data.mapID;
		// we use this format, because of ids that start with numbers
		data.content = id + '_' + data.mapID;
	}
	return data.content.toLowerCase();
};

/**
 * Original Source: https://salferrarello.com/wordpress-sanitize-title-javascript/
 *
 * JavaScript function to mimic the WordPress PHP function sanitize_title()
 * See https://codex.wordpress.org/Function_Reference/sanitize_title
 *
 * Note: the WordPress PHP function sanitize_title() accepts two additional
 * optional parameters. At this time, this function does not.
 *
 * @param string title The title to be santized.
 * @return string The sanitized string.
 */

//php: 4024kiszynic3b3w3d5cwith2fspaced0b4d0b4d0b4d0b428par2922yes7c7c_comma-minus25_101011
//js : 4024kiszynic3b3w3d5cwith2fspaced0b4d0b4d0b4d0b428par2922yes7c7c_comma-minus25_101011

iMapsActions.wpFeSanitizeTitle = function (title) {
	var diacriticsMap;
	title = title.replace(/\ /g, "").replace(/\,/g, "_");
	title = encodeURIComponent(title).replace(/\(/g, "%28").replace(/\)/g, "%29").replace(/%/g, "").toLowerCase();
	return title;

	return removeSingleTrailingDash(
		replaceSpacesWithDash(
			removeAccents(
				// Strip any HTML tags.
				title.replace(/<[^>]+>/ig, '')
			).toLowerCase()
			// Replace any forward slashes (/) or periods (.) with a dash (-).
			.replace(/[\/\.]/g, '-')
			// Replace anything that is not a:
			// word character
			// space
			// nor a dash (-)
			// with an empty string (i.e. remove it).
			.replace(/[^\w\s-]+/g, '')
		)
	);

	/**
	 * Replace one or more blank spaces (or repeated dashes) with a single dash.
	 *
	 * @param str String that may contain spaces or multiple dashes.
	 * @return String with spaces replaced by dashes and no more than one dash in a row.
	 */
	function replaceSpacesWithDash(str) {
		return str
			// Replace one or more blank spaces with a single dash (-)
			.replace(/ +/g, '-')
			// Replace two or more dashes (-) with a single dash (-).
			.replace(/-{2,}/g, '-');
	}

	/**
	 * If the string end in a dash, remove it.
	 *
	 * @param string str The string which may or may not end in a dash.
	 * @return string The string without a dash on the end.
	 */
	function removeSingleTrailingDash(str) {
		if ('-' === str.substr(str.length - 1)) {
			return str.substr(0, str.length - 1);
		}
		return str;
	}

	/* Remove accents/diacritics in a string in JavaScript
	 * from https://stackoverflow.com/a/18391901
	 */

	/*
	 * Licensed under the Apache License, Version 2.0 (the "License");
	 * you may not use this file except in compliance with the License.
	 * You may obtain a copy of the License at
	 *
	 * http://www.apache.org/licenses/LICENSE-2.0
	 *
	 * Unless required by applicable law or agreed to in writing, software
	 * distributed under the License is distributed on an "AS IS" BASIS,
	 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
	 * See the License for the specific language governing permissions and
	 * limitations under the License.
	 */
	function getDiacriticsRemovalMap() {
		if (diacriticsMap) {
			return diacriticsMap;
		}
		var defaultDiacriticsRemovalMap = [{
				'base': 'A',
				'letters': '\u0041\u24B6\uFF21\u00C0\u00C1\u00C2\u1EA6\u1EA4\u1EAA\u1EA8\u00C3\u0100\u0102\u1EB0\u1EAE\u1EB4\u1EB2\u0226\u01E0\u00C4\u01DE\u1EA2\u00C5\u01FA\u01CD\u0200\u0202\u1EA0\u1EAC\u1EB6\u1E00\u0104\u023A\u2C6F'
			},
			{
				'base': 'AA',
				'letters': '\uA732'
			},
			{
				'base': 'AE',
				'letters': '\u00C6\u01FC\u01E2'
			},
			{
				'base': 'AO',
				'letters': '\uA734'
			},
			{
				'base': 'AU',
				'letters': '\uA736'
			},
			{
				'base': 'AV',
				'letters': '\uA738\uA73A'
			},
			{
				'base': 'AY',
				'letters': '\uA73C'
			},
			{
				'base': 'B',
				'letters': '\u0042\u24B7\uFF22\u1E02\u1E04\u1E06\u0243\u0182\u0181'
			},
			{
				'base': 'C',
				'letters': '\u0043\u24B8\uFF23\u0106\u0108\u010A\u010C\u00C7\u1E08\u0187\u023B\uA73E'
			},
			{
				'base': 'D',
				'letters': '\u0044\u24B9\uFF24\u1E0A\u010E\u1E0C\u1E10\u1E12\u1E0E\u0110\u018B\u018A\u0189\uA779\u00D0'
			},
			{
				'base': 'DZ',
				'letters': '\u01F1\u01C4'
			},
			{
				'base': 'Dz',
				'letters': '\u01F2\u01C5'
			},
			{
				'base': 'E',
				'letters': '\u0045\u24BA\uFF25\u00C8\u00C9\u00CA\u1EC0\u1EBE\u1EC4\u1EC2\u1EBC\u0112\u1E14\u1E16\u0114\u0116\u00CB\u1EBA\u011A\u0204\u0206\u1EB8\u1EC6\u0228\u1E1C\u0118\u1E18\u1E1A\u0190\u018E'
			},
			{
				'base': 'F',
				'letters': '\u0046\u24BB\uFF26\u1E1E\u0191\uA77B'
			},
			{
				'base': 'G',
				'letters': '\u0047\u24BC\uFF27\u01F4\u011C\u1E20\u011E\u0120\u01E6\u0122\u01E4\u0193\uA7A0\uA77D\uA77E'
			},
			{
				'base': 'H',
				'letters': '\u0048\u24BD\uFF28\u0124\u1E22\u1E26\u021E\u1E24\u1E28\u1E2A\u0126\u2C67\u2C75\uA78D'
			},
			{
				'base': 'I',
				'letters': '\u0049\u24BE\uFF29\u00CC\u00CD\u00CE\u0128\u012A\u012C\u0130\u00CF\u1E2E\u1EC8\u01CF\u0208\u020A\u1ECA\u012E\u1E2C\u0197'
			},
			{
				'base': 'J',
				'letters': '\u004A\u24BF\uFF2A\u0134\u0248'
			},
			{
				'base': 'K',
				'letters': '\u004B\u24C0\uFF2B\u1E30\u01E8\u1E32\u0136\u1E34\u0198\u2C69\uA740\uA742\uA744\uA7A2'
			},
			{
				'base': 'L',
				'letters': '\u004C\u24C1\uFF2C\u013F\u0139\u013D\u1E36\u1E38\u013B\u1E3C\u1E3A\u0141\u023D\u2C62\u2C60\uA748\uA746\uA780'
			},
			{
				'base': 'LJ',
				'letters': '\u01C7'
			},
			{
				'base': 'Lj',
				'letters': '\u01C8'
			},
			{
				'base': 'M',
				'letters': '\u004D\u24C2\uFF2D\u1E3E\u1E40\u1E42\u2C6E\u019C'
			},
			{
				'base': 'N',
				'letters': '\u004E\u24C3\uFF2E\u01F8\u0143\u00D1\u1E44\u0147\u1E46\u0145\u1E4A\u1E48\u0220\u019D\uA790\uA7A4'
			},
			{
				'base': 'NJ',
				'letters': '\u01CA'
			},
			{
				'base': 'Nj',
				'letters': '\u01CB'
			},
			{
				'base': 'O',
				'letters': '\u004F\u24C4\uFF2F\u00D2\u00D3\u00D4\u1ED2\u1ED0\u1ED6\u1ED4\u00D5\u1E4C\u022C\u1E4E\u014C\u1E50\u1E52\u014E\u022E\u0230\u00D6\u022A\u1ECE\u0150\u01D1\u020C\u020E\u01A0\u1EDC\u1EDA\u1EE0\u1EDE\u1EE2\u1ECC\u1ED8\u01EA\u01EC\u00D8\u01FE\u0186\u019F\uA74A\uA74C'
			},
			{
				'base': 'OI',
				'letters': '\u01A2'
			},
			{
				'base': 'OO',
				'letters': '\uA74E'
			},
			{
				'base': 'OU',
				'letters': '\u0222'
			},
			{
				'base': 'OE',
				'letters': '\u008C\u0152'
			},
			{
				'base': 'oe',
				'letters': '\u009C\u0153'
			},
			{
				'base': 'P',
				'letters': '\u0050\u24C5\uFF30\u1E54\u1E56\u01A4\u2C63\uA750\uA752\uA754'
			},
			{
				'base': 'Q',
				'letters': '\u0051\u24C6\uFF31\uA756\uA758\u024A'
			},
			{
				'base': 'R',
				'letters': '\u0052\u24C7\uFF32\u0154\u1E58\u0158\u0210\u0212\u1E5A\u1E5C\u0156\u1E5E\u024C\u2C64\uA75A\uA7A6\uA782'
			},
			{
				'base': 'S',
				'letters': '\u0053\u24C8\uFF33\u1E9E\u015A\u1E64\u015C\u1E60\u0160\u1E66\u1E62\u1E68\u0218\u015E\u2C7E\uA7A8\uA784'
			},
			{
				'base': 'T',
				'letters': '\u0054\u24C9\uFF34\u1E6A\u0164\u1E6C\u021A\u0162\u1E70\u1E6E\u0166\u01AC\u01AE\u023E\uA786'
			},
			{
				'base': 'TZ',
				'letters': '\uA728'
			},
			{
				'base': 'U',
				'letters': '\u0055\u24CA\uFF35\u00D9\u00DA\u00DB\u0168\u1E78\u016A\u1E7A\u016C\u00DC\u01DB\u01D7\u01D5\u01D9\u1EE6\u016E\u0170\u01D3\u0214\u0216\u01AF\u1EEA\u1EE8\u1EEE\u1EEC\u1EF0\u1EE4\u1E72\u0172\u1E76\u1E74\u0244'
			},
			{
				'base': 'V',
				'letters': '\u0056\u24CB\uFF36\u1E7C\u1E7E\u01B2\uA75E\u0245'
			},
			{
				'base': 'VY',
				'letters': '\uA760'
			},
			{
				'base': 'W',
				'letters': '\u0057\u24CC\uFF37\u1E80\u1E82\u0174\u1E86\u1E84\u1E88\u2C72'
			},
			{
				'base': 'X',
				'letters': '\u0058\u24CD\uFF38\u1E8A\u1E8C'
			},
			{
				'base': 'Y',
				'letters': '\u0059\u24CE\uFF39\u1EF2\u00DD\u0176\u1EF8\u0232\u1E8E\u0178\u1EF6\u1EF4\u01B3\u024E\u1EFE'
			},
			{
				'base': 'Z',
				'letters': '\u005A\u24CF\uFF3A\u0179\u1E90\u017B\u017D\u1E92\u1E94\u01B5\u0224\u2C7F\u2C6B\uA762'
			},
			{
				'base': 'a',
				'letters': '\u0061\u24D0\uFF41\u1E9A\u00E0\u00E1\u00E2\u1EA7\u1EA5\u1EAB\u1EA9\u00E3\u0101\u0103\u1EB1\u1EAF\u1EB5\u1EB3\u0227\u01E1\u00E4\u01DF\u1EA3\u00E5\u01FB\u01CE\u0201\u0203\u1EA1\u1EAD\u1EB7\u1E01\u0105\u2C65\u0250'
			},
			{
				'base': 'aa',
				'letters': '\uA733'
			},
			{
				'base': 'ae',
				'letters': '\u00E6\u01FD\u01E3'
			},
			{
				'base': 'ao',
				'letters': '\uA735'
			},
			{
				'base': 'au',
				'letters': '\uA737'
			},
			{
				'base': 'av',
				'letters': '\uA739\uA73B'
			},
			{
				'base': 'ay',
				'letters': '\uA73D'
			},
			{
				'base': 'b',
				'letters': '\u0062\u24D1\uFF42\u1E03\u1E05\u1E07\u0180\u0183\u0253'
			},
			{
				'base': 'c',
				'letters': '\u0063\u24D2\uFF43\u0107\u0109\u010B\u010D\u00E7\u1E09\u0188\u023C\uA73F\u2184'
			},
			{
				'base': 'd',
				'letters': '\u0064\u24D3\uFF44\u1E0B\u010F\u1E0D\u1E11\u1E13\u1E0F\u0111\u018C\u0256\u0257\uA77A'
			},
			{
				'base': 'dz',
				'letters': '\u01F3\u01C6'
			},
			{
				'base': 'e',
				'letters': '\u0065\u24D4\uFF45\u00E8\u00E9\u00EA\u1EC1\u1EBF\u1EC5\u1EC3\u1EBD\u0113\u1E15\u1E17\u0115\u0117\u00EB\u1EBB\u011B\u0205\u0207\u1EB9\u1EC7\u0229\u1E1D\u0119\u1E19\u1E1B\u0247\u025B\u01DD'
			},
			{
				'base': 'f',
				'letters': '\u0066\u24D5\uFF46\u1E1F\u0192\uA77C'
			},
			{
				'base': 'g',
				'letters': '\u0067\u24D6\uFF47\u01F5\u011D\u1E21\u011F\u0121\u01E7\u0123\u01E5\u0260\uA7A1\u1D79\uA77F'
			},
			{
				'base': 'h',
				'letters': '\u0068\u24D7\uFF48\u0125\u1E23\u1E27\u021F\u1E25\u1E29\u1E2B\u1E96\u0127\u2C68\u2C76\u0265'
			},
			{
				'base': 'hv',
				'letters': '\u0195'
			},
			{
				'base': 'i',
				'letters': '\u0069\u24D8\uFF49\u00EC\u00ED\u00EE\u0129\u012B\u012D\u00EF\u1E2F\u1EC9\u01D0\u0209\u020B\u1ECB\u012F\u1E2D\u0268\u0131'
			},
			{
				'base': 'j',
				'letters': '\u006A\u24D9\uFF4A\u0135\u01F0\u0249'
			},
			{
				'base': 'k',
				'letters': '\u006B\u24DA\uFF4B\u1E31\u01E9\u1E33\u0137\u1E35\u0199\u2C6A\uA741\uA743\uA745\uA7A3'
			},
			{
				'base': 'l',
				'letters': '\u006C\u24DB\uFF4C\u0140\u013A\u013E\u1E37\u1E39\u013C\u1E3D\u1E3B\u017F\u0142\u019A\u026B\u2C61\uA749\uA781\uA747'
			},
			{
				'base': 'lj',
				'letters': '\u01C9'
			},
			{
				'base': 'm',
				'letters': '\u006D\u24DC\uFF4D\u1E3F\u1E41\u1E43\u0271\u026F'
			},
			{
				'base': 'n',
				'letters': '\u006E\u24DD\uFF4E\u01F9\u0144\u00F1\u1E45\u0148\u1E47\u0146\u1E4B\u1E49\u019E\u0272\u0149\uA791\uA7A5'
			},
			{
				'base': 'nj',
				'letters': '\u01CC'
			},
			{
				'base': 'o',
				'letters': '\u006F\u24DE\uFF4F\u00F2\u00F3\u00F4\u1ED3\u1ED1\u1ED7\u1ED5\u00F5\u1E4D\u022D\u1E4F\u014D\u1E51\u1E53\u014F\u022F\u0231\u00F6\u022B\u1ECF\u0151\u01D2\u020D\u020F\u01A1\u1EDD\u1EDB\u1EE1\u1EDF\u1EE3\u1ECD\u1ED9\u01EB\u01ED\u00F8\u01FF\u0254\uA74B\uA74D\u0275'
			},
			{
				'base': 'oi',
				'letters': '\u01A3'
			},
			{
				'base': 'ou',
				'letters': '\u0223'
			},
			{
				'base': 'oo',
				'letters': '\uA74F'
			},
			{
				'base': 'p',
				'letters': '\u0070\u24DF\uFF50\u1E55\u1E57\u01A5\u1D7D\uA751\uA753\uA755'
			},
			{
				'base': 'q',
				'letters': '\u0071\u24E0\uFF51\u024B\uA757\uA759'
			},
			{
				'base': 'r',
				'letters': '\u0072\u24E1\uFF52\u0155\u1E59\u0159\u0211\u0213\u1E5B\u1E5D\u0157\u1E5F\u024D\u027D\uA75B\uA7A7\uA783'
			},
			{
				'base': 's',
				'letters': '\u0073\u24E2\uFF53\u00DF\u015B\u1E65\u015D\u1E61\u0161\u1E67\u1E63\u1E69\u0219\u015F\u023F\uA7A9\uA785\u1E9B'
			},
			{
				'base': 't',
				'letters': '\u0074\u24E3\uFF54\u1E6B\u1E97\u0165\u1E6D\u021B\u0163\u1E71\u1E6F\u0167\u01AD\u0288\u2C66\uA787'
			},
			{
				'base': 'tz',
				'letters': '\uA729'
			},
			{
				'base': 'u',
				'letters': '\u0075\u24E4\uFF55\u00F9\u00FA\u00FB\u0169\u1E79\u016B\u1E7B\u016D\u00FC\u01DC\u01D8\u01D6\u01DA\u1EE7\u016F\u0171\u01D4\u0215\u0217\u01B0\u1EEB\u1EE9\u1EEF\u1EED\u1EF1\u1EE5\u1E73\u0173\u1E77\u1E75\u0289'
			},
			{
				'base': 'v',
				'letters': '\u0076\u24E5\uFF56\u1E7D\u1E7F\u028B\uA75F\u028C'
			},
			{
				'base': 'vy',
				'letters': '\uA761'
			},
			{
				'base': 'w',
				'letters': '\u0077\u24E6\uFF57\u1E81\u1E83\u0175\u1E87\u1E85\u1E98\u1E89\u2C73'
			},
			{
				'base': 'x',
				'letters': '\u0078\u24E7\uFF58\u1E8B\u1E8D'
			},
			{
				'base': 'y',
				'letters': '\u0079\u24E8\uFF59\u1EF3\u00FD\u0177\u1EF9\u0233\u1E8F\u00FF\u1EF7\u1E99\u1EF5\u01B4\u024F\u1EFF'
			},
			{
				'base': 'z',
				'letters': '\u007A\u24E9\uFF5A\u017A\u1E91\u017C\u017E\u1E93\u1E95\u01B6\u0225\u0240\u2C6C\uA763'
			}
		];

		diacriticsMap = {};
		for (var i = 0; i < defaultDiacriticsRemovalMap.length; i++) {
			var letters = defaultDiacriticsRemovalMap[i].letters;
			for (var j = 0; j < letters.length; j++) {
				diacriticsMap[letters[j]] = defaultDiacriticsRemovalMap[i].base;
			}
		}
		return diacriticsMap;
	}

	// Remove accent characters/diacritics from the string.
	function removeAccents(str) {
		diacriticsMap = getDiacriticsRemovalMap();
		return str.replace(/[^\u0000-\u007E]/g, function (a) {
			return diacriticsMap[a] || a;
		});
	}

	function urlenc(str) {
		return encodeURI(str).toLowerCase();
	}
};

iMapsActions.resetContainer = function (id, selector) {
	var mapContainer = document.getElementById("map_wrapper_" + id);
	var footerContent = document.getElementById("igm-hidden-footer-content");
	var mapContentContainer, what2hide;

	// if map container doesn't exist, return. we might be trying to reset a container with id of an overlay
	if( mapContainer === null ) {
		return;
	}

	mapContentContainer = mapContainer.querySelector(selector);

	if (mapContentContainer !== null) {
		
		what2hide = mapContentContainer.firstChild;
		if (what2hide && footerContent) {
			what2hide.style.display = 'none';
			footerContent.appendChild(what2hide);
		}
		what2display = document.getElementById('default_' + id);
		if (what2display) {
			mapContentContainer.appendChild(what2display);
			what2display.style.display = 'block';
		}
	}
};

iMapsActions.resetActions = function (id) {

	iMapsActions.resetContainer(id, ".igm_content_below");
	iMapsActions.resetContainer(id, ".igm_content_above");
	iMapsActions.resetContainer(id, ".igm_content_right_1_3");

};

iMapsActions.isTouchScreendevice = function () {
	return 'ontouchstart' in window || navigator.maxTouchPoints;
};


iMapsActions.loadScript = function (url, callback) {
	var script = document.createElement('script');
	script.type = 'text/javascript';
	script.src = url;
	script.onreadystatechange = callback;
	script.onload = callback;
	document.head.appendChild(script);
};

iMapsActions.loadScripts = function (urls, callback) {

	var loadedCount = 0;
	var multiCallback = function () {
		loadedCount++;
		if (loadedCount >= urls.length) {
			callback.call(this, arguments);
		}
	};

	urls.forEach(function (url, index) {
		iMapsActions.loadScript(url, multiCallback);
	});
};

if (typeof iMapsActionOptions.async !== 'undefined' && Array.isArray(iMapsActionOptions.urls) && iMapsActionOptions.urls.length > 0) {

	iMapsActions.loadScripts(iMapsActionOptions.urls, function () {
		iMapsActions.init();
	});

} else {
	iMapsActions.init();
}
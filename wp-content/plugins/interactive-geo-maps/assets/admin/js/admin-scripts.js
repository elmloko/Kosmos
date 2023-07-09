iMapsBuilder = {
	searchInput: {},
	autocomplete: {},
	geocoder: {},
	regionData: {},
	regionsUsed: {},
	imageSaved: false,
	needsUpdate: false,
	previewBlocked: false,
	advanced:false,
	form: document.querySelector("form#post"),
};


iMapsBuilder.editControls = function(){

	// Basic
	let addMarker = document.createElement("div");
	addMarker.setAttribute('id','map_click_add_marker');
	addMarker.innerHTML = '<span class="dashicons dashicons-plus-alt"></span><span class="map_action_label">Add Marker There</span>';
	
	let coordinatesBox = document.getElementById("map_clicked_coordinates_box");
	coordinatesBox.appendChild(addMarker);
	coordinatesBox.style.display = 'block';
	

	// Advanced
	if( ! iMapsBuilder.advanced ){
		return;
	}

	let setCenter = document.createElement("div");
	setCenter.setAttribute('id','map_set_center');
	setCenter.setAttribute('title','Set as initial zoom and center');
	setCenter.innerHTML = '<span class="dashicons dashicons-admin-post"></span><span class="map_action_label"></span>';
	
	setCenter.addEventListener('click',function(ev){
		let centerData = document.getElementById('map_visual_info').dataset.visual;
		centerData = JSON.parse(centerData);
		iMapsBuilder.setInitialCenter(centerData);
		let icon = ev.target;
		icon.classList.remove('dashicons-admin-post');
		icon.classList.add('dashicons-yes-alt');
		setTimeout(function(){
			icon.classList.remove('dashicons-yes-alt');
			icon.classList.add('dashicons-admin-post');
		},2500);
	});

	let visualBox = document.getElementById("map_visual_info_box");
	visualBox.appendChild(setCenter);
	visualBox.style.display = 'block';


}

iMapsBuilder.setInitialCenter = function(centerData){

	let zoomField = document.getElementsByName("map_info[viewport][zoomLevel]")[0];
	let latitudeField = document.getElementsByName("map_info[viewport][homeGeoPoint][latitude]")[0];
	let longitudeField = document.getElementsByName("map_info[viewport][homeGeoPoint][longitude]")[0];
	zoomField.value = centerData.zoom;
	latitudeField.value = centerData.lat;
	longitudeField.value = centerData.long;

	// rebuildPreview
	const event = new Event('change');  
	longitudeField.dispatchEvent(event);

}

iMapsBuilder.listenToClicks = function( mapID ){

	console.log( iMapsData.data[0].container );
	document.getElementById( iMapsData.data[0].container ).addEventListener('mapEntryClicked', function(ev) { 
		let data = iMapsManager.maps[mapID].map.lastClickedEntry.dataItem.dataContext;
		iMapsBuilder.populateClickInfo(data);
	});
	
}

iMapsBuilder.populateClickInfo = function (data) {
	var container = document.getElementById("map_click_events_info"),
	info = "";
	controls = false;

	if (container && data) {
	info += "ID: " + data.id + "<br>";

	if (data.name) {
		info += "Name: " + data.name + "<br>";
	}

	if (data.madeFromGeoData) {
		controls = iMapsBuilder.addRegionButton(data.id, data.name);
		controls.addEventListener('click',function(ev){
			ev.target.classList.remove('dashicons-plus-alt');
			ev.target.classList.add('dashicons-yes-alt');
			ev.target.style.pointerEvents = "none";
		});
	}

	if (data.latitude) {
		info += "LAT: " + Number(data.latitude).toFixed(6) + "<br>";
		info += "LONG: " + Number(data.longitude).toFixed(6) + "<br>";
	}

	if (data.action) {
		info += "Action: " + data.action.replace("igm_", "") + "<br>";
	}
	container.parentElement.style.display = 'block'
	container.innerHTML = info;

	if(controls){
		container.appendChild(controls);
	}
	
	}
};


iMapsBuilder.checkMaxInputVars = function () {
	var numNormInputs = document.getElementsByTagName("input").length;
	var numTextarea = document.getElementsByTagName("textarea").length;
	var numInputs = numNormInputs + numTextarea;
	var warning = "";
	var firstBox = document.getElementById("titlediv");
	var reqBox = document.getElementById("map_min_req");
	var icon = '<span class="dashicons dashicons-yes-alt"></span>';

	console.log("Number of inputs: ", numInputs + numTextarea);
	console.log("Max Input Vars: ", iMapsOptions.max_input_vars);

	if (parseInt(numInputs) > parseInt(iMapsOptions.max_input_vars * 0.75)) {
		warning = document.createElement("div");
		warning.setAttribute("class", "notice notice-error is-dismissible");
		warning.innerHTML = "<h1>" + iMapsOptions.messages.maxInputError + "</h1>";
		icon =
			firstBox.insertBefore(warning, firstBox.childNodes[0]);

		icon = '<span class="dashicons dashicons-warning"></span>';
	}

	reqBox.innerHTML = 'max_input_vars: ' + numInputs + '/' + iMapsOptions.max_input_vars + ' ' + icon;
};

iMapsBuilder.init = function () {
	var saveBtn = document.getElementById("publish"),
	mapID = document.getElementById("post_ID").value;

	// add listner to accordions and clone buttons for maxInputVars
	iMapsBuilder.addRepeatFieldsListener();

	//check if advanced
	if( document.body.classList.contains('igm-pro') ){
		iMapsBuilder.advanced = true;
	}

	// remember tabs
    try {
        iMapsBuilder.rememberTabInit();
    } catch( err ) {
        console.log('Error: will not remember tab;');
    }

	// click action warning
	iMapsBuilder.clickActionWarnings();

	// add preview
	if (typeof iMapsData !== "undefined") {
		iMapsBuilder.buildPreview();
		iMapsBuilder.initAvailableRegions();
		iMapsBuilder.addPreviewImage();
		iMapsBuilder.listenToClicks( mapID );
	}

	// save button
	saveBtn.addEventListener("click", function (ev) {
		var mapImageField = document.getElementsByName("map_image[mapImage]")[0],
			mapId = document.getElementById("post_ID").value;

		if (typeof mapImageField === 'undefined') {
			return;
		}

		if (!iMapsBuilder.imageSaved) {

			if (iMaps.maps && iMaps.maps[mapId] && iMaps.maps[mapId].map) {
				ev.preventDefault();

				var zoomLevel = iMaps.maps[mapId].map.zoomLevel;
				if (zoomLevel !== 1) {
					iMaps.maps[mapId].map.goHome(0);
				}

				iMaps.maps[mapId].map.exporting.getImage("svg").then(function (imgData) {
					mapImageField.value = imgData;
					iMapsBuilder.imageSaved = true;
					ev.target.click();
				});
			}
		}
	});

	// check for overlay and make sortable
	iMapsBuilder.sortableOverlay();

    // check for auto labels clear link
    iMapsBuilder.autoLabelsReset();

	// geocoding fields
	if (iMapsOptions.googleApiKey && iMapsOptions.googleApiKey !== "") {
		var geocodingFields = document.getElementsByClassName("geocoding");
		for (var i = 0; i < geocodingFields.length; i++) {
			geocodingFields.item(i).classList.remove("geocoding-hide");
		}

		// enable google autocomplete
		if (
			iMapsOptions.googleAutocomplete &&
			iMapsOptions.googleAutocomplete === "1"
		) {
			iMapsBuilder.initGoogleAutocomplete();
		} else {
			iMapsBuilder.initGeocoding();
		}
	}

	iMapsBuilder.editControls();
	iMapsBuilder.prepareAddMarkerActions();

	// check number of inputs
	setTimeout(function () {
		iMapsBuilder.checkMaxInputVars();
	}, 1000);
};

iMapsBuilder.addRepeatFieldsListener = function () {

	var cloneBts = document.getElementsByClassName('csf-cloneable-add');

	for (var i = 0; i < cloneBts.length; i++) {
		cloneBts[i].addEventListener('click', iMapsBuilder.checkMaxInputVars, false);
	}

};

iMapsBuilder.clickActionWarnings = function () {

	var clickActionBts = document.getElementsByName("map_info[regionDefaults][action]")[0];
	var clickActionMarkerBts = document.getElementsByName("map_info[markerDefaults][action]")[0];
	var mainContainer = document.getElementById("map_info");

	clickActionBts.addEventListener('change', function (ev) {
		if (this.value !== 'none') {
			mainContainer.classList.add('igm_region_click_check');
		} else {
			mainContainer.classList.remove('igm_region_click_check');
		}
	});

	clickActionMarkerBts.addEventListener('change', function (ev) {
		if (this.value !== 'none') {
			mainContainer.classList.add('igm_marker_click_check');
		} else {
			mainContainer.classList.remove('igm_marker_click_check');
		}
	});

	// on initial load, check them
	if (clickActionBts && clickActionBts.value !== 'none') {
		mainContainer.classList.add('igm_region_click_check');
	}

	if (clickActionMarkerBts && clickActionMarkerBts.value !== 'none') {
		mainContainer.classList.add('igm_marker_click_check');
	}

};

/** 
 * Set up click event to reset auto labels custom coordinates 
 */
iMapsBuilder.autoLabelsReset = function() {
    var clearLink = document.getElementById('igm-reset-auto-labels');
    if(clearLink) { 
        clearLink.addEventListener( "click", function (ev) {
                if( confirm(iMapsOptions.messages.resetAutoLabels) ){
                    var regionLabelsCustomCoordinates = document.getElementsByName('map_info[regionLabels][regionLabelCustomCoordinates]')[0];
                    regionLabelsCustomCoordinates.value = '';
                    regionLabelsCustomCoordinates.dispatchEvent(new Event("change"));
                }
            } 
        );
    }
}

iMapsBuilder.sortableOverlay = function () {

	// Using jQuery since we'll use jquery-ui for sortable anyway
	var overlayParent = jQuery('[data-depend-id=overlay]');
	var overlaySortable;

	if (overlayParent) {
		overlaySortable = overlayParent.first().closest('ul');

		// take advantage of this function to also set the title attribute for the li with the map id
		overlaySortable.find('li').each(function () {
			jQuery(this).attr('title', 'Map ID: ' + jQuery(this).find('input').attr('value'));
		});

	}

	// update order when we select a new overlay
	overlayParent.on('change', function () {
		iMapsBuilder.updateOrderOverlay();
	});

	// if there's an order
	var overlayOrder = document.querySelector('[data-depend-id=overlayOrder]');
	if (overlayOrder && overlayOrder.value !== '' && overlaySortable) {
		var orderValues = JSON.parse(overlayOrder.value);
		if (Array.isArray(orderValues)) {
			orderValues.reverse();
			orderValues.forEach(function (val) {
				var thisOrder = overlaySortable.find("input[value=" + val + "]").closest('li');
				if (thisOrder) {
					overlaySortable.prepend(thisOrder);
				}
			});
		}
	}

	// set sortable behaviour
	if (overlayParent) {
		overlaySortable.sortable({
			placeholder: "ui-state-highlight",
			stop: function (event, ui) {
				iMapsBuilder.previewBlocked = true;
				iMapsBuilder.blockPreview();
				iMapsBuilder.updateOrderOverlay();
			}
		});
		overlayParent.disableSelection();
	}
};

iMapsBuilder.updateOrderOverlay = function () {
	var overlayOrder = document.querySelector('[data-depend-id=overlayOrder]');
	var overlaySelected = document.querySelectorAll('input[data-depend-id=overlay]:checked');
	var order = [];

	if (overlaySelected) {
		overlaySelected.forEach(function (el) {
			order.push(el.value);
		});
	}
	order = JSON.stringify(order);
	overlayOrder.value = order;
};

iMapsBuilder.prepareAddMarkerActions = function () {

	var addMarkerButton = document.getElementById('map_click_add_marker');
	var addMarkerButtonIcon = addMarkerButton.querySelector('.dashicons');
	var addMarkerButtonLabel = addMarkerButton.querySelector('.map_action_label');

	// listen to clicked point event
	document.addEventListener('mapPointClicked', function (e) {
		// set coordinates in button
		addMarkerButton.setAttribute('data-latitude', e.detail.latitude);
		addMarkerButton.setAttribute('data-longitude', e.detail.longitude);
		addMarkerButtonIcon.classList.remove('dashicons-yes-alt');
		addMarkerButtonIcon.classList.add('dashicons-plus-alt');
		addMarkerButtonLabel.innerHTML = 'Add Marker Here';
	});

	addMarkerButton.addEventListener('click', function (e) {

		e.stopPropagation();

		var lat = addMarkerButton.getAttribute('data-latitude');
		var long = addMarkerButton.getAttribute('data-longitude');

		iMapsBuilder.addMarker(lat, long);

		addMarkerButtonIcon.classList.remove('dashicons-plus-alt');
		addMarkerButtonIcon.classList.add('dashicons-yes-alt');
		addMarkerButtonLabel.innerHTML = 'Marker Added';
	}, false);

};

iMapsBuilder.updateRegionsUsed = function () {

	var regionFields = document.querySelectorAll('.region-code-autocomplete');
	var result = [].map.call(regionFields, function (e) {
		return e.value;
	});

	iMapsBuilder.regionsUsed = result;
	return result;

};

iMapsBuilder.addPreviewImage = function () {
	var mapImage = document.getElementById("map_image_preview"),
		mapImageField = document.getElementsByName("map_image[mapImage]")[0];

	if (mapImageField && mapImageField.value !== "") {
		mapImage.innerHTML =
			'<img class="igm_map_image_preview" src="' + mapImageField.value + '">';
	}
};

iMapsBuilder.updatePreviewImage = function () {
	// set timeout to export png image
	var mapImage = document.getElementById("map_image_preview"),
		mapImageField = document.getElementsByName("map_image[mapImage]")[0],
		mapId = document.getElementById("post_ID").value;

	if (typeof mapImageField === 'undefined') {
		return;
	}

	if (iMaps.maps[mapId].map) {
		iMaps.maps[mapId].map.exporting.getImage("png").then(function (imgData) {
			mapImageField.value = imgData;
			mapImage.innerHTML =
				'<img class="igm_map_image_preview" src="' + imgData + '">';
		});
	}
};

iMapsBuilder.initAvailableRegions = function () {
	var data = iMapsData.data[0],
		regionCodes;

	if (data.disabled) {
		return;
	}

	if (data.map === "custom" || data.map.startsWith('http')) {
		fetch(data.mapURL)
			.then(function (res) {
				res.json().then(function (out) {
					regionCodes = iMapsBuilder.extractGeojson(out);
					iMapsBuilder.updateRegionsUsed();
					iMapsBuilder.populateAvailableRegions(regionCodes);

				});
			})
			.catch(function (err) {
				console.error(err);
			});
	} else {
		mapVar = iMapsRouter.getVarByName(data.map);

		if (typeof window[mapVar] !== "undefined") {
			regionCodes = iMapsBuilder.extractGeojson(window[mapVar]);
			iMapsBuilder.updateRegionsUsed();
			iMapsBuilder.populateAvailableRegions(regionCodes);
		} else {
			return;
		}
	}
};

iMapsBuilder.populateAvailableRegions = function (regionCodes) {
	var data = iMapsData.data[0],
		regionDataContainer = document.getElementsByName(
			"map_regions_info[regionData]"
		)[0],
		container = document.getElementById("map_region_data"),
		currentMap = document.getElementsByName("map_info[map]")[0]
			.selectedOptions[0].text,
		jsonContainer,
		containerContent,
		tree,
		regionData = {},
		populateButton,
		regionDataArray = [],
		mainContainer = document.getElementById("map_info"),
		html = document.createElement('div'),
		used = iMapsBuilder.regionsUsed;

	if (typeof regionCodes === 'undefined') {
		regionCodes = iMapsBuilder.regionCodes;
	} else {
		iMapsBuilder.regionCodes = regionCodes;
	}


	if (container) {

		var currentMapEl = document.createElement('p');
		currentMapEl.innerHTML = currentMap;
		html.appendChild(currentMapEl);

		// add button to autopopulate map
		/*
		populateButton = document.createElement("button");
		populateButton.innerHTML = "Populate Map with all regions";
		populateButton.setAttribute("class", "button button-secondary");
		populateButton.onclick = function (e) {
			e.preventDefault();

			Object.keys(regionCodes).forEach(function (region) {
				iMapsBuilder.addRegion(
					regionCodes[region].id,
					regionCodes[region].name
				);
			});

			// trigger form change event
			document
				.getElementsByName("map_info[map]")[0]
				.dispatchEvent(new Event("change"));
		};
		html.appendChild(populateButton);
		*/
		var regionCodeEl = document.createElement('code')
		regionCodeEl.innerHTML = iMapsOptions.messages.regionCode;
		html.appendChild(regionCodeEl);

		var regionNameEl = document.createElement('span');
		regionNameEl.innerHTML = iMapsOptions.messages.regionName + '<br> <small>' + iMapsOptions.messages.regionCodeInfo + '</small>';
		html.appendChild(regionNameEl);

		html.appendChild(document.createElement('br'));
		html.appendChild(document.createElement('br'));

		Object.keys(regionCodes)
			.sort()
			.forEach(function (region) {

				regionData[regionCodes[region].name] = region;

				regionDataArray.push({
					label:
						regionCodes[region].name + " " + "(" + region + ")",
					value: region
				});

				// if there's no name property, try NAME or id
				if (typeof regionCodes[region].name === 'undefined') {
					if (typeof regionCodes[region].NAME !== 'undefined') {
						regionCodes[region].name = regionCodes[region].NAME;
					} else {
						regionCodes[region].name = region;
					}

				}

				html.appendChild(iMapsBuilder.addRegionButton(region, regionCodes[region].name));

				var regionCodeId = document.createElement("code");
				regionCodeId.innerHTML = region;
				html.appendChild(regionCodeId);

				var regionNameLoop = document.createElement("span");
				regionNameLoop.innerHTML = regionCodes[region].name;
				html.appendChild(regionNameLoop);
				html.appendChild(document.createElement('br'));
			});

		var fullDataEl = document.createElement("p");
		fullDataEl.innerHTML = iMapsOptions.messages.fullData;
		html.appendChild(fullDataEl);

		containerContent = document.createElement("div");
		containerContent.appendChild(html);

		container.innerHTML = "";
		container.appendChild(containerContent);

		jsonContainer = document.createElement("DIV");
		container.appendChild(jsonContainer);

		// Create json-tree
		tree = jsonTree.create({ full: regionCodes }, jsonContainer);

		// set regionData to hidden field to be saved
		regionDataContainer.value = JSON.stringify(regionData);
		iMapsBuilder.regionData = regionDataArray;

		// set region code fields to autocomplete
		mainContainer.addEventListener("click", iMapsBuilder.clickEvents);

		// for the region autocomplete
		mainContainer.addEventListener(
			"focus",
			iMapsBuilder.eventRegionCodeAutocomplete,
			true
		);
	}
};

iMapsBuilder.addRegionButton = function (id, name) {

	var icon = document.createElement("span"),
		form;

	if (!iMapsBuilder.regionsUsed.includes(id.toString())) {
		icon.classList.add("igm_add_region_btn", "dashicons", "dashicons-plus-alt");
	} else {
		icon.classList.add("dashicons", "dashicons-yes-alt");
		return icon;
	}


	icon.setAttribute("title", iMapsOptions.messages.addToMap);

	icon.onclick = function (e) {
		e.preventDefault();

		iMapsBuilder.addRegion(id, name);

		// trigger form change event
		previewContainer = document.querySelector(".map_wrapper");

		if (!previewContainer) {
			return;
		}

		iMapsBuilder.needsUpdate = true;
		previewContainer.classList.add("map_updating");

		if (!iMapsBuilder.isInViewport(previewContainer)) {
			return;
		}

		form = iMapsBuilder.form;
		iMapsBuilder.updatePreview(form, false);
		return;
	};

	return icon;
};



iMapsBuilder.clickEvents = function (e) {
	var previewContainer;

	if (e.target && e.target.classList.contains("csf-cloneable-remove")) {
		previewContainer = document.querySelector(".map_wrapper");
		previewContainer.classList.add("map_updating");

		// update region list? Only when regions.. should be improved
		iMapsBuilder.updateRegionsUsed();
		iMapsBuilder.populateAvailableRegions();

		if (previewContainer && !iMapsBuilder.isInViewport(previewContainer)) {
			iMapsBuilder.needsUpdate = true;
			return;
		}
	}
};

iMapsBuilder.eventRegionCodeAutocomplete = function (e) {
	if (e.target && e.target.classList.contains("region-code-autocomplete")) {
		autocomplete({
			input: e.target,
			preventSubmit: true,
			fetch: function (text, update) {
				text = text.toLowerCase();
				// you can also use AJAX requests instead of preloaded data
				var suggestions = iMapsBuilder.regionData.filter(function (n) {
					return n.label.toLowerCase().includes(text);
				});
				update(suggestions);
			},
			onSelect: function (item) {
				e.target.value = item.value;
			}
		});
	}
};

iMapsBuilder.extractGeojson = function (geojson) {
	if (typeof geojson === "undefined") {
		return {
			error: iMapsOptions.messages.jsonError
		};
	}

	var extracted = {},
		features = geojson.features ? geojson.features : geojson;

	Object.keys(features).forEach(function (key) {
		extracted[features[key].id] = features[key].properties ? features[key].properties : features[key];
	});

	return extracted;
};



iMapsBuilder.buildPreview = function () {
	var previewContainer = document.getElementById("map_preview"),
		form = iMapsBuilder.form,
		mapId,
		mapContainer,
		mapData,
		onChangeFunction,
		inputsSelector,
		inputs,
		switchInputs;

	window.addEventListener(
		"scroll",
		function (e) {
			if (iMapsBuilder.isInViewport(previewContainer)) {
				if (iMapsBuilder.needsUpdate) {
					iMapsBuilder.updatePreview(form, false);
				}
			}
		},
		false
	);

	// set listener for input fields of form
	onChangeFunction = iMapsBuilder.eventThrottle(function (e) {
		// create exceptions
		if (
			e.target.classList.contains("chosen-search-input") ||
			e.target.classList.contains("skip-preview") ||
			e.target.name == "map_info[description]"
		) {
			return;
		}
		//form = document.querySelector("form#post"),
		iMapsBuilder.updatePreview(form, e);
	}, 3000);

	//form.addEventListener("change", onChangeFunction);
	inputsSelector = "#map_info input, #map_info select, #map_info textarea";
	inputs = document.querySelectorAll(inputsSelector);

	inputs.forEach(function (input) {
		input.onchange = onChangeFunction;
	});

	// when a clone button is clicked, we remove event listener from form and add it again to include new inputs
	document.querySelectorAll(".csf-cloneable-add").forEach(function (button) {
		button.addEventListener("click", function (e) {
			inputs.forEach(function (input) {
				input.onchange = null;
			});
			setTimeout(function () {
				inputs = document.querySelectorAll(inputsSelector);
				inputs.forEach(function (input) {
					input.onchange = onChangeFunction;
				});
			}, 500);
		});
	});

	// onclick events to trigger change on some fields
	switchInputs = document.querySelectorAll("#map_info .csf--switcher");
	switchInputs.forEach(function (inp) {
		// inp.addEventListener("click", onChangeFunction);
	});

	if (previewContainer) {
		mapContainer = previewContainer.querySelector(".map_render");
		mapId = document.getElementById("post_ID").value;
		mapContainer.id = "map_" + mapId;

		// trigger map preview if first time
		if (iMapsData.data[0].disabled) {
			document
				.getElementsByName("map_info[map]")[0]
				.dispatchEvent(new Event("change"));
		}
	}
};

iMapsBuilder.restartMap = function () {

	var data;
	var previewContainer = document.querySelector(".map_wrapper");

	if (iMaps.maps) {
		Object.keys(iMaps.maps).forEach(function (id) {
			iMaps.maps[id].map.dispose();
		});

		data = iMapsModel.prepareData(iMapsData.data);
		data.forEach(function (tdata, index) {
			if (tdata.disabled) {
				return;
			}
			iMapsManager.init(index);
		});
		iMapsBuilder.needsUpdate = false;
	}

	previewContainer.classList.remove("map_updating");
};

iMapsBuilder.updatePreview = function (form, ev) {
	var formData = new FormData(form),
		mapContainer,
		mapData,
		mapWrapper,
		paddingTop,
		maxWidth,
		baseRegionSeries,
		previewContainer,
		map = false;

	// if we're just scrolling and we don't need to update
	if (!ev && !iMapsBuilder.needsUpdate) {
		return;
	}

	// if the preview is blocked - because of overlay preview
	if (iMapsBuilder.previewBlocked) {
		return;
	}

	// if the change event was not from the map_info collection
	if (ev && !ev.target.name.includes("map_info")) {
		return;
	}

	if (iMaps.maps) {
		Object.keys(iMaps.maps).forEach(function (id) {
			mapData = iMaps.maps[id];
			map = iMaps.maps[id].map;
			baseRegionSeries = iMaps.maps[id].baseRegionSeries;
		});
	}

	// if we only changed visual stuff
	if (
		ev &&
		(ev.target.name == "map_info[visual][paddingTop]" ||
			ev.target.name == "map_info[visual][maxWidth]")
	) {
		maxWidth = document.getElementsByName("map_info[visual][maxWidth]")[0];
		paddingTop = document.getElementsByName("map_info[visual][paddingTop]")[0];
		mapWrapper = document.querySelector(".map_wrapper .map_box");
		mapContainer = document.querySelector(".map_aspect_ratio");
		mapContainer.style.paddingTop = String(paddingTop.value) + "%";

		if (maxWidth !== "" && maxWidth !== "0") {
			mapWrapper.style.maxWidth = String(maxWidth.value) + "px";
		}

		//iMapsBuilder.updatePreviewImage();
		return;
	}
	// change fontFamily
	if (ev && ev.target.name == "map_info[visual][fontFamily]") {
		mapWrapper = document.querySelector(".map_wrapper .map_box");
		mapWrapper.style.fontFamily = ev.target.value;
		return;
	}

	// change projections
	/*
	if (ev && ev.target.name == "map_info[projection]" && map) {

		if ("Orthographic" !== ev.target.value && ev.target.value !== "Albers" ) {
			map.projection = new am4maps.projections[ev.target.value]();
			console.log(ev.target.value);
			//iMapsBuilder.updatePreviewImage();
			return;
		}
	}*/

	// background color
	if (ev && ev.target.name == "map_info[visual][backgroundColor]" && map) {
		if (ev.target.value === "transparent") {
			map.background.fill = am4core.color("#f00", 0);
		} else {
			map.background.fill = am4core.color(ev.target.value);
		}
		//iMapsBuilder.updatePreviewImage();
		return;
	}

	// borders width
	if (ev && ev.target.name == "map_info[visual][borderWidth]" && map) {
		baseRegionSeries.mapPolygons.each(function (polygon) {
			polygon.animate(
				{
					property: "strokeWidth",
					to: ev.target.value
				},
				250
			);
		});
		//iMapsBuilder.updatePreviewImage();
		return;
	}

	// inactive color
	if (ev && ev.target.name == "map_info[visual][inactiveColor]" && map) {
		baseRegionSeries.mapPolygons.each(function (polygon) {
			if (polygon.dataItem.dataContext.madeFromGeoData) {
				return;
			}

			polygon.animate(
				{
					property: "inactiveColor",
					to: ev.target.value
				},
				250
			);
		});
		//iMapsBuilder.updatePreviewImage();
		return;
	}

	// border color
	if (ev && ev.target.name == "map_info[visual][borderColor]" && map) {
		baseRegionSeries.mapPolygons.each(function (polygon) {
			polygon.animate(
				{
					property: "stroke",
					to: am4core.color(ev.target.value)
				},
				250
			);
		});
		//iMapsBuilder.updatePreviewImage();
		return;
	}

	if (ev && ev.target.name == "map_info[overlay][]" && map) {

		iMapsBuilder.previewBlocked = true;
		iMapsBuilder.blockPreview();
		//iMapsBuilder.updatePreviewImage();
		return;
	}

	// if map changes and is set to 'custom' maybe we block it to make sure everything else works?
	if (ev && ev.target.name == "map_info[map]" && ev.target.value === 'custom' && ev.target.value === '' && map) {

		iMapsBuilder.previewBlocked = true;
		iMapsBuilder.blockPreview();
		return;
	}

	if (ev && ev.target.name == "map_info[mapURL]" && ev.target.value !== '' && map) {

		iMapsBuilder.previewBlocked = true;
		iMapsBuilder.blockPreview();
		return;
	}


	if (iMapsOptions.preview && iMapsOptions.preview !== '1') {
		iMapsBuilder.previewBlocked = true;
		iMapsBuilder.blockPreview();
		//iMapsBuilder.updatePreviewImage();
		return;
	}

	// to block preview after custom js change
	if (ev && ev.target.name == "map_info[custom_js]" && map) {

		iMapsBuilder.previewBlocked = true;
		iMapsBuilder.blockPreview();
		//iMapsBuilder.updatePreviewImage();
		return;
	}


	// if preview container is not visible, only update when it becomes visible
	// but only if the change was not in the map option
	previewContainer = document.querySelector(".map_wrapper");
	if (
		ev &&
		ev.target.name != "map_info[map]" &&
		previewContainer &&
		!iMapsBuilder.isInViewport(previewContainer)
	) {
		previewContainer.classList.add("map_updating");
		iMapsBuilder.needsUpdate = true;

		return;
	}

	formData.append("action", "map_form_data");
	formData.append("security", iMapsOptions.ajax_nonce);

	previewContainer.classList.add("map_updating");

	jQuery.ajaxSetup({
		async: true
	});

	console.log( formData );

	jQuery.ajax({
		url: iMapsOptions.ajax_url,
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,
		async: false,
		success: function (response) {

			var decode = [JSON.parse(response)];

			//handle entries with json that might lost formatting
			if (typeof decode[0].regionLabels === 'object' && decode[0].regionLabels.hasOwnProperty('regionLabelCustomCoordinates')) {
				decode[0].regionLabels.regionLabelCustomCoordinates = decode[0].regionLabels.regionLabelCustomCoordinates.replace(/\\/g, '');
			}

			iMapsData.data = decode;

			// merge with global options
			if (typeof iMapsOptions !== 'undefined') {
				iMapsData.data[0] = Object.assign(iMapsData.data[0], iMapsOptions);
			}

			if (ev && ev.target.name === "map_info[map]") {
				// we changed map, so we need to attach it to the page
				iMapsBuilder.addGeoFileSeries(
					decode[0].mapURL,
					iMapsBuilder.restartMap
				);
			} else {
				// rebuild map
				iMapsBuilder.restartMap();
				//iMapsBuilder.checkMaxInputVars();
				console.log("map updated");
			}

			//iMapsBuilder.updatePreviewImage();
		},
		error: function (error) {
			console.log(error);
		}
	});

	// check number of inputs
	// iMapsBuilder.checkMaxInputVars();
};

iMapsBuilder.blockPreview = function () {
	previewContainer = document.querySelector(".map_wrapper .map_blocked_preview");
	previewContainer.classList.remove("map_block_hidden");
	iMapsBuilder.needsUpdate = false;
	return;
};

/**
 * Adds a new script to the page
 *
 * @return false
 */
iMapsBuilder.addGeoFileSeries = function (src, clback) {
	if (!src) {
		return false;
	}

	var scriptPromise = new Promise(function (resolve, reject) {
		var script = document.createElement("script");
		document.body.appendChild(script);
		script.onload = resolve;
		script.onerror = reject;
		script.async = true;
		script.src = src;
	});

	scriptPromise.then(function () {
		clback();
		iMapsBuilder.initAvailableRegions();
	});

	return true;
};

iMapsBuilder.eventGeocoding = function (e) {
	// e.target is the clicked element!
	// If it was a list item
	if (e.target && e.target.classList.contains("geocoding-input")) {
		iMapsBuilder.geocoder = new google.maps.Geocoder();
		iMapsBuilder.searchInput = e.target;

		// key enter press
		iMapsBuilder.searchInput.addEventListener("keypress", function (e) {
			var key = e.which || e.keyCode;
			if (key === 13) {
				e.preventDefault();
				iMapsBuilder.geocodeAddress();
			}
		});

		// blur (remove focus) event
		iMapsBuilder.searchInput.addEventListener("blur", function (e) {
			iMapsBuilder.geocodeAddress();
		});
	}
};

iMapsBuilder.initGeocoding = function () {
	// Get the element, add a click listener...
	var container = document.getElementById("map_info");
	container.addEventListener("click", iMapsBuilder.eventGeocoding);
	container.addEventListener("focus", iMapsBuilder.eventGeocoding, true);
};

iMapsBuilder.eventGoogleAutocomplete = function (e) {
	// e.target is the clicked element!
	// If it was a list item
	if (e.target && e.target.classList.contains("geocoding-input")) {
		// Create the autocomplete object, restricting the search predictions to
		// geographical location types.
		iMapsBuilder.autocomplete = new google.maps.places.Autocomplete(e.target, {
			types: ["geocode"]
		});

		iMapsBuilder.searchInput = e.target;

		// Avoid paying for data that you don't need by restricting the set of
		// place fields that are returned to just the address components.
		iMapsBuilder.autocomplete.setFields(["geometry"]);

		// When the user selects an address from the drop-down, populate the
		// address fields in the form.
		iMapsBuilder.autocomplete.addListener(
			"place_changed",
			iMapsBuilder.fillInAddress
		);
	}
};

iMapsBuilder.initGoogleAutocomplete = function () {
	// Get the element, add a click listener...
	var container = document.getElementById("map_info");
	container.addEventListener("click", iMapsBuilder.eventGoogleAutocomplete);
	container.addEventListener(
		"focus",
		iMapsBuilder.eventGoogleAutocomplete,
		true
	);

	return;
};

iMapsBuilder.fillInAddress = function (i) {
	var autocomplete = iMapsBuilder.autocomplete;
	var input = iMapsBuilder.searchInput;
	var parent = iMapsBuilder.getClosest(input, ".csf-fieldset-content");
	var changeEvent = new Event("change");

	console.log(iMapsBuilder.autocomplete);

	// Get the place details from the autocomplete object.
	var place = autocomplete.getPlace();
	var latfield = parent.querySelector('[data-depend-id="latitude"]');
	var lonfield = parent.querySelector('[data-depend-id="longitude"]');

	if (place) {
		latfield.value = place.geometry.location.lat();
		lonfield.value = place.geometry.location.lng();
		latfield.dispatchEvent(changeEvent);
	}

};

iMapsBuilder.geocodeAddress = function () {
	var addressInput = iMapsBuilder.searchInput,
		address = addressInput.value,
		geocoder = iMapsBuilder.geocoder,
		parent = iMapsBuilder.getClosest(
			iMapsBuilder.searchInput,
			".csf-fieldset-content"
		),
		latfield = parent.querySelector('[data-depend-id="latitude"]'),
		lonfield = parent.querySelector('[data-depend-id="longitude"]'),
		place,
		changeEvent = new Event("change");

	addressInput.setAttribute("readonly", "readyonly");

	geocoder.geocode({ address: address }, function (results, status) {
		if (status === "OK") {
			place = results[0];
			latfield.value = place.geometry.location.lat();
			lonfield.value = place.geometry.location.lng();
			latfield.dispatchEvent(changeEvent);
		} else {
			console.log("geocode error: " + status);
		}

		addressInput.removeAttribute("readonly");
	});
};

iMapsBuilder.getClosest = function (elem, selector) {
	// Element.matches() polyfill
	if (!Element.prototype.matches) {
		Element.prototype.matches =
			Element.prototype.matchesSelector ||
			Element.prototype.mozMatchesSelector ||
			Element.prototype.msMatchesSelector ||
			Element.prototype.oMatchesSelector ||
			Element.prototype.webkitMatchesSelector ||
			function (s) {
				var matches = (this.document || this.ownerDocument).querySelectorAll(s),
					i = matches.length;
				while (--i >= 0 && matches.item(i) !== this) { }
				return i > -1;
			};
	}

	// Get the closest matching element
	for (; elem && elem !== document; elem = elem.parentNode) {
		if (elem.matches(selector)) return elem;
	}
	return null;
};

iMapsBuilder.swap = function (json) {
	var ret = {};
	for (var key in json) {
		ret[json[key]] = key;
	}
	return ret;
};

iMapsBuilder.eventThrottle = function (fn, threshhold, scope) {
	threshhold = typeof threshhold !== "undefined" ? threshhold : 250;
	var last, deferTimer;
	return function () {
		var context = scope || this;

		var now = +new Date(),
			args = arguments;
		if (last && now < last + threshhold) {
			// hold on to it
			clearTimeout(deferTimer);
			deferTimer = setTimeout(function () {
				last = now;
				fn.apply(context, args);
			}, threshhold);
		} else {
			last = now;
			fn.apply(context, args);
		}
	};
};

iMapsBuilder.addRegion = function (regionCode, regionName) {
	var newEntry,
		titleField,
		regionCodeField,
		tooltipField,
		regionCollection,
		regionCheck = false,
		regionTab,
		addButton = document.querySelector(".regions_tab .csf-cloneable-add");

	regionTab = document.querySelector("#map_info .csf-nav-metabox ul li:nth-of-type(3)");
	if (!regionTab.classList.contains("csf-section-active")) {
		regionTab.querySelector('a').click();
	}

	// check if region already exists
	regionCollection = document.querySelector(".region-code-autocomplete");

	if (regionCollection.length > 0) {
		regionCollection.forEach(function (field) {
			if (field.value === regionCode) {
				regionCheck = true;
			}
		});
	}

	// if it doesn't exist yet
	if (!regionCheck) {
		addButton.click();

		newEntry = document.querySelector(
			'div[data-field-id="[regions]"] .csf-cloneable-item:last-of-type'
		);

		// set title
		titleField = newEntry.querySelector('[data-depend-id="name"]');
		titleField.value = regionName;
		titleField.dispatchEvent(new Event("keyup"));

		// set regionCode
		regionCodeField = newEntry.querySelector('[data-depend-id="id"]');
		regionCodeField.value = regionCode;

		// set Tooltip
		tooltipField = newEntry.querySelector('[data-depend-id="tooltipContent"]');
		tooltipField.value = regionName;
	}

	iMapsBuilder.updateRegionsUsed();
	iMapsBuilder.populateAvailableRegions();
};

iMapsBuilder.hitTab = function(index){
	setTimeout(function(){
		let tab = document.querySelector('#map_info .csf-nav ul li:nth-child(' + index + ') a');
		if( tab ) {
			tab.click();
		}
	},1000);
}

iMapsBuilder.rememberTabInit = function() {

	// check if URL contains tab parameter
	let referer    = document.querySelector('#referredby'); 
	let refererUrl = new URL( window.location.origin + referer.value );

    if( typeof refererUrl.origin === 'undefined' ) {
        return;
    }

	if (refererUrl.searchParams.get('igmtab')) {
		iMapsBuilder.hitTab( refererUrl.searchParams.get('igmtab') );
	} else {
		let currentUrl = new URL (window.location.href );
		if (currentUrl.searchParams.get('igmtab')) {
			iMapsBuilder.hitTab( currentUrl.searchParams.get('igmtab') );
		}
	}

	let tabs          = document.querySelectorAll("#map_info .csf-nav-metabox ul li");
	let currentURL    = window.location.href;
	let url           = new URL( currentURL );
	let search_params = url.searchParams;

	tabs.forEach(function(tab,index){
		tab.addEventListener('click',function(){
			search_params.set('igmtab', index+1);
			const nextTitle = document.title;
			const nextState = { additionalInformation: 'Updated the URL with JS' };
			window.history.replaceState(nextState, nextTitle, url.toString());
		});
	});
}


iMapsBuilder.addMarker = function (latitude, longitude) {
	var newEntry,
		titleField,
		latitudeField,
		longitudeField,
		tooltipField,
		markersTab,
		form,
		previewContainer = document.querySelector(".map_wrapper"),
		addButton = document.querySelector(".markers_tab .csf-cloneable-add");

	markersTab = document.querySelector("#map_info .csf-nav-metabox ul li:nth-of-type(4)");

	if (!markersTab.classList.contains("csf-section-active")) {
		markersTab.querySelector('a').click();
	}

	addButton.click();

	newEntry = document.querySelector(
		'div[data-field-id="[roundMarkers]"] .csf-cloneable-item:last-of-type'
	);

	if (newEntry) {

		// set title
		titleField = newEntry.querySelector('[data-depend-id="id"]');

		thisID = titleField.getAttribute('name');
		thisID = thisID.replace('map_info[roundMarkers]', '');
		thisID = thisID.replace('[id]', '');

		titleField.value = "Custom Marker " + thisID;
		titleField.dispatchEvent(new Event("keyup"));

		// set Tooltip
		tooltipField = newEntry.querySelector('[data-depend-id="tooltipContent"]');
		tooltipField.value = "Custom Marker " + thisID;

		// set lat and long
		latitudeField = newEntry.querySelector('[data-depend-id="latitude"]');
		longitudeField = newEntry.querySelector('[data-depend-id="longitude"]');

		if (latitudeField && longitudeField) {
			latitudeField.value = latitude;
			longitudeField.value = longitude;
		}
		// probably using openstreetmap
		else {
			latitudeField = newEntry.querySelector('.csf--latitude');
			longitudeField = newEntry.querySelector('.csf--longitude');
			latitudeField.value = latitude;
			longitudeField.value = longitude;
			latitudeField.dispatchEvent(new Event("change"));
		}

		iMapsBuilder.needsUpdate = true;
		previewContainer.classList.add("map_updating");

		if (!iMapsBuilder.isInViewport(previewContainer)) {
			return;
		}

		form = iMapsBuilder.form;
		iMapsBuilder.updatePreview(form, false);
		return;

	}

};

/*!
 * Determine if an element is in the viewport
 * (c) 2017 Chris Ferdinandi, MIT License, https://gomakethings.com
 * @param  {Node}    elem The element
 * @return {Boolean}      Returns true if element is in the viewport
 */
iMapsBuilder.isInViewport = function (elem) {
	var distance = elem.getBoundingClientRect();
	return (
		distance.bottom >= 250 &&
		distance.bottom <=
		(window.innerHeight || document.documentElement.clientHeight)
	);
};

// code for geolocate metabox
iMapsBuilder.geoLocateInit = function () {

	// fix map field not rendering properly when initially hidden
	let trigger = document.querySelector('[data-depend-id="enabled"]').closest('.csf--switcher');
	trigger.addEventListener('click', function () {
		window.dispatchEvent(new Event('resize'));
	});
};

// if we have the options available, we're in the edit map screen
if (typeof iMapsOptions !== "undefined") {
	iMapsBuilder.init();
} else {
	iMapsBuilder.geoLocateInit();
}



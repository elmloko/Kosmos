"use strict";

function _typeof(obj) {
  "@babel/helpers - typeof";
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }
  return _typeof(obj);
}

/**
 * Geocluster function. Original:
 * https://github.com/yetzt/node-geocluster
 * and adapted to work with iMaps
 */
function geocluster(elements, bias, defaults, tooltipTemplate) {
  bias = parseFloat(bias);
  if (!(this instanceof geocluster)) return new geocluster(elements, bias, defaults, tooltipTemplate);
  return this._cluster(elements, bias, defaults, tooltipTemplate);
} // degrees to radians


geocluster.prototype._toRad = function (number) {
  return number * Math.PI / 180;
}; // geodetic distance approximation


geocluster.prototype._dist = function (lat1, lon1, lat2, lon2) {
  var dlat = this._toRad(lat2 - lat1);

  var dlon = this._toRad(lon2 - lon1);

  var a = Math.sin(dlat / 2) * Math.sin(dlat / 2) + Math.sin(dlon / 2) * Math.sin(dlon / 2) * Math.cos(this._toRad(lat1)) * Math.cos(this._toRad(lat2));
  return Math.round(2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a)) * 6371 * 100) / 100;
};

geocluster.prototype._centroid = function (set) {
  var cetroidObj = Object.values(set).reduce(function (accumulator, currentValue) {
    return {
      latitude: accumulator.latitude + currentValue.latitude,
      longitude: accumulator.longitude + currentValue.longitude
    };
  }, {
    latitude: 0,
    longitude: 0
  });
  cetroidObj = Object.values(cetroidObj).map(function (e) {
    return e / Object.keys(set).length;
  });
  return cetroidObj;
};

geocluster.prototype._clean = function (data) {
  return data.map(function (cluster) {
    return [cluster.centroid, cluster.elements];
  });
};

geocluster.prototype._cluster = function (elements, bias, defaults, tooltipTemplate) {
  var self = this,
    cluster_map_collection = [];
  var tempMarker; // set bias to 1 on default

  if (typeof bias !== "number" || isNaN(bias)) bias = 1;
  var tot_diff = 0;
  var diffs = [];
  var diff; // calculate sum of differences

  for (var i = 1; i < elements.length; i++) {
    diff = self._dist(elements[i].latitude, elements[i].longitude, elements[i - 1].latitude, elements[i - 1].longitude);
    tot_diff += diff;
    diffs.push(diff);
  } 
  
  // calculate mean diff
  var mean_diff = tot_diff / diffs.length;
  var diff_variance = 0; 
  
  // calculate variance total
  diffs.forEach(function (diff) {
    diff_variance += Math.pow(diff - mean_diff, 2);
  }); 
  
  // derive threshold from stdev and bias - modified to allow bias to be more decisive
  var diff_stdev = Math.sqrt(diff_variance / diffs.length);
  var threshold = 10000 * bias;
  var cluster_map = []; 
  
  // generate random initial cluster map
  cluster_map.push({
    centroid: elements[Math.floor(0.5 * elements.length)],
    elements: [],
    fill: defaults.fill,
    hover: defaults.hover,
    radius: defaults.radius
  }); 
  
  // loop elements and distribute them to clusters
  var changing = true;

  while (changing === true) {
    var new_cluster = false;
    var cluster_changed = false; // iterate over elements

    elements.forEach(function (e, ei) {
      var closest_dist = Infinity;
      var closest_cluster = null; // find closest cluster

      cluster_map.forEach(function (cluster, ci) {
        // distance to cluster
        var dist = self._dist(e.latitude, e.longitude, cluster_map[ci].centroid.latitude, cluster_map[ci].centroid.longitude);

        if (dist < closest_dist) {
          closest_dist = dist;
          closest_cluster = ci;
        }
      }); // is the closest distance smaller than the stddev of elements?

      if (closest_dist < threshold || closest_dist === 0) {
        // put element into existing cluster
        cluster_map[closest_cluster].elements.push(e);
      } else {
        // create a new cluster with this element
        cluster_map.push({
          centroid: e,
          elements: [e]
        });
        new_cluster = true;
      }
    }); // delete empty clusters from cluster_map

    cluster_map = cluster_map.filter(function (cluster) {
      return cluster.elements.length > 0;
    }); // calculate the clusters centroids and check for change

    cluster_map.forEach(function (cluster, ci) {
      var centroid = self._centroid(cluster.elements);

      changing = false;

      if (centroid.latitude !== cluster.centroid.latitude || centroid.longitude !== cluster.centroid.longitude) {
        cluster_map[ci].centroid = centroid;
        cluster_changed = true;
      }
    }); // loop cycle if clusters have changed

    if (!cluster_changed && !new_cluster) {
      changing = false;
    } else {
      // remove all elements from clusters and run again
      if (changing) cluster_map = cluster_map.map(function (cluster) {
        cluster.elements = [];
        return cluster;
      });
    }
  }

  cluster_map = cluster_map.map(function (cluster) {
    if (cluster.elements.length === 1) {
      cluster_map_collection.push(cluster.elements[0]);
    } else {
      tempMarker = {
        id: "",
        label: cluster.elements.length,
        name: "",
        value: cluster.elements.length,
        cluster: true,
        latitude: cluster.centroid[0],
        longitude: cluster.centroid[1],
        elements: cluster.elements,
        content: "",
        fill: defaults.fill,
        hover: defaults.hover,
        radius: defaults.radius,
        action: ""
      }; // in case we have a custom tooltipTemplate

      if (tooltipTemplate) {
        tempMarker.tooltipTemplate = tooltipTemplate;
      }

      cluster_map_collection.push(tempMarker);
    }
  }); // compress result

  return cluster_map_collection;
};
/* ROUTER */


var iMapsRouter = {};

iMapsRouter.getGeoFiles = function (regionClicked) {
  var regionID = regionClicked.id.toString(),
    regionName = regionClicked.name,
    urlappend,
    varappend,
    geoFiles = {},
    continents = ["southAmerica", "northAmerica", "europe", "middleEast", "asia", "oceania", "africa", "antarctica"]; // continents

  if (continents.includes(regionID)) {
    urlappend = "region/world/";
    varappend = "_region_world_";
  } // us maps
  else if (regionID.includes("US-")) {
    urlappend = "region/usa/";
    varappend = "_region_usa_";
    regionID = regionID.substr(-2).toLowerCase();
  } else {
    urlappend = "";
    varappend = "_";
    regionID = regionName.toLowerCase().replace("united states", "usa");
  }

  geoFiles.src = "https://cdn.amcharts.com/lib/4/geodata/" + urlappend + regionID + "Low.js";
  geoFiles.map = "am4geodata" + varappend + regionID + "Low";
  geoFiles.title = regionName;
  return geoFiles;
};

iMapsRouter.getAllSrc = function () {
  var sources = {},
    paths = {
      main: "../../vendor/geodata",
      usa: "../../vendor/geodata/region/usa",
      world: "../../vendor/geodata/region/world"
    };

  var fs = require("fs");

  Object.keys(paths).forEach(function (item) {
    sources[item] = [];
    fs.readdir(paths[item], function (err, files) {
      files.forEach(function (file) {
        sources[item].push(file);
      });
    });
  });
  return sources;
};
/**
 * get map am4charts variable name based on name value
 */


iMapsRouter.getVarByName = function (variable) {
  if (typeof variable === "undefined") {
    return "am4geodata_worldLow";
  } else if (variable.includes("custom")) {
    return variable;
  }

  variable = variable.replace(/\//g, "_");
  return "am4geodata_" + variable;
};

iMapsRouter.getCleanMapName = function (mapName, id) {
  if (typeof mapName === "undefined") {
    return false;
  }

  mapName = mapName.replace("Low", "");
  mapName = mapName.replace("High", "");

  if (mapName === 'custom') {
    mapName += id;
  }

  return mapName;
};

iMapsRouter.iso2cleanName = function (iso, mapID) {
  var countries = iMapsRouter.getCountries();
  var continents = ['africa', 'antarctiva', 'asia', 'europe','middleEast', 'northAmerica', 'oceania', 'southAmerica', 'centralAmerica'];
  var tempIso;
  var series = iMapsManager.maps[mapID].seriesIndex;
  var match = false;
  console.log('ISO: ' + iso); // exceptions

  if ("VA" === iso) {
    return 'vatican';
  } else if ("US" === iso) {
    return "usa";
  } else if (iso.includes("CA-")) {
    return "region/canada/" + iso.replace("CA-", "").toLowerCase();
  } else if (iso.includes("MX-")) {
    return "region/mexico/" + iso.replace("MX-", "").toLowerCase();
  } else if (continents.includes(iso)) {
    return "region/world/" + iso;
  } else if (iso.includes("US-")) {
    tempIso = ["region/usa/" + iso.replace("US-", "").toLowerCase(), "region/usa/congressional/" + iso.replace("US-", "").toLowerCase(), "region/usa/congressional2022/" + iso.replace("US-", "").toLowerCase()];
  } else if ("GB" === iso) {
    tempIso = ['uk', 'ukCountries', 'ukCounties'];
  } else if ("BA" === iso) {
    tempIso = ['bosniaHerzegovinaCantons', 'bosniaHerzegovina'];
  } else if ("IL" === iso) {
    tempIso = ['israel', 'israelPalestine'];
  } else if ("SI" === iso) {
    tempIso = ['sloveniaRegions', 'slovenia'];
  } else if ("IN" === iso) {
    tempIso = ['india2019', 'india', 'india2020'];
  } else if ("AE" === iso) {
    tempIso = ['unitedArabEmirates', 'uae'];
  } else if ("FR" === iso) {
    tempIso = ['franceDepartments', 'france'];
  } else if ("MG" === iso) {
    tempIso = ['madagascarProvince', 'madagascarRegion'];
  } else if ("PT" === iso) {
    tempIso = ['portugal', 'portugalRegions'];
  } else if ("RS" === iso) {
    tempIso = ['serbia', 'serbiaNoKosovo'];
  } else if ("CD" === iso) {
    tempIso = ['congoDR'];
  } else if ("CG" === iso) {
    tempIso = ['congo'];
  } else if ("CZ" === iso) {
    tempIso = ['czechRepublic','czechia'];
  } else if ("MM" === iso) {
    tempIso = ['myanmar'];
  } // the rest

  // this block needs reviewing.. if/else are a mess
  if (Array.isArray(tempIso)) {
    tempIso.forEach(function (item, index) {
      if (series.hasOwnProperty(item)) {
        match = item;
      } 
    });

    if (match) {
      return match;
    } else {
        if (countries.hasOwnProperty(iso)) {
            return iMapsRouter.camelize(countries[iso]);
          }
    }
  } else {
    if (countries.hasOwnProperty(iso)) {
      return iMapsRouter.camelize(countries[iso]);
    }
  }
  return false;
};

iMapsRouter.getCountries = function () {
  var rawjson = "{\"AF\":\"Afghanistan\",\"AX\":\"\xC5land Islands\",\"AL\":\"Albania\",\"DZ\":\"Algeria\",\"AS\":\"American Samoa\",\"AD\":\"Andorra\",\"AO\":\"Angola\",\"AI\":\"Anguilla\",\"AQ\":\"Antarctica\",\"AG\":\"Antigua & Barbuda\",\"AR\":\"Argentina\",\"AM\":\"Armenia\",\"AW\":\"Aruba\",\"AC\":\"Ascension Island\",\"AU\":\"Australia\",\"AT\":\"Austria\",\"AZ\":\"Azerbaijan\",\"BS\":\"Bahamas\",\"BH\":\"Bahrain\",\"BD\":\"Bangladesh\",\"BB\":\"Barbados\",\"BY\":\"Belarus\",\"BE\":\"Belgium\",\"BZ\":\"Belize\",\"BJ\":\"Benin\",\"BM\":\"Bermuda\",\"BT\":\"Bhutan\",\"BO\":\"Bolivia\",\"BA\":\"Bosnia & Herzegovina\",\"BW\":\"Botswana\",\"BR\":\"Brazil\",\"IO\":\"British Indian Ocean Territory\",\"VG\":\"British Virgin Islands\",\"BN\":\"Brunei\",\"BG\":\"Bulgaria\",\"BF\":\"Burkina Faso\",\"BI\":\"Burundi\",\"KH\":\"Cambodia\",\"CM\":\"Cameroon\",\"CA\":\"Canada\",\"IC\":\"Canary Islands\",\"CV\":\"Cape Verde\",\"BQ\":\"Caribbean Netherlands\",\"KY\":\"Cayman Islands\",\"CF\":\"Central African Republic\",\"EA\":\"Ceuta & Melilla\",\"TD\":\"Chad\",\"CL\":\"Chile\",\"CN\":\"China\",\"CX\":\"Christmas Island\",\"CC\":\"Cocos (Keeling) Islands\",\"CO\":\"Colombia\",\"KM\":\"Comoros\",\"CG\":\"Congo - Brazzaville\",\"CD\":\"Congo - Kinshasa\",\"CK\":\"Cook Islands\",\"CR\":\"Costa Rica\",\"CI\":\"C\xF4te d\u2019Ivoire\",\"HR\":\"Croatia\",\"CU\":\"Cuba\",\"CW\":\"Cura\xE7ao\",\"CY\":\"Cyprus\",\"CZ\":\"Czechia\",\"DK\":\"Denmark\",\"DG\":\"Diego Garcia\",\"DJ\":\"Djibouti\",\"DM\":\"Dominica\",\"DO\":\"Dominican Republic\",\"EC\":\"Ecuador\",\"EG\":\"Egypt\",\"SV\":\"El Salvador\",\"GQ\":\"Equatorial Guinea\",\"ER\":\"Eritrea\",\"EE\":\"Estonia\",\"SZ\":\"Eswatini\",\"ET\":\"Ethiopia\",\"FK\":\"Falkland Islands\",\"FO\":\"Faroe Islands\",\"FJ\":\"Fiji\",\"FI\":\"Finland\",\"FR\":\"France\",\"GF\":\"French Guiana\",\"PF\":\"French Polynesia\",\"TF\":\"French Southern Territories\",\"GA\":\"Gabon\",\"GM\":\"Gambia\",\"GE\":\"Georgia\",\"DE\":\"Germany\",\"GH\":\"Ghana\",\"GI\":\"Gibraltar\",\"GR\":\"Greece\",\"GL\":\"Greenland\",\"GD\":\"Grenada\",\"GP\":\"Guadeloupe\",\"GU\":\"Guam\",\"GT\":\"Guatemala\",\"GG\":\"Guernsey\",\"GN\":\"Guinea\",\"GW\":\"Guinea-Bissau\",\"GY\":\"Guyana\",\"HT\":\"Haiti\",\"HN\":\"Honduras\",\"HK\":\"Hong Kong SAR China\",\"HU\":\"Hungary\",\"IS\":\"Iceland\",\"IN\":\"India\",\"ID\":\"Indonesia\",\"IR\":\"Iran\",\"IQ\":\"Iraq\",\"IE\":\"Ireland\",\"IM\":\"Isle of Man\",\"IL\":\"Israel\",\"IT\":\"Italy\",\"JM\":\"Jamaica\",\"JP\":\"Japan\",\"JE\":\"Jersey\",\"JO\":\"Jordan\",\"KZ\":\"Kazakhstan\",\"KE\":\"Kenya\",\"KI\":\"Kiribati\",\"XK\":\"Kosovo\",\"KW\":\"Kuwait\",\"KG\":\"Kyrgyzstan\",\"LA\":\"Laos\",\"LV\":\"Latvia\",\"LB\":\"Lebanon\",\"LS\":\"Lesotho\",\"LR\":\"Liberia\",\"LY\":\"Libya\",\"LI\":\"Liechtenstein\",\"LT\":\"Lithuania\",\"LU\":\"Luxembourg\",\"MO\":\"Macao SAR China\",\"MG\":\"Madagascar\",\"MW\":\"Malawi\",\"MY\":\"Malaysia\",\"MV\":\"Maldives\",\"ML\":\"Mali\",\"MT\":\"Malta\",\"MH\":\"Marshall Islands\",\"MQ\":\"Martinique\",\"MR\":\"Mauritania\",\"MU\":\"Mauritius\",\"YT\":\"Mayotte\",\"MX\":\"Mexico\",\"FM\":\"Micronesia\",\"MD\":\"Moldova\",\"MC\":\"Monaco\",\"MN\":\"Mongolia\",\"ME\":\"Montenegro\",\"MS\":\"Montserrat\",\"MA\":\"Morocco\",\"MZ\":\"Mozambique\",\"MM\":\"Myanmar (Burma)\",\"NA\":\"Namibia\",\"NR\":\"Nauru\",\"NP\":\"Nepal\",\"NL\":\"Netherlands\",\"NC\":\"New Caledonia\",\"NZ\":\"New Zealand\",\"NI\":\"Nicaragua\",\"NE\":\"Niger\",\"NG\":\"Nigeria\",\"NU\":\"Niue\",\"NF\":\"Norfolk Island\",\"KP\":\"North Korea\",\"MK\":\"North Macedonia\",\"MP\":\"Northern Mariana Islands\",\"NO\":\"Norway\",\"OM\":\"Oman\",\"PK\":\"Pakistan\",\"PW\":\"Palau\",\"PS\":\"Palestinian Territories\",\"PA\":\"Panama\",\"PG\":\"Papua New Guinea\",\"PY\":\"Paraguay\",\"PE\":\"Peru\",\"PH\":\"Philippines\",\"PN\":\"Pitcairn Islands\",\"PL\":\"Poland\",\"PT\":\"Portugal\",\"XA\":\"Pseudo-Accents\",\"XB\":\"Pseudo-Bidi\",\"PR\":\"Puerto Rico\",\"QA\":\"Qatar\",\"RE\":\"R\xE9union\",\"RO\":\"Romania\",\"RU\":\"Russia\",\"RW\":\"Rwanda\",\"WS\":\"Samoa\",\"SM\":\"San Marino\",\"ST\":\"S\xE3o Tom\xE9 & Pr\xEDncipe\",\"SA\":\"Saudi Arabia\",\"SN\":\"Senegal\",\"RS\":\"Serbia\",\"SC\":\"Seychelles\",\"SL\":\"Sierra Leone\",\"SG\":\"Singapore\",\"SX\":\"Sint Maarten\",\"SK\":\"Slovakia\",\"SI\":\"Slovenia\",\"SB\":\"Solomon Islands\",\"SO\":\"Somalia\",\"ZA\":\"South Africa\",\"GS\":\"South Georgia & South Sandwich Islands\",\"KR\":\"South Korea\",\"SS\":\"South Sudan\",\"ES\":\"Spain\",\"LK\":\"Sri Lanka\",\"BL\":\"St Barth\xE9lemy\",\"SH\":\"St Helena\",\"KN\":\"St Kitts & Nevis\",\"LC\":\"St Lucia\",\"MF\":\"St Martin\",\"PM\":\"St Pierre & Miquelon\",\"VC\":\"St Vincent & Grenadines\",\"SD\":\"Sudan\",\"SR\":\"Suriname\",\"SJ\":\"Svalbard & Jan Mayen\",\"SE\":\"Sweden\",\"CH\":\"Switzerland\",\"SY\":\"Syria\",\"TW\":\"Taiwan\",\"TJ\":\"Tajikistan\",\"TZ\":\"Tanzania\",\"TH\":\"Thailand\",\"TL\":\"Timor-Leste\",\"TG\":\"Togo\",\"TK\":\"Tokelau\",\"TO\":\"Tonga\",\"TT\":\"Trinidad & Tobago\",\"TA\":\"Tristan da Cunha\",\"TN\":\"Tunisia\",\"TR\":\"Turkey\",\"TM\":\"Turkmenistan\",\"TC\":\"Turks & Caicos Islands\",\"TV\":\"Tuvalu\",\"UG\":\"Uganda\",\"UA\":\"Ukraine\",\"AE\":\"United Arab Emirates\",\"GB\":\"United Kingdom\",\"US\":\"United States\",\"UY\":\"Uruguay\",\"UM\":\"US Outlying Islands\",\"VI\":\"US Virgin Islands\",\"UZ\":\"Uzbekistan\",\"VU\":\"Vanuatu\",\"VA\":\"Vatican City\",\"VE\":\"Venezuela\",\"VN\":\"Vietnam\",\"WF\":\"Wallis & Futuna\",\"EH\":\"Western Sahara\",\"YE\":\"Yemen\",\"ZM\":\"Zambia\",\"ZW\":\"Zimbabwe\"}";
  return JSON.parse(rawjson);
};

iMapsRouter.camelize = function (str) {
  return str.replace(/(?:^\w|[A-Z]|\b\w)/g, function (word, index) {
    return index == 0 ? word.toLowerCase() : word.toUpperCase();
  }).replace(/\s+/g, "");
};
/**
 * File that contains functions to prepare raw data that arrives in a javascript variable.
 * 1) Convert color strings to color objects
 * */


var iMapsModel = {};

iMapsModel.prepareData = function (fullData) {
  if (!fullData) {
    return fullData;
  }

  if (!Array.isArray(fullData)) {
    return fullData;
  }

  fullData.forEach(function (data, index) {
    fullData[index] = iMapsModel.iterateData(data);

    if (Array.isArray(fullData[index].overlay) && fullData[index].overlay.length > 0) {
      fullData[index].overlay.forEach(function (odata, ind) {
        // if the overlay map is the same as base map, only include the active regions
        if (fullData[index].overlay[ind].map === fullData[index].map && ! iMapsManager.bool( fullData[index].allowEmpty ) )  {
          fullData[index].overlay[ind].include = iMapsModel.prepareOverlayInclude(odata);
        }

        fullData[index].overlay[ind] = iMapsModel.iterateData(odata);
      });
    }
  });
  return fullData;
};

iMapsModel.prepareOverlayInclude = function (data) {
  var includes = data.includes || "";

  if (data.regions) {
    data.regions.forEach(function (region, index) {
      includes += "," + region.id;
    });
  }

  return includes;
};

iMapsModel.iterateData = function (data) {
  // check if there's a custom callback function to prepare the data
  if (typeof igm_custom_filter === 'function') {
    data = igm_custom_filter(data);
  } // check if there's a map specific custom callback function


  var mapFunction = 'igm_custom_filter_' + data.id;

  if (typeof window[mapFunction] === 'function') {
    data = window[mapFunction](data);
  } // create color objects


  data = iMapsModel.prepareColor(data); // convert exclude and includes to array

  data = iMapsModel.prepareExcludeIncludes(data); // convert coordinates to int

  data = iMapsModel.coordinatesToInt(data); // prepare entries that use defaults

  data = iMapsModel.prepareEntriesData(data); // prepare grouped regions

  data = iMapsModel.prepareGroupedRegions(data); //extract image source from imageMarkers

  data = iMapsModel.prepareImageFields(data); //prepare lines multiGeoLine format

  data = iMapsModel.prepareMultiGeoLine(data);
  return data;
};


iMapsModel.prepareMultiGeoLine = function (data) {
  if (Array.isArray(data.lines) && data.lines.length) {
    data.lines.map(function (line) {
      // notice the arrow option needs to be reversed, because it controls the arrow.disabled option when rendered
      line.arrow = typeof line.arrow !== 'undefined' ? iMapsManager.bool( line.arrow ) : false;
      line.arrowDisabled = ! line.arrow;
      line.multiGeoLine.map(function (geoLine) {
        Object.assign(geoLine, geoLine.coordinates);
        delete geoLine.coordinates;
      });
    });
  }

  return data;
};

iMapsModel.prepareImageFields = function (data) {
  // image markers
  if (Array.isArray(data.imageMarkers) && Array.isArray( data.imageMarkers )) {
    data.imageMarkers.map(function (marker) {
      if (typeof marker.href === 'undefined' && marker.image && ( Array.isArray( marker.image ) || typeof marker.image === 'object' )) {
        marker.href = marker.image.url;
      }
      return marker;
    });
  }

  return data;
};

iMapsModel.prepareGroupedRegions = function (data) {
  var regions, tempRegion, group; // regions

  if (Array.isArray(data.regions) && data.regions.length) {

    if (typeof data.regionGroups === 'undefined') {
      data.regionGroups = [];
    }

    // if the option to group entries is enabled, cosider them as a group entry
  if(iMapsManager.bool ( data.regionsGroupHover ) ) {
    data.regionGroups.push(data.regions);
    return data;
  }


    data.regions.map(function (region, index) {
      if (region.id && String(region.id).includes(",")) {
        group = [];
        regions = region.id.split(",");
        regions.forEach(function (reg, ix) {
          tempRegion = Object.assign({}, region);
          tempRegion.id = reg.trim();
          tempRegion.originalID = region.id;

          // check if numeric
          if (!isNaN(tempRegion.id)) {
            // tempRegion.id = parseFloat( tempRegion.id );
          }

          group.push(tempRegion);
        });

        // add group to main data object
        // finally add it to the main data object
        data.regionGroups.push(group);

        // remove this region from the default regions data set
        data.regions.splice(index, -1);
      } else {
        return region;
      }
    });
  }

  return data;
};

iMapsModel.prepareTooltip = function (tooltipContent, options) {
  var maxWidth, isMSIE, images, tooltipNode, range, tempDiv;

  if (typeof options === 'undefined' || typeof tooltipContent === 'undefined') {
    return tooltipContent;
  }

  maxWidth = typeof options.maxWidth !== "undefined" && options.maxWidth !== '' ? parseInt(options.maxWidth) : false;
  isMSIE = iMapsModel.isMSIE(); // remove html tags for IE

  if (isMSIE) {
    return iMapsModel.removeHTMLtags(tooltipContent);
  } // check for images


  if (tooltipContent && tooltipContent.includes("<img")) {
    // images
    range = document.createRange(); // Make the parent of the first div in the document becomes the context node

    range.selectNode(document.getElementsByTagName("div").item(0));
    tooltipNode = range.createContextualFragment(tooltipContent);
    images = tooltipNode.querySelectorAll('img');

    if (images.length > 0) {
      images.forEach(function (img) {
        if (img.width !== 0 && img.style.width === '') {
          img.style.width = img.width + 'px';
        }

        if (img.height !== 0 && img.style.height === '') {
          img.style.height = img.height + 'px';
        }
      });
      tempDiv = document.createElement('div');
      tempDiv.appendChild(tooltipNode.cloneNode(true));
      tooltipContent = tempDiv.innerHTML;
    }
  } // tooltip


  if (maxWidth && tooltipContent !== '' && !isMSIE) {
    tooltipContent = '<div class="imapsInnerTooltip" style="max-width:' + maxWidth + 'px">' + tooltipContent + '</div>';
  }

  return tooltipContent;
};

iMapsModel.isMSIE = function () {
  return window.navigator.userAgent.match(/(MSIE|Trident)/);
}

iMapsModel.removeHTMLtags = function (str) {
  if (str === null || str === '') return '';
  else str = str.toString();
  return str.replace(/<[^>]*>/g, '');
};

iMapsModel.prepareEntriesData = function (data) {
  if (data.heatMapMarkers && data.heatMapMarkers.enabled === "1") {
    data.heatMapMarkers.minRadius = parseInt(data.heatMapMarkers.minRadius);
    data.heatMapMarkers.maxRadius = parseInt(data.heatMapMarkers.maxRadius);
  }

  var mapID = data.id; // regions

  if (Array.isArray(data.regions) && data.regions.length) {
    data.regions.map(function (region) {
      if (typeof region.useDefaults === "undefined" || region.useDefaults === "1") {
        Object.assign(region, data.regionDefaults);
      }

      if (typeof region.action !== 'undefined' && region.action === 'default') {
        region.action = data.regionDefaults.action;
      }

      if (typeof data.regionDefaults.triggerClickOnHover !== 'undefined' && data.regionDefaults.triggerClickOnHover === '1') {
        region.triggerClickOnHover = true;
      }

      if (typeof data.regionActiveState !== 'undefined' && data.regionActiveState.enabled === '1') {
        region.activeState = true;
      }

      // auto labels properties
      if (data.regionLabels && data.regionLabels.source === "custom" && typeof data.regionLabels.customSource !== "undefined") {
        if (data.regionLabels.customSource.includes('.')) {
          region.autoLabel = data.regionLabels.customSource.split('.').reduce(function (obj, i) {
            if (obj[i]) {
              return obj[i];
            }

            return 0;
          }, region);
        } else {
          region.autoLabel = region[data.regionLabels.customSource];
        }
      }

      if (data.heatMapRegions && data.heatMapRegions.enabled === "1") {
        // if it includes a reference with dot notation
        if (data.heatMapRegions.source.includes('.')) {
          region.heatMapRegionRef = data.heatMapRegions.source.split('.').reduce(function (obj, i) {
            if (obj[i]) {
              return obj[i];
            }

            return 0;
          }, region);
        }

        delete region.fill;
      } // default action


      // border hover - currently doesn't exist
      if (typeof data.visual.borderColorHover !== "undefined" && data.visual.borderColorHover !== data.visual.borderColor) {
        region.borderColorHover = data.visual.borderColorHover;
      } // border width on hover - currently doesn't exist


      if (typeof data.visual.borderWidthHover !== "undefined" && data.visual.borderWidthHover !== data.visual.borderWidth) {
        region.borderWidthHover = data.visual.borderWidthHover;
      }

      region.tooltipContent = iMapsModel.prepareTooltip(region.tooltipContent, data.tooltip);
      region.mapID = mapID;

      // fix for value with "-" minus sign
      if (region.value && _typeof(region.value) !== undefined && region.value !== '') {

        region.val = region.value;
        region.value = parseFloat(region.value);
      }

      return region;
    }); // heatmap with dot notation
    // if it includes a reference with dot notation

    if (data.heatMapRegions && data.heatMapRegions.source.includes('.')) {
      data.heatMapRegions.source = 'heatMapRegionRef';
    }
  } // round markers


  if (Array.isArray(data.roundMarkers) && data.roundMarkers.length) {
    data.roundMarkers.map(function (marker) {
      if (typeof marker.useDefaults === "undefined" || marker.useDefaults === "1") {
        Object.assign(marker, data.markerDefaults);
      }

      if (marker.coordinates) {
        marker.latitude = marker.coordinates.latitude;
        marker.longitude = marker.coordinates.longitude; //Object.assign(marker, marker.coordinates);
        //delete marker.coordinates;
      } // set name to be id


      if( typeof marker.latitude === 'string' ){
        marker.latitude = parseFloat(marker.latitude);
      }
      if( typeof marker.longitude === 'string' ){
        marker.longitude = parseFloat(marker.latitude);
      }

      if (typeof marker.name === 'undefined') {
        marker.name = marker.id;
      }

      if (data.roundMarkersMobileSize && parseInt(data.roundMarkersMobileSize) !== 100) {
        if (window.innerWidth <= 780) {
          marker.radius = parseFloat(marker.radius) * parseFloat(data.roundMarkersMobileSize) / 100;
        }
      }

      if (data.heatMapMarkers && data.heatMapMarkers.enabled === "1") {
        if (!isNaN(marker.value)) {
          marker.value = parseFloat(marker.value);
        } // if it includes a reference with dot notation


        if (data.heatMapMarkers.source.includes('.')) {
          marker.heatMapMarkerRef = data.heatMapMarkers.source.split('.').reduce(function (obj, i) {
            if (obj[i]) {
              return obj[i];
            }

            return 0;
          }, marker);
        }

        delete marker.fill;
        delete marker.radius;
      }

      // fix for value with "-" minus sign
      if (marker.value && _typeof(marker.value) !== undefined && marker.value !== '') {

        marker.val = marker.value;
        marker.value = parseFloat(marker.value);
      }

      // default action
      if (marker.action === "default") {
        marker.action = data.markerDefaults.action;
      }

      if (typeof data.markerDefaults.triggerClickOnHover !== 'undefined' && data.markerDefaults.triggerClickOnHover === '1') {
        marker.triggerClickOnHover = true;
      }

      if (typeof data.triggerRegionHover !== 'undefined' && data.triggerRegionHover.enabled === '1') {
        marker.triggerRegionHover = true;
      }

      marker.tooltipContent = iMapsModel.prepareTooltip(marker.tooltipContent, data.tooltip);
      marker.mapID = mapID;
      return marker;
    }); // remove markers with empty coordinates

    data.roundMarkers = data.roundMarkers.filter(function (marker) {
      return typeof marker.latitude !== 'undefined' && marker.latitude !== '' && marker.name !== '';
    }); // heatmap with dot notation
    // if it includes a reference with dot notation

    if (data.heatMapMarkers && data.heatMapMarkers.source.includes('.')) {
      data.heatMapMarkers.source = 'heatMapMarkerRef';
    }
  } // icon markers


  if (Array.isArray(data.iconMarkers) && data.iconMarkers.length) {
    data.iconMarkers.map(function (marker) {
      if (typeof marker.useDefaults === "undefined" || marker.useDefaults === "1") {
        Object.assign(marker, data.iconMarkerDefaults);
      }

      if (marker.coordinates) {
        marker.latitude = marker.coordinates.latitude;
        marker.longitude = marker.coordinates.longitude; //Object.assign(marker, marker.coordinates);
        //delete marker.coordinates;
      } // set name to be id


      if (typeof marker.name === 'undefined') {
        marker.name = marker.id;
      } // default action


      if (marker.action === "default") {
        marker.action = data.iconMarkerDefaults.action;
      }


      if (typeof data.iconMarkerDefaults.triggerClickOnHover !== 'undefined' && data.iconMarkerDefaults.triggerClickOnHover === '1') {
        marker.triggerClickOnHover = true;
      }

      // Possible approach to resize icon marker on smaller screens, but won't resize on the fly


      if (data.iconMarkersMobileSize && parseInt(data.iconMarkersMobileSize) !== 100) {
        if (window.innerWidth <= 780) {
          marker.scale = parseFloat(marker.scale) * parseFloat(data.iconMarkersMobileSize) / 100;
        }
      }

      // solve issue with value parameter only accepting numbers
      marker.val = marker.value;
      marker.value = parseFloat(marker.value);

      marker.tooltipContent = iMapsModel.prepareTooltip(marker.tooltipContent, data.tooltip);
      marker.mapID = mapID;
      return marker;
    });
  } // image markers


  if (Array.isArray(data.imageMarkers) && data.imageMarkers.length) {
    data.imageMarkers.map(function (marker) {
      if (typeof marker.useDefaults === "undefined" || marker.useDefaults === "1") {
        Object.assign(marker, data.imageMarkerDefaults);
      } 

      if (marker.coordinates) {
        marker.latitude = marker.coordinates.latitude;
        marker.longitude = marker.coordinates.longitude; //Object.assign(marker, marker.coordinates);
        //delete marker.coordinates;
      } // set name to be id

      if (typeof marker.nonScaling !== 'undefined') {
        marker.nonScaling = iMapsManager.bool(marker.nonScaling);
      }

      if (typeof marker.size === 'undefined') {
        marker.size = data.imageMarkerDefaults.size;
      }

      if (typeof marker.horizontalCenter === 'undefined') {
        marker.horizontalCenter = data.imageMarkerDefaults.horizontalCenter;
      }

      if (typeof marker.verticalCenter === 'undefined') {
        marker.verticalCenter = data.imageMarkerDefaults.verticalCenter;
      }

      if (typeof marker.name === 'undefined') {
        marker.name = marker.id;
      }

      if (marker.action === "default" || typeof marker.action === 'undefined') {
        marker.action = data.imageMarkerDefaults.action;
      }

      if (typeof data.imageMarkerDefaults.triggerClickOnHover !== 'undefined' && data.imageMarkerDefaults.triggerClickOnHover === '1') {
        marker.triggerClickOnHover = true;
      }


      // Possible approach to resize icon marker on smaller screens, but won't resize on the fly


      if (data.imageMarkersMobileSize && parseInt(data.imageMarkersMobileSize) !== 100) {
        if (window.innerWidth <= 780) {
          marker.size = parseFloat(marker.size) * parseFloat(data.imageMarkersMobileSize) / 100;
        }
      }

      // solve problem of value param only accepting numbers
      marker.val = marker.value;
      marker.value = parseFloat(marker.value);

      marker.tooltipContent = iMapsModel.prepareTooltip(marker.tooltipContent, data.tooltip);
      marker.mapID = mapID;
      return marker;
    });
  } // labels


  if (Array.isArray(data.labels) && data.labels.length) {
    data.labels.map(function (label) {
      if (typeof label.useDefaults === "undefined" || label.useDefaults === "1") {
        Object.assign(label, data.labelDefaults);
        Object.assign(label, data.labelPosition);
      }

      if (label.coordinates) {
        Object.assign(label, label.coordinates);
        delete label.coordinates;
      }

      if (label.action === "default") {
        label.action = data.labelDefaults.action;
      }

      if (typeof data.labelDefaults.triggerClickOnHover !== 'undefined' && data.labelDefaults.triggerClickOnHover === '1') {
        label.triggerClickOnHover = true;
      }

      if (typeof label.size !== 'undefined') {
        label.fontSize = label.size;
      } // change font size on smaller screens


      if (data.labelsMobileSize && parseInt(data.labelsMobileSize) !== 100) {
        if (window.innerWidth <= 780) {
          label.fontSize = parseInt(label.fontSize) * parseInt(data.labelsMobileSize) / 100;
          label.size = label.fontSize;
        }
      }

      label.tooltipContent = iMapsModel.prepareTooltip(label.tooltipContent, data.tooltip);
      label.mapID = mapID;
      return label;
    });
  } // lines


  if (Array.isArray(data.lines) && data.lines.length) {
    data.lines.map(function (line) {
      if (typeof line.useDefaults === "undefined" || line.useDefaults === "1") {
        Object.assign(line, data.lineDefaults);
      }

      line.curvature = parseFloat(line.curvature);
      return line;
    });
  }

  return data;
};

iMapsModel.prepareExcludeIncludes = function (data) {
  if (typeof data.onlyIncludeActive !== 'undefined' && parseInt(data.onlyIncludeActive) === 1) {
    data.include = [];
    data.exclude = [];

    if (data.regions) {
      data.regions.forEach(function (region, index) {
        data.include.push(region.id);

        if (!isNaN(region.id)) {
          data.include.push(parseInt(region.id));
        }

      });
    }

    return data;
  }

  if (data.exclude && typeof data.exclude === "string" && data.exclude.trim().length) {
    data.exclude = data.exclude.split(",").map(function (item) {
      return item.trim();
    });
  }

  if (data.include && data.include.trim().length) {
    data.include = data.include.split(",").map(function (item) {
      return item.trim();
    });

    data.include.map(function (item) {
      if (!isNaN(item)) {
        data.include.push(parseInt(item));
      }
    });

  }

  return data;
};

iMapsModel.coordinatesToInt = function (data) {
  var convertCoordinates = function convertCoordinates(key, obj) {
      obj[key].latitude = Number(obj[key].latitude);
      obj[key].longitude = Number(obj[key].longitude);
    },
    iterateObj = function iterateObj(Obj) {
      if (_typeof(Obj) !== "object" || Obj === null) {
        return;
      }

      Object.keys(Obj).map(function (key, index) {
        if (_typeof(Obj[key]) === "object") {
          if (key === "coordinates" || key === "homeGeoPoint") {
            convertCoordinates(key, Obj);
          } 
          else if (typeof Obj['className'] !== 'undefined' ) {
            return;
          }
          else {
            iterateObj(Obj[key]);
          }
        }
      });
    };

  iterateObj(data);
  return data;
};


iMapsModel.prepareColor = function (data) {
  // prepare color fields
  var colorFields = ["inactiveColor", "activeColor", "hoverColor", "hover", "inactiveHoverColor", "backgroundColor", "color", "minColor", "maxColor", "fill", "projectionBackgroundColor", "borderColor", "borderColorHover"],
  createGradient = function createGradient( data ){
    var colours = data.split("|");
    var gradient, gradientType, gradientOffset, colourIndex;
    
    gradientType   = typeof igmGradientType !== 'undefined' ? igmGradientType : 'LinearGradient';
    gradientOffset = typeof igmGradientOffset !== 'undefined' && Array.isArray(igmGradientOffset) ? igmGradientOffset : [];

    gradient = new am4core[gradientType]();
    colours.forEach(function(color, index){
          gradient.addColor(am4core.color(color), 1, gradientOffset[index]);
    });

    //rotation
    gradient.rotation = typeof igmGradientRotation !== 'undefined' ? igmGradientRotation : 0;
    return gradient;
  },
  createPattern = function createPattern( data ) {
    var url = data;
    // Create pattern
    var pattern = new am4core.Pattern();
    pattern.width = 150;
    pattern.height = 150;
    pattern.strokeWidth = 0;
    pattern.stroke = am4core.color('#6699cc');
    //pattern.patternUnits = 'objectBoundingBox'; // objectBoundingBox // userSpaceOnUse

    var image = new am4core.Image();
    image.href = url;
    image.width = 150;
    image.height = 150;
    image.x = 0;
    image.y = 0;
    image.verticalCenter = "middle";
    image.valign = "middle";
    pattern.addElement(image.element);
    pattern.addElement(image.element);
    pattern.addElement(image.element);
    return pattern;
  },
  checkColor = function checkColor(key, obj) {
      if (colorFields.includes(key)) {
        if (obj[key].includes("|")) {
          obj[key] = createGradient( obj[key] );
        }
        else if (obj[key].startsWith("http")) {
          obj[key] = createPattern( obj[key] );
        }
        else if (obj[key] === "transparent") {
          obj[key] = am4core.color("#f00", 0);
        } else {
          obj[key] = am4core.color(obj[key]);
        }
      }
    },
    iterateObj = function iterateObj(Obj) {
      if (_typeof(Obj) !== "object" || Obj === null) {
        return;
      }

      Object.keys(Obj).map(function (key, index) {
        if (_typeof(Obj[key]) === "object") {
          iterateObj(Obj[key]);
        } 
        else if (typeof Obj['className'] !== 'undefined' ) {
          return;
        }
        else {
          checkColor(key, Obj);
        }
      });
    };

  iterateObj(data);
  return data;
};

/**
 * Retrives object with region codes and names from geojson
 */


iMapsModel.extractCodes = function (data) {
  var obj = {};

  for (var i = 0; i < data.features.length; i++) {
    obj[data.features[i].properties.id] = data.features[i].properties.name;
  }

  return obj;
};

"use strict";
/* jshint node: true */

/* global Promise */

/* jshint browser: true */

/* global am4maps */

/* global am4core */

/* global iMapsData */

/* global geocluster */

/* global iMapsRouter */

/* global iMaps */

/* jshint esversion:5 */

/* global iMapsActions */

/*
Model Object with methods and helpers to build the maps
*/


var iMapsManager = {};
iMapsManager.maps = {};

iMapsManager.init = function (i) {
  var im = this;
  im.addMap(i);
};
/**
 * Adds maps based on data index
 *
 */


iMapsManager.addMap = function (index) {
  var im = this,
    data = iMapsData.data[index],
    id = data.id,
    map,
    regionSeries,
    groupedSeries,
    markerSeries,
    labelSeries,
    lineSeries,
    clusters,
    mapContainer,
    seriesColumn,
    legendHover,
    legendActive,
    customLegend,
    imageSeries,
    iconSeries,
    graticuleSeries,
    clusterSeries,
    mapVar,
    bgSeries,
    bgImage,
    aspRatioContainer,
    container = document.getElementById(data.container);

  if (data.disabled) {
    return;
  }

  if (container === null) {
    return;
  }

  aspRatioContainer = container.closest(".map_aspect_ratio");


  // if map was already built
  if (typeof im.maps[id] !== 'undefined') {
    im.maps[id].map.dispose();
  }
  
  // map container size adjustment
  // if mobile
  if (window.innerWidth <= 780 && typeof data.visual.paddingTop !== 'undefined' && data.visual.paddingTop !== '') {
    aspRatioContainer.style.paddingTop = String(data.visual.paddingTopMobile) + '%';
  } else {
    aspRatioContainer.style.paddingTop = String(data.visual.paddingTop) + '%';
  }

  window.addEventListener('resize', function(){
    if (window.innerWidth <= 780 && typeof data.visual.paddingTop !== 'undefined' && data.visual.paddingTop !== '') {
      aspRatioContainer.style.paddingTop = String(data.visual.paddingTopMobile) + '%';
    } else {
      aspRatioContainer.style.paddingTop = String(data.visual.paddingTop) + '%';
    }
  });


  if (data.visual.maxWidth !== "") {
    //container.closest(".map_box").style.maxWidth = String(data.visual.maxWidth) + "px";
  }

  if (data.visual.fontFamily !== "" && data.visual.fontFamily !== "inherit") {
    container.closest(".map_box").style.fontFamily = data.visual.fontFamily;
  }

  // create map and a shorter reference to it
  im.maps[id] = {
    map: am4core.create(data.container, am4maps.MapChart),
    series: [],
    clusterSeries: {},
    seriesIndex: {},
    seriesById: {},
    data: data,
    allBaseSeries: [],
    labelSeries:[],
    baseRegionSeries: {},
    groupedBaseRegionSeries: [],
    backgroundSeries: {}
  };
  map = im.maps[id].map;
  map.readerTitle = "Interactive Map";

  clusterSeries = im.maps[id].clusterSeries;
  // ready event
  // on click map debug
  map.events.on("ready", function (ev) {
    var event = new Event("mapready");
    container.dispatchEvent(event);
    // we might move the event that triggers on ready to mapappeared because it doesn't work well with custom maps
    im.triggerOnReady(id, data);
  });
  map.events.on("appeared", function (ev) {
    var event = new Event("mapappeared");
    container.dispatchEvent(event);
    im.triggerOnAppeared(id, data);
    container.classList.remove('map_loading');
  });

  // locale
  if (typeof iMapsData.options !== "undefined" && typeof iMapsData.options.locale !== "undefined" && iMapsData.options.locale && typeof window['am4lang_' + iMapsData.options.locale] !== "undefined") {
    map.language.locale = window['am4lang_' + iMapsData.options.locale];
  } // enable small map


  if (data.zoom && data.zoom.smallMap && im.bool(data.zoom.smallMap)) {
    map.smallMap = new am4maps.SmallMap();
  } // load map geodata


  if (data.map === "custom" || im.bool(data.useGeojson)) {
    map.geodataSource.url = data.mapURL;
  } else {
    mapVar = iMapsRouter.getVarByName(data.map);
    map.geodata = window[mapVar];
  }

  if (typeof iMapsData.options !== "undefined" && typeof iMapsData.options.lang !== "undefined" && iMapsData.options.lang && typeof window['am4geodata_lang_' + iMapsData.options.lang] !== "undefined") {
    map.geodataNames = window['am4geodata_lang_' + iMapsData.options.lang];
  }

  // projection - moved to the end of the function to fix issue with Albers not rendering correctly
  map.projection = new am4maps.projections[data.projection]();

  // fix issues with USA territories map
  if (data.map.startsWith("usaTerritories")) {
    map.events.on("ready", function (ev) {
      map.projection = new am4maps.projections[data.projection]();
    });
  }

  map.fontFamily = data.visual.fontFamily;

  // export menu
  if (data.exportMenu && im.bool(data.exportMenu.enable)) {
    map.exporting.menu = new am4core.ExportMenu();
    map.exporting.menu.items[0].icon = "data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMjAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CiA8Zz4KICA8dGl0bGU+YmFja2dyb3VuZDwvdGl0bGU+CiAgPHJlY3QgeD0iLTEiIHk9Ii0xIiB3aWR0aD0iNS45NDQ3NzMiIGhlaWdodD0iNS45NDQ3NzMiIGlkPSJjYW52YXNfYmFja2dyb3VuZCIgZmlsbD0ibm9uZSIvPgogPC9nPgoKIDxnPgogIDx0aXRsZT5MYXllciAxPC90aXRsZT4KICA8cGF0aCBzdHJva2U9Im51bGwiIGQ9Im0xNC40MjcwMzEsMTUuNzQwNDAxcTAsLTAuMjU3NjQzIC0wLjE4ODI3NCwtMC40NDU5MTd0LTAuNDQ1OTE3LC0wLjE4ODI3NHQtMC40NDU5MTcsMC4xODgyNzR0LTAuMTg4Mjc0LDAuNDQ1OTE3dDAuMTg4Mjc0LDAuNDQ1OTE3dDAuNDQ1OTE3LDAuMTg4Mjc0dDAuNDQ1OTE3LC0wLjE4ODI3NHQwLjE4ODI3NCwtMC40NDU5MTd6bTIuNTM2NzgsMHEwLC0wLjI1NzY0MyAtMC4xODgyNzQsLTAuNDQ1OTE3dC0wLjQ0NTkxNywtMC4xODgyNzR0LTAuNDQ1OTE3LDAuMTg4Mjc0dC0wLjE4ODI3NCwwLjQ0NTkxN3QwLjE4ODI3NCwwLjQ0NTkxN3QwLjQ0NTkxNywwLjE4ODI3NHQwLjQ0NTkxNywtMC4xODgyNzR0MC4xODgyNzQsLTAuNDQ1OTE3em0xLjI2ODM5LC0yLjIxOTY4NWwwLDMuMTcwOTdxMCwwLjM5NjM3NCAtMC4yNzc0NjEsMC42NzM4MzR0LTAuNjczODM0LDAuMjc3NDYxbC0xNC41ODY0NywwcS0wLjM5NjM3NCwwIC0wLjY3MzgzNCwtMC4yNzc0NjF0LTAuMjc3NDYxLC0wLjY3MzgzNGwwLC0zLjE3MDk3cTAsLTAuMzk2Mzc0IDAuMjc3NDYxLC0wLjY3MzgzNHQwLjY3MzgzNCwtMC4yNzc0NjFsNC42MDc4MTYsMGwxLjMzNzc1MSwxLjM0NzY1OXEwLjU3NDczOCwwLjU1NDkyMSAxLjM0NzY1OSwwLjU1NDkyMXQxLjM0NzY1OSwtMC41NTQ5MjFsMS4zNDc2NTksLTEuMzQ3NjU5bDQuNTk3OTA4LDBxMC4zOTYzNzQsMCAwLjY3MzgzNCwwLjI3NzQ2MXQwLjI3NzQ2MSwwLjY3MzgzNGwwLjAwMDAxOCwwem0tMy4yMjA1MjMsLTUuNjM4MzlxMC4xNjg0NTYsMC40MDYyODIgLTAuMTM4NzMsMC42OTM2NTFsLTQuNDM5MzYsNC40MzkzNnEtMC4xNzgzNjUsMC4xODgyNzQgLTAuNDQ1OTE3LDAuMTg4Mjc0dC0wLjQ0NTkxNywtMC4xODgyNzRsLTQuNDM5MzYsLTQuNDM5MzZxLTAuMzA3MTg3LC0wLjI4NzM2OSAtMC4xMzg3MywtMC42OTM2NTFxMC4xNjg0NTYsLTAuMzg2NDY1IDAuNTg0NjQ3LC0wLjM4NjQ2NWwyLjUzNjc4LDBsMCwtNC40MzkzNnEwLC0wLjI1NzY0MyAwLjE4ODI3NCwtMC40NDU5MTd0MC40NDU5MTcsLTAuMTg4Mjc0bDIuNTM2NzgsMHEwLjI1NzY0MywwIDAuNDQ1OTE3LDAuMTg4Mjc0dDAuMTg4Mjc0LDAuNDQ1OTE3bDAsNC40MzkzNmwyLjUzNjc4LDBxMC40MTYxOTEsMCAwLjU4NDY0NywwLjM4NjQ2NXoiIGlkPSJzdmdfMSIvPgogPC9nPgo8L3N2Zz4=";
    map.exporting.menu.align = data.exportMenu.align ? data.exportMenu.align : "right";
    map.exporting.menu.verticalAlign = data.exportMenu.verticalAlign ? data.exportMenu.verticalAlign : "top";
  }


  // touch devices options
  if (data.touchInterface) {
    if (im.bool(data.touchInterface.tapToActivate)) {
      map.tapToActivate = true;
      map.tapTimeout = data.touchInterface.tapTimeout;
    }

    if (im.bool(data.touchInterface.dragGrip)) {
      map.dragGrip.disabled = false;
      map.dragGrip.autoHideDelay = data.touchInterface.dragGripAutoHideDelay;
    }
  }

  // different map center
  //map.deltaLongitude = -10;
  // pan behaviours
  // map.panBehavior = "rotateLongLat";
  // map.panBehavior = "rotateLong";
  // map.deltaLatitude = -20;
  // map.padding(20, 20, 20, 20);
  // visual settings


  map.background.fill = data.visual.backgroundColor;
  map.background.fillOpacity = data.visual.backgroundOpacity;

  // iOS scroll issue fix - not working
  map.backgroundSeries.mapPolygons.template.focusable = false;
  map.background.focusable = false;
  map.backgroundSeries.focusable = false;

  // backgroud image
  if (typeof data.visual.bgImage !== 'undefined' && typeof data.visual.bgImage.url !== 'undefined' && data.visual.bgImage.url !== '') {
    bgSeries = map.series.push(new am4maps.MapImageSeries());
    bgSeries.toBack();
    bgImage = bgSeries.mapImages.template.createChild(am4core.Image);
    bgImage.propertyFields.href = "src";
    bgImage.width = map.width;
    bgImage.height = map.height;
    bgSeries.data = [{
      src: data.visual.bgImage.url
    }];

    im.maps[id].backgroundSeries = bgSeries;
  }

  map.exporting.backgroundColor = data.visual.backgroundColor;
  map.exporting.filePrefix = "interactive_map";
  map.exporting.useWebFonts = false;

  // graticulate - grid lines
  if (data.projection === "Orthographic" && data.grid) {
    graticuleSeries = map.series.push(new am4maps.GraticuleSeries());
    graticuleSeries.mapLines.template.line.stroke = data.grid.color;
    graticuleSeries.mapLines.template.line.strokeOpacity = 1;
    graticuleSeries.fitExtent = false;
    map.backgroundSeries.mapPolygons.template.polygon.fillOpacity = 1;
    map.backgroundSeries.mapPolygons.template.polygon.fill = data.grid.projectionBackgroundColor;
  }



  im.handleZoom(id); // Auto Legend


  if (data.legend && im.bool(data.legend.enabled)) {
    map.legend = new am4maps.Legend(); // positioing

    if (data.legend.position === "top" || data.legend.position === "bottom") {
      map.legend.contentAlign = data.legend.align;
      map.legend.valign = data.legend.position;
    } else {
      map.legend.position = data.legend.position;
      map.legend.align = data.legend.position;
      map.legend.valign = data.legend.valign;
    } 


    if (typeof data.legend.style !== 'undefined' && data.legend.style !== 'default') {
      im.setLegendStyle(id, map.legend, data.legend.style);
    }

    if (_typeof(data.legend.fill) !== undefined) {
      map.legend.labels.template.fill = data.legend.fill;
    } // interactive


    if (data.legend.clickable === "false") {
      map.legend.itemContainers.template.interactionsEnabled = false;
    }

    if (data.legend.clickable === "toggle") {
      // do nothing, it's the default event
      // let's just clear the events, just in case.
      map.legend.itemContainers.template.events.on("hit", function (ev) {
        iMapsManager.clearSelected(id);
      });
    }

    if (data.legend.clickable === "select") {
      map.legend.itemContainers.template.togglable = false;
      map.legend.itemContainers.template.events.on("hit", function (ev) {
        iMapsManager.clearSelected(id);
        var select = [];
        var seriesType = im.getTargetSeriesType(ev.target.dataItem.dataContext);
        var target = ''; // currently only works for region series

        if (seriesType === "MapPolygonSeries") {
          ev.target.dataItem.dataContext.mapPolygons.each(function (polygon) {
            if (!polygon.dataItem.dataContext.madeFromGeoData) {
              polygon.setState("active");
              polygon.isActive = true;
              polygon.isHover = false;
              select.push(polygon);
            }
          });
          im.maps[id].selected = select;
        }
      });
      map.legend.itemContainers.template.events.on("over", function (ev) {
        var seriesType = im.getTargetSeriesType(ev.target.dataItem.dataContext);
        var target = '';

        if (seriesType === "MapImageSeries") {
          target = ev.target.dataItem.dataContext.mapImages;
          target.each(function (marker) {
            marker.setState("highlight");
          });
        } else if (seriesType === "MapPolygonSeries") {
          target = ev.target.dataItem.dataContext.mapPolygons;
          target.each(function (polygon) {
            if (!polygon.dataItem.dataContext.madeFromGeoData) {
              polygon.setState("highlight");
            }
          });
        } else {
          return;
        }
      });
      map.legend.itemContainers.template.events.on("out", function (ev) {
        var seriesType = im.getTargetSeriesType(ev.target.dataItem.dataContext);
        var target = '';

        if (seriesType === "MapImageSeries") {
          target = ev.target.dataItem.dataContext.mapImages;
          target.each(function (marker) {
            marker.setState("default");
          });
        } else if (seriesType === "MapPolygonSeries") {
          target = ev.target.dataItem.dataContext.mapPolygons;
          target.each(function (polygon) {
            if (!polygon.dataItem.dataContext.madeFromGeoData) {
              polygon.setState("default");
            }
          });
        } else {
          return;
        }
      });
    }
  }

  // Custom Legend
  if (data.customLegend && im.bool(data.customLegend.enabled)) {
    var customLegendType = typeof data.customLegend.type !== 'undefined' ? data.customLegend.type : 'internal';

    if (data.customLegend.data && Array.isArray(data.customLegend.data) && customLegendType === 'internal') {
      customLegend = new am4maps.Legend();
      customLegend.parent = map.chartContainer;
      customLegend.data = data.customLegend.data;

      /*
      customLegend.data.map(function(entry){
        entry.name = entry.name.replace(/&#(\d+);/g, function(match, dec) {
          return String.fromCharCode(dec);
        });
        return entry;
      });*/

      if (typeof data.customLegend.style !== 'undefined' && data.customLegend.style !== 'default') {
        im.setLegendStyle(id, customLegend, data.customLegend.style);
      } // no interaction with mouse


      customLegend.itemContainers.template.clickable = false;
      customLegend.itemContainers.template.focusable = false;
      customLegend.itemContainers.template.hoverable = false;
      customLegend.itemContainers.template.cursorOverStyle = am4core.MouseCursorStyle["default"];
      customLegend.labels.template.truncate = false;
      customLegend.labels.template.wrap = true; // positioing

      if (data.customLegend.position === "top" || data.customLegend.position === "bottom") {
        customLegend.contentAlign = data.customLegend.align;
        customLegend.valign = data.customLegend.position;
      } else {
        customLegend.position = data.customLegend.position;
        customLegend.align = data.customLegend.position;
        customLegend.valign = data.customLegend.valign;
      }

      if (_typeof(data.customLegend.fill) !== undefined) {
        customLegend.labels.template.fill = data.customLegend.fill;
      }
    }
  }

  // Create Main Series
  regionSeries = im.pushRegionSeries(id, data);
  im.maps[id].baseRegionSeries = regionSeries; // Check for grouped regions

  if (Array.isArray(data.regionGroups) && data.regionGroups.length) {
    groupedSeries = im.pushGroupSeries(id, data);
    im.maps[id].groupedBaseRegionSeries = groupedSeries;
  } // Overlay collections - we can add all series in the preset order


  if (Array.isArray(data.overlay) && data.overlay.length) {
    data.overlay.forEach(function (mapObj) {
      im.pushSeries(id, mapObj);
    });
  } // Create Other Series - we create them after the overlay to avoid overlap


  if (Array.isArray(data.lines) && data.lines.length) {
    lineSeries = im.pushLineSeries(id, data);
  }

  if (Array.isArray(data.roundMarkers) && data.roundMarkers.length) {
    markerSeries = im.pushRoundMarkerSeries(id, data);

    if (data.clusterMarkers && im.bool(data.clusterMarkers.enabled)) {
      markerSeries.hidden = true;
      clusters = im.setupClusters(data, id);
      clusterSeries[id].zoomLevels[data.clusterMarkers.zoomLevel] = markerSeries;
      // we setup the main index series (zoom=1) to be visible
      // when doing it inside setupClusters function, there was a bug
      if (clusterSeries[id].zoomLevels[1]) {
        clusterSeries[id].zoomLevels[1].hidden = false;
        // we also add it to the base series here, since it wasn't working in the setupClusters function
        im.maps[id].allBaseSeries.push(clusterSeries[id].zoomLevels[1]);
      }
    }
  }

  if (Array.isArray(data.imageMarkers) && data.imageMarkers.length) {
    imageSeries = im.pushImageMarkerSeries(id, data); //imageSeries.hiddenInLegend = true;

    im.maps[id].allBaseSeries.push(imageSeries);
  }

  if (Array.isArray(data.iconMarkers) && data.iconMarkers.length) {
    iconSeries = im.pushIconMarkerSeries(id, data); //iconSeries.hiddenInLegend = true;

    im.maps[id].allBaseSeries.push(iconSeries);
  }

  if (Array.isArray(data.labels) && data.labels.length) {
    labelSeries = im.pushLabelSeries(id, data); //labelSeries.hiddenInLegend = true;

    im.maps[id].allBaseSeries.push(labelSeries);
  } // checks if we should display info and creates events.


  im.handleInfoBox(id); // map.projection = new am4maps.projections[data.projection]();
};

iMapsManager.setLegendStyle = function (id, legend, style) {
  var sizes = {
    'xsmall': 10,
    'small': 13,
    'regular': 16,
    'large': 19,
    'larger': 23
  };
  var legendSize = sizes[style];
  var legendMarkerTemplate = legend.markers.template;
  legend.labels.template.fontSize = legendSize;
  legend.useDefaultMarker = true;
  legendMarkerTemplate.width = legendSize;
  legendMarkerTemplate.height = legendSize;

};

iMapsManager.handleZoom = function (id) {
  var im = this,
    map = im.maps[id].map,
    data = im.maps[id].data,
    allCurrentSeries = im.maps[id].series,
    allBaseSeries = im.maps[id].allBaseSeries,
    defaultOffset = true,
    defaultZoom = true; // Viewport settings

  // Zoom Level
  if (typeof data.viewport !== "undefined" && parseFloat(data.viewport.zoomLevel) >= 1) {
    map.homeZoomLevel = parseFloat(data.viewport.zoomLevel);
    defaultZoom = false; // to make sure everything else is disabled by default

    map.seriesContainer.resizable = false;
    map.seriesContainer.draggable = false;
    map.chartContainer.wheelable = false;
  }

  // Home center point
  if (typeof data.viewport !== "undefined" && data.viewport.homeGeoPoint && data.viewport.homeGeoPoint.latitude !== 0 && data.viewport.homeGeoPoint.longitude !== 0) {
    map.homeGeoPoint = data.viewport.homeGeoPoint;
  }

  // delta Coordinates Offset
  if (typeof data.viewport !== "undefined" && data.viewport.offset) {
    // only change if there are values, otherwise we might messup projections (Albers)
    if (data.viewport.offset.longitude !== '' && data.viewport.offset.longitude !== '0') {
      defaultOffset = false;
      map.deltaLongitude = data.viewport.offset.longitude;
    }

    if (data.viewport.offset.latitude !== '' && data.viewport.offset.latitude !== '0') {
      defaultOffset = false;
      map.deltaLatitude = data.viewport.offset.latitude;
    }

    if (defaultOffset) {
      iMapsManager.latlongOffsetFix(data, map, defaultZoom);
    }
  }

  // manual fixes for NZ and RU and Asia maps
  if (typeof data.viewport === "undefined") {
    iMapsManager.latlongOffsetFix(data, map, defaultZoom);
  }

  // default zoom behaviour
  if (typeof data.zoom === "undefined") {
    // default zoom behaviour when we can't find zoom settings
    if (typeof data.zoomMaster !== "undefined" && im.bool(data.zoomMaster)) {
      map.seriesContainer.draggable = true;
      map.seriesContainer.resizable = true;

      // display zoom controls by default
      map.zoomControl = new am4maps.ZoomControl();
      map.zoomControl.focusable = false;

      // iOS fix
      map.zoomControl.exportable = false;
      if (map.zoomControl.children && map.zoomControl.children.values) {
        map.zoomControl.children.values.forEach(function (child) {
          child.focusable = false;
        });
      }

      map.zoomControl.align = "right";
      map.zoomControl.valign = "bottom";
    } else {
      map.seriesContainer.resizable = false;
      map.seriesContainer.draggable = false;
    }

    map.seriesContainer.events.disableType("doublehit");
    map.chartContainer.background.events.disableType("doublehit");
    map.chartContainer.wheelable = false;
    return;
  } // disable drag in Ortographic and leave default for the rest


  if (data.projection !== "Orthographic") {
    map.seriesContainer.draggable = data.zoom ? im.bool(data.zoom.draggable) : false;
    map.seriesContainer.resizable = data.zoom ? im.bool(data.zoom.draggable) : false; // don't zoom out to center

    // control zoom and pan behaviour
    map.centerMapOnZoomOut = false;
    map.maxPanOut = 0; 

    // zoom is enabled, only allowdrag on mobile
    if (im.bool(data.zoom.enabled) && !im.bool(data.zoom.draggable) && im.bool(data.zoom.mobileResizable) && /Mobi|Android/i.test(navigator.userAgent)) {
      map.seriesContainer.draggable = true;
      map.seriesContainer.resizable = true;
    }
  } else {
    map.seriesContainer.draggable = false;
    map.seriesContainer.resizable = false;
    map.panBehavior = "rotateLongLat";
  } // disable zoom


  if (!im.bool(data.zoom.enabled)) {
    map.maxZoomLevel = parseFloat(data.viewport.zoomLevel);
    map.seriesContainer.events.disableType("doublehit");
    map.chartContainer.background.events.disableType("doublehit");
    map.seriesContainer.draggable = false;
    map.seriesContainer.resizable = false;
    map.chartContainer.wheelable = false;
  } else {
    // mouse wheel zoom
    map.chartContainer.wheelable = im.bool(data.zoom.wheelable); // double click zoom

    if (!im.bool(data.zoom.doubleHitZoom)) {
      map.seriesContainer.events.disableType("doublehit");
      map.chartContainer.background.events.disableType("doublehit");
    }

    if (typeof data.zoom.maxZoomLevel !== 'undefined') {
      map.maxZoomLevel = parseInt(data.zoom.maxZoomLevel);
    } // Zoom Controls


    if (im.bool(data.zoom.controls)) {
      map.zoomControl = new am4maps.ZoomControl();

      // iOS fix
      map.zoomControl.exportable = false;
      if (map.zoomControl.children && map.zoomControl.children.values) {
        map.zoomControl.children.values.forEach(function (child) {
          child.focusable = false;
        });
      }

      map.zoomControl.exportable = false;
      map.zoomControl.align = data.zoom.controlsPositions ? data.zoom.controlsPositions.align : "right";
      map.zoomControl.valign = data.zoom.controlsPositions ? data.zoom.controlsPositions.valign : "bottom";

      if (typeof data.zoom.homeButton === "undefined" || im.bool(data.zoom.homeButton)) {
        // home button
        var homeButton = new am4core.Button();
        // iOS scroll fix
        homeButton.focusable = false;
        homeButton.events.on("hit", function () {
          map.goHome();
          // in case drillDown is enabled, we hide everything else
          iMapsManager.resetDrilldown(id); 
          // reset actions
          if (typeof iMapsActions !== 'undefined' && typeof iMapsActions.resetActions !== 'undefined') {
            iMapsActions.resetActions(id);
          }
        });
        homeButton.icon = new am4core.Sprite();
        homeButton.padding(7, 5, 7, 5);
        homeButton.width = 30;
        homeButton.icon.path = "M16,8 L14,8 L14,16 L10,16 L10,10 L6,10 L6,16 L2,16 L2,8 L0,8 L8,0 L16,8 Z M16,8";
        homeButton.marginBottom = 3;
        homeButton.parent = map.zoomControl;
        homeButton.insertBefore(map.zoomControl.plusButton); // fix for Firefox homebutton not being loaded correctly

        map.events.on("inited", function () {
          homeButton.deepInvalidate();
        });
      }
    } // outside zoom controls


    if (im.bool(data.zoom.externalControls)) {
      // add home button
      iMapsManager.handleExternalZoom(data.id);
    }
  } // full screen button


  if (typeof data.fullScreen !== "undefined" && im.bool(data.fullScreen.enabled)) {
    // full screen
    var fullScreenButton = map.chartContainer.createChild(am4core.Button);
    fullScreenButton.events.on("hit", function (ev) {
      var parentMap = document.querySelector('#map_' + ev.target.icon.mapID);
      var mainParent = parentMap.closest('.map_wrapper'); 
      
      // browser fullscreen
      iMapsManager.toggleFullscreen(mainParent); 
      iMapsManager.isFullScreen = ev.target;
      
    }); 
    
    // Add button
    fullScreenButton.align = data.fullScreen.align;
    fullScreenButton.valign = data.fullScreen.valign;
    fullScreenButton.margin(5, 5, 5, 5);
    fullScreenButton.padding(5, 0, 5, 0);
    fullScreenButton.width = 30;
    fullScreenButton.icon = new am4core.Sprite();
    fullScreenButton.icon.path = iMapsManager.library.icons.goFullIconPath;
    fullScreenButton.icon.isFullScreen = false;
    fullScreenButton.icon.mapID = id; // if mobile only, add class

    if (im.bool(data.fullScreen.mobileOnly)) {
      fullScreenButton.id = '_fullscreen_button_only_mobile';
    } else {
      fullScreenButton.id = '_fullscreen_button';
    }

    // Solution for Firefox issue with button container
    map.events.on("inited", function () {
        fullScreenButton.deepInvalidate();
    });
    
  }

  // pan events?
  map.events.on("mappositionchanged",function(ev){
    // what to do here to have tooltips always display follow pan?
  });

  // zoom level changed events
  map.events.on("zoomlevelchanged", function (ev) {

    var clusterSeries = im.maps[id].clusterSeries,
      closest,
      zlevel = ev.target.zoomLevel,
      isDrill = im.bool(im.maps[id].data.drillDownOnClick),
      isDrilling = im.maps[id].isDrilling,
      drilledTo = im.maps[id].drilledTo,
      activeMap = im.filteredMap;


    // where the magic happens to show/hide series from cluster markers
    if (clusterSeries && Object.keys(clusterSeries).length) {
      Object.keys(clusterSeries).forEach(function (key) {

        // if we are drilling and this cluster belongs to the map being drilled to
        // or if the map is active
        if ( ( isDrill && clusterSeries[key].overlay ) || activeMap ) {

          // check if it's drilling and the destination is the overlay that the cluster belongs to
          // check if we are filtering and the overlay is the one the cluster belongs to 
          // !!!! we need to check for overlays from other maps that visible initially
          if ( ( isDrilling && drilledTo && parseInt(drilledTo) === parseInt(clusterSeries[key].overlay) ) 
                || parseInt( activeMap ) === parseInt(clusterSeries[key].overlay)
                || ! isDrill && ! im.bool(data.liveFilter.enabled) ) {
            
            // first we get the closest zoom value compared to the current zom
            closest = im.getClosest(clusterSeries[key].zoomLevels, zlevel);
            Object.keys(clusterSeries[key].zoomLevels).forEach(function (zkey) {
                clusterSeries[key].zoomLevels[zkey].hide();
                if (parseFloat(zkey) === closest) {
                    clusterSeries[key].zoomLevels[zkey].show();
                } else {
                    clusterSeries[key].zoomLevels[zkey].hide();

                }
            });
          } else {
            // not drilling and not active map
            Object.keys(clusterSeries[key].zoomLevels).forEach(function (zkey) {
                clusterSeries[key].zoomLevels[zkey].hide();
            });
          }

          return;
        }

        // it's not a drilldown, proceed
        // we go through the series to show the closest one and hide the others
        closest = im.getClosest(clusterSeries[key].zoomLevels, zlevel);
        Object.keys(clusterSeries[key].zoomLevels).forEach(function (zkey) {
          
            // we hide them by default
          clusterSeries[key].zoomLevels[zkey].hide();

          if (parseFloat(zkey) === closest) {
            clusterSeries[key].zoomLevels[zkey].show();
          } else {
            clusterSeries[key].zoomLevels[zkey].hide();
          }
        });
      });
    }
  });
};
/**
 * Gets closest zoom level based on current zoom and array of zoom levels for clusters
 * @param {*} zoomLevels
 * @param {*} zlevel
 */


iMapsManager.getClosest = function (zoomLevels, zlevel) {
  var closest = Object.keys(zoomLevels).reduce(function (prev, curr) {
    prev = parseInt(prev);
    curr = parseInt(curr);
    return Math.abs(curr - zlevel) < Math.abs(prev - zlevel) ? curr : prev;
  });
  return closest;
};


/* Reset drilldown behaviour */
iMapsManager.resetDrilldown = function (id) {

    var im = this,
    data = im.maps[id].data,
    allCurrentSeries = im.maps[id].series,
    allBaseSeries = im.maps[id].allBaseSeries,
    bgSeries = im.maps[id].backgroundSeries;

    if (im.bool(data.drillDownOnClick)) {
        for (var i = 0, len = allCurrentSeries.length; i < len; i++) {
          allCurrentSeries[i].hide(); //map.deltaLongitude = 0;
        }

        for (var ib = 0, lenb = allBaseSeries.length; ib < lenb; ib++) {
          // this is messing the cluster markers on base map
          allBaseSeries[ib].show();
          bgSeries.show();
        }

        iMapsManager.maps[id].drilledTo = false;
        iMapsManager.maps[id].isDrilling = false;
      }
}

/** Manually fix lat/long offset for some countries in default projections/values */


iMapsManager.latlongOffsetFix = function (data, mapObj, defaultZoom) {
  var mapSelected = data.map;
  var mapsFixInclude = ['russiaLow', 'russiaHigh', 'russiaCrimeaLow', 'russiaCrimeaHigh', 'region/world/asiaLow', 'region/world/asiaHigh', 'region/world/asiaUltra', 'region/world/asiaIndiaLow', 'region/world/asiaIndiaHigh', 'region/world/asiaIndiaUltra']; 
  
  // only do the fix if Russia is included

  if (mapsFixInclude.includes(mapSelected) && data.exclude && !data.exclude.includes('RU')) {
    mapObj.deltaLongitude = -100;

    if (defaultZoom) {
      mapObj.homeZoomLevel = 1.5;
    }
  }

  if (mapSelected === 'newZealandLow' || mapSelected == 'newZealandHigh') {
    mapObj.deltaLongitude = 20;
  }
};
/**
 * Push region series that are grouped together
 */


iMapsManager.pushGroupSeries = function (id, data) {
  var series = [];
  var regionSerie;
  data.regionGroups.forEach(function (group) {
    var newData = {},
      include = group.map(function (a) {
        return a.id;
      });

    // let's get all the options from the main map and change the group option to true
    newData = Object.assign({}, data);
    newData.regionsGroupHover = true;
    newData.regions = group;
    newData.include = include; // include only the regions we're grouping
    regionSerie = iMapsManager.pushRegionSeries(id, newData, true);
    series.push(regionSerie);
  });
  return series;
};
/**
 * Push different series of overlay/child maps
 * int i - index of the map data
 * data - object with map data to push
 */


iMapsManager.pushSeries = function (id, data) {
  var im = this,
    regionSeries,
    markerSeries,
    labelSeries,
    lineSeries,
    iconSeries,
    imageSeries,
    groupedSeries,
    clusters,
    clusterSeries = im.maps[id].clusterSeries,
    parentData = im.maps[id].data,
    seriesIndex = im.maps[id].seriesIndex,
    seriesById = im.maps[id].seriesById,
    isDrill = im.bool(im.maps[id].data.drillDownOnClick),
    cleanMapName = iMapsRouter.getCleanMapName(data.map, data.id),
    defaultSeries = false;

  if (false === cleanMapName) {
    return;
  }

  if (typeof data.id === 'undefined') {
    return;
  }

  seriesById[data.id] = [];
  
  // check if it's set a map overlay by default
  if (typeof parentData.liveFilter !== 'undefined' && parseInt( parentData.liveFilter["default"] ) !== parentData.id) {
    defaultSeries = parseInt( parentData.liveFilter["default"] );
    im.filteredMap = defaultSeries;
  } // setup series index


  if (!Array.isArray(im.maps[id].seriesIndex[data.map])) {
    im.maps[id].seriesIndex[cleanMapName] = [];
  } 
  
  // to allow empty maps to overlay, we removed the check && data.regions.length and send empty array
  if (!Array.isArray(data.regions)) {
    data.regions = [];
  }

  if (typeof parentData.allowEmpty === 'undefined') {
    parentData.allowEmpty = 0;
  } // in case we want empty maps to overlay, we remove this check.
  // in are not allowing the emtpy so that the live filter works better and markers are not hidden behind the map
  // reference: https://interactivegeomaps.com/feature/live-filter/
  // but other overlays and empty maps might need to be added...

  // we don't check if the regions.lenght exist, because maybe user wants to add an empty map, only to show divisions
  if (data.regions.length || im.bool(parentData.allowEmpty)) {
    // in case we don't allow empty, we only include the active regions
    if (!im.bool(parentData.allowEmpty)){
        data.include = [];
        data.regions.forEach(function (region, index) {
          data.include.push(region.id);
          if (!isNaN(region.id)) {
            data.include.push(parseInt(region.id));
          }
        });
    }

    regionSeries = iMapsManager.pushRegionSeries(id, data);
    seriesIndex[cleanMapName].push(regionSeries);
    seriesById[data.id].push(regionSeries);

    if (isDrill) {
      regionSeries.hidden = true;
      regionSeries.mapID = data.id;
    } 
    
    // hide in case we have a live filter and this is not the default
    if (parentData.liveFilter && im.bool(parentData.liveFilter.enabled) && defaultSeries && defaultSeries !== data.id) {
      regionSeries.hidden = true;
    } 
  } 
  
  // Check for grouped regions
  if (Array.isArray(data.regionGroups) && data.regionGroups.length) {
    groupedSeries = im.pushGroupSeries(id, data);
    groupedSeries.forEach(function (regionSerie) {
      seriesIndex[cleanMapName].push(regionSerie);
      seriesById[data.id].push(regionSerie);

      if (isDrill) {
        regionSerie.hidden = true;
        regionSerie.mapID = data.id;
      }

      // hide in case we have a live filter and this is not the default
        if (parentData.liveFilter && im.bool(parentData.liveFilter.enabled) && defaultSeries && defaultSeries !== data.id) {
            regionSerie.hidden = true;
        } 

    });
  } // let's add lines before markers, so that the markers then overlay the end of lines, in case


  if (Array.isArray(data.lines) && data.lines.length) {
    lineSeries = iMapsManager.pushLineSeries(id, data);
    seriesIndex[cleanMapName].push(lineSeries);
    seriesById[data.id].push(lineSeries);

    if (isDrill) {
      lineSeries.hidden = true;
    } // hide in case we have a live filter and this is not the default


    if (parentData.liveFilter && im.bool(parentData.liveFilter.enabled) && defaultSeries && defaultSeries !== data.id) {
      lineSeries.hidden = true;
    }
  }

  if (Array.isArray(data.roundMarkers) && data.roundMarkers.length) {
    markerSeries = iMapsManager.pushRoundMarkerSeries(id, data);
    seriesIndex[cleanMapName].push(markerSeries);
    seriesById[data.id].push(markerSeries);

    if (isDrill) {
      markerSeries.hidden = true;
      markerSeries.mapID = data.id;
    } 
    
   

    // clusters in overlay maps
    if (data.clusterMarkers && im.bool(data.clusterMarkers.enabled)) {
      markerSeries.hidden = true;
      clusters = im.setupClusters(data, id, data.id);
      clusterSeries[data.id].zoomLevels[data.clusterMarkers.zoomLevel] = markerSeries; // we setup the main index series (zoom=1) to be visible
      // when doing it inside setupClusters function, there was a bug

      if (!isDrill && clusterSeries[data.id].zoomLevels[1] ) {
        clusterSeries[data.id].zoomLevels[1].hidden = false;

        // if there's a live filter, double check - we hidde them.
        // otherwise overlaid series with clusters were always displaying, even when they shouldn't
        if (parentData.liveFilter && im.bool(parentData.liveFilter.enabled) && defaultSeries && defaultSeries !== data.id) {
            clusterSeries[data.id].zoomLevels[1].hidden = true;
        } 
      }
    }

     // hide in case we have a live filter and this is not the default
     if (parentData.liveFilter && im.bool(parentData.liveFilter.enabled) && defaultSeries && defaultSeries !== data.id) {
        markerSeries.hidden = true;
      } 
  }

  if (Array.isArray(data.iconMarkers) && data.iconMarkers.length) {
    iconSeries = iMapsManager.pushIconMarkerSeries(id, data);
    seriesIndex[cleanMapName].push(iconSeries);
    seriesById[data.id].push(iconSeries);

    if (isDrill) {
      iconSeries.hidden = true;
    } // hide in case we have a live filter and this is not the default


    if (parentData.liveFilter && im.bool(parentData.liveFilter.enabled) && defaultSeries && defaultSeries !== data.id) {
      iconSeries.hidden = true;
    }
  }

  if (Array.isArray(data.imageMarkers) && data.imageMarkers.length) {
    imageSeries = iMapsManager.pushImageMarkerSeries(id, data);
    seriesIndex[cleanMapName].push(imageSeries);
    seriesById[data.id].push(imageSeries);

    if (isDrill) {
      imageSeries.hidden = true;
    } // hide in case we have a live filter and this is not the default


    if (parentData.liveFilter && im.bool(parentData.liveFilter.enabled) && defaultSeries && defaultSeries !== data.id) {
      imageSeries.hidden = true;
    }
  }

  if (Array.isArray(data.labels) && data.labels.length) {
    labelSeries = iMapsManager.pushLabelSeries(id, data);
    seriesIndex[cleanMapName].push(labelSeries);
    seriesById[data.id].push(labelSeries);

    if (isDrill) {
      labelSeries.hidden = true;
    } // hide in case we have a live filter and this is not the default


    if (parentData.liveFilter && im.bool(parentData.liveFilter.enabled) && defaultSeries && defaultSeries !== data.id) {
      labelSeries.hidden = true;
    }
  }
};

iMapsManager.pushRegionSeries = function (id, data, groupHover) {
  var im = this,
    map = im.maps[id].map,
    // shorter reference for the map
    regionSeries,
    tooltipConfig = data.tooltip || {},
    regionTemplate,
    hover,
    active,
    highlight,
    mapVar,
    clkLabel,
    groupHover = groupHover || false;
  data = data || {};
  regionSeries = map.series.push(new am4maps.MapPolygonSeries());

  if (data.map === "custom" || im.bool(data.useGeojson)) {
    regionSeries.geodataSource.url = data.mapURL;
  } else {
    mapVar = iMapsRouter.getVarByName(data.map);
    regionSeries.geodata = window[mapVar];
  }

  regionSeries.name = data.regionLegend && data.regionLegend.title !== "" ? data.regionLegend.title : data.title;
  regionSeries.hiddenInLegend = data.regionLegend ? !im.bool(data.regionLegend.enabled) : true; // if it's a base series

  // always hide group series from legend?
  /*
  if(groupHover){
    regionSeries.hiddenInLegend = true;
  }
  */

  if (id === data.id) {
    // add it as the baseSeries - which will contain all region base series
    if( typeof im.maps[id].baseSeries === 'undefined' ){
      im.maps[id].baseSeries = [];
    }
    im.maps[id].baseSeries.push(regionSeries);
    im.maps[id].allBaseSeries.push(regionSeries);
  } 
  
  // Make map load polygon (like country names) data from GeoJSON
  regionSeries.useGeodata = true; // Exclude

  if (Array.isArray(data.exclude) && data.exclude.length) {
    regionSeries.exclude = data.exclude;
  } // Include


  if (Array.isArray(data.include) && data.include.length) {
    regionSeries.include = data.include;
  } // Setup Heatmap rules


  if (data.heatMapRegions && im.bool(data.heatMapRegions.enabled)) {
    im.setupHeatMap(regionSeries, id, data);
  } // Data
  // if (Array.isArray(data.regions)) {

  regionSeries.data = data.regions; // Configure series

  if( groupHover ){
    regionSeries.groupHover = true;
  }

  regionTemplate = regionSeries.mapPolygons.template;
  im.setupTooltip(id, regionSeries, data); // check for custom tooltip template

  if (typeof data.regionsTooltipTemplate !== 'undefined' && data.regionsTooltipTemplate.trim() !== '') {
    regionTemplate.tooltipText = data.regionsTooltipTemplate;
    regionTemplate.tooltipHTML = data.regionsTooltipTemplate;
  } else {
    regionTemplate.tooltipText = tooltipConfig.template ? tooltipConfig.template : "{tooltipContent}";
    regionTemplate.tooltipHTML = tooltipConfig.template ? tooltipConfig.template : "{tooltipContent}";
  }

  regionTemplate.adapter.add("tooltipHTML", function (value, target, key) {
    if (_typeof(target.dataItem.dataContext) === 'object' && typeof tooltipConfig.onlyWithData !== 'undefined') {
      // check if we don't return tooltip on empty regions
      if (im.bool(tooltipConfig.onlyWithData)) {
        if (target.dataItem.dataContext.madeFromGeoData === true) {
          target.tooltip.tooltipText = undefined;
          target.tooltip.tooltipHTML = undefined;
          return '';
        }
      }
    }

    if (value === "") {
      return value;
    }

    return value.replace(/\\/g, "");
  });
  regionTemplate.adapter.add("tooltipText", function (value, target, key) {
    if (_typeof(target.dataItem.dataContext) === 'object' && typeof tooltipConfig.onlyWithData !== 'undefined') {
      // check if we don't return tooltip on empty regions
      if (im.bool(tooltipConfig.onlyWithData)) {
        if (target.dataItem.dataContext.madeFromGeoData === true) {
          target.tooltip.tooltipText = undefined;
          target.tooltip.tooltipHTML = undefined;
          return '';
        }
      }
    }

    if (value === "") {
      return value;
    }

    return value.replace(/\\/g, "");
  }); // For legend color

  regionSeries.fill = data.regionDefaults.fill;
  regionTemplate.fill = data.regionDefaults.inactiveColor;
  regionTemplate.stroke = data.visual.borderColor;
  regionTemplate.strokeWidth = data.visual.borderWidth; // fill

  regionTemplate.propertyFields.fill = "fill"; 
  
  // exploring adapter
  /*
  	regionTemplate.adapter.add("fill", function(fill, target) {
  		if(Array.isArray(fill)){
        let gradient = new am4core.LinearGradient();
        fill.forEach(function(color){
          gradient.addColor(am4core.color(color));
        });
        fill = gradient;
      }
      return fill;
  	});
    */
    
  // hover - only create if it's not a group hover series

  if (!groupHover) {
    // also don't consider it on heatmaps with hover disabled
    if (typeof data.heatMapRegions !== 'undefined' && im.bool(data.heatMapRegions.enabled) && im.bool(data.heatMapRegions.noHover)) { // do nothing for now, we don't need hover, might implement something at some point
    } else {
      hover = regionTemplate.states.create("hover");
      hover.propertyFields.fill = "hover";
    } 
    //hover.propertyFields.strokeWidth = "borderWidthHover";
    //hover.propertyFields.stroke = "borderColorHover";
  } 
    
  // active state
  if (data.regionActiveState && im.bool(data.regionActiveState.enabled)) {

    active = regionTemplate.states.create("active");

    if (data.regionActiveState.source === 'custom') {
      active.properties.fill = data.regionActiveState.fill;
    } else {
      active.propertyFields.fill = "hover";
    }
  } 
  // highlight - for group hover
  highlight = regionTemplate.states.create("highlight");
  highlight.propertyFields.fill = "hover";

  if (groupHover) {
    regionTemplate.events.on("out", function (ev) {
      im.groupHoverOut(id, ev);
    });
    regionTemplate.events.on("over", function (ev) {
      im.groupHover(id, ev);
    });
    regionTemplate.events.on("hit", function (ev) {
      im.groupHit(id, ev);
    });

  } else {

    regionTemplate.events.on("hit", function (ev) {
      im.singleHit(id, ev);
    });
    regionTemplate.events.on("over", function (ev) {
      im.setupHoverEvents(id, ev);
    });
  } // Events


  // iOS bug fix, remove focus
  regionTemplate.focusable = false;

  regionTemplate.events.on("hit", function (ev) {
    im.setupHitEvents(id, ev);
  });

  // enable small map
  if (im.bool(data.smallMap)) {
    map.smallMap.series.push(regionSeries);
  } // auto Labels in progress


  if (data.regionLabels && im.bool(data.regionLabels.source)) {
    regionSeries.calculateVisualCenter = true; // Configure label series

    var labelSeries = map.series.push(new am4maps.MapImageSeries());
    var labelTemplate = labelSeries.mapImages.template.createChild(am4core.Label);
   
    // Label Background - currently only possible with global
    var background = typeof igmLabelsBackground !== 'undefined' ? igmLabelsBackground : false;
    if ( typeof background === 'object' ) {
      labelTemplate.background = new am4core.RoundedRectangle();
      labelTemplate.background.cornerRadius(...background.cornerRadius);
      labelTemplate.background.fill = am4core.color( background.color );
      labelTemplate.padding(...background.padding);
      labelTemplate.background.stroke = am4core.color( background.stroke );
    }  

    im.maps[id].labelSeries.push(labelSeries);
    
    labelTemplate.horizontalCenter = data.regionLabels.horizontalCenter;
    labelTemplate.verticalCenter = data.regionLabels.verticalCenter;
    labelTemplate.fontSize = data.regionLabels.fontSize;
    labelTemplate.fill = data.regionLabels.fill; // let's set a listener to the hide event of main series to hide this one also

    if (data.regionLabels.mobileFontSize && parseInt(data.regionLabels.mobileFontSize) !== 100) {
      if (window.innerWidth <= 780) {
        labelTemplate.fontSize = parseInt(labelTemplate.fontSize) * parseInt(data.regionLabels.mobileFontSize) / 100;
        labelTemplate.size = labelTemplate.fontSize;
      }
    }


    regionSeries.events.on("hidden", function (ev) {
      labelSeries.hide();
    });
    regionSeries.events.on("shown", function (ev) {
      labelSeries.show();
    }); // label events

    labelTemplate.events.on("hit", function (ev) {
      clkLabel = regionSeries.getPolygonById(ev.target.parent.LabelForRegion);
      clkLabel.dispatchImmediately("hit");
    });
    labelTemplate.events.on("over", function (ev) {
      iMapsManager.hover(id, ev.target.parent.LabelForRegion, false);
    });
    labelTemplate.events.on("out", function (ev) {
      iMapsManager.clearHovered(id, ev.target.parent.LabelForRegion);
    });
    im.setupTooltip(id, labelSeries, data, labelTemplate);
    labelTemplate.interactionsEnabled = true;
    labelTemplate.nonScaling = im.bool(data.regionLabels.nonScaling);
    labelSeries.hiddenInLegend = true; // fix initially hidden series - for example drilldown overlay

    regionSeries.events.on("inited", function () {
      if (regionSeries.hidden) {
        labelSeries.hide();
        labelSeries.hidden = true;
      }
    }); // set labels drag listener
    // allow labels to be dragged if in admin

    if (im.bool(data.admin)) {
      labelTemplate.draggable = true;
      labelTemplate.cursorOverStyle = am4core.MouseCursorStyle.grab;
      labelTemplate.events.on("dragstop", function (ev) {
        var svgPoint = am4core.utils.spritePointToSvg({
          x: 0,
          y: 0
        }, ev.target);
        svgPoint = am4core.utils.spritePointToSvg({
          x: 0 - ev.target.maxLeft,
          y: 0 - ev.target.maxTop
        }, ev.target);
        var geo = map.svgPointToGeo(svgPoint); // check if field to add json object with adjustments exists

        var adjField = document.querySelector("[data-depend-id=" + data.regionLabelCustomCoordinates + "]");

        if (adjField) {
          var jsonAdjustments;

          if (iMapsManager.isJSON(adjField.value)) {
            jsonAdjustments = JSON.parse(adjField.value);
          } else {
            jsonAdjustments = {};
          }

          jsonAdjustments[ev.target.parent.LabelForRegion] = {
            latitude: geo.latitude,
            longitude: geo.longitude
          };
          adjField.value = JSON.stringify(jsonAdjustments);
        }

        map.seriesContainer.draggable = im.bool(data.zoom.draggable);
        ev.target.cursorOverStyle = am4core.MouseCursorStyle.grab;
      });
      labelTemplate.events.on("down", function (ev) {
        map.seriesContainer.draggable = false;
        ev.target.cursorOverStyle = am4core.MouseCursorStyle.grabbing;
      });
    } // end dragevent
    // convert custom json position string to object


    var regionLabelCustomCoordinates = im.isJSON(data.regionLabels.regionLabelCustomCoordinates) ? JSON.parse(data.regionLabels.regionLabelCustomCoordinates) : false;
    regionSeries.events.on("inited", function () {
      regionSeries.mapPolygons.each(function (polygon) {
        var label = labelSeries.mapImages.create(),
          text; // if we're only adding labels to active regions

        if (im.bool(data.regionLabels.activeOnly) && polygon.dataItem.dataContext && typeof polygon.dataItem.dataContext.tooltipContent === "undefined") {
          return;
        } // if it's a group entry, ignore


        if (polygon.dataItem.dataContext.id.includes(',')) {
          return;
        }

        if (data.regionLabels.source === "code") {
          text = polygon.dataItem.dataContext.id.split("-").pop();
        }

        if (data.regionLabels.source === "{name}") {
          text = polygon.dataItem.dataContext.name;
        }

        if (data.regionLabels.source === "{id}") {
          text = polygon.dataItem.dataContext.id;
        }

        if (data.regionLabels.source === "custom" && typeof data.regionLabels.customSource !== "undefined") {
          text = polygon.dataItem.dataContext.autoLabel;
        }

        label.LabelForRegion = polygon.dataItem.dataContext.id; // if it was a group

        if (typeof polygon.dataItem.dataContext.originalID !== 'undefined') {
          label.groupRegion = polygon.dataItem.dataContext.originalID;
        } 
        
        // tooltip content
        label.tooltipDataItem = polygon.tooltipDataItem;
        label.tooltip = polygon.tooltip;
        label.tooltipHTML = polygon.tooltipHTML;
        label.tooltipPosition = im.bool(data.tooltip.fixed) ? "fixed" : "pointer"; // cursor style

        if (polygon.dataItem.dataContext.action && polygon.dataItem.dataContext.action !== "none") {
          label.cursorOverStyle = am4core.MouseCursorStyle.pointer;
        } // use custom coordinates adjustments or use auto position


        if (regionLabelCustomCoordinates && regionLabelCustomCoordinates.hasOwnProperty(polygon.dataItem.dataContext.id)) {
          label.latitude = regionLabelCustomCoordinates[polygon.dataItem.dataContext.id].latitude;
          label.longitude = regionLabelCustomCoordinates[polygon.dataItem.dataContext.id].longitude;
        } else {
          label.latitude = polygon.visualLatitude;
          label.longitude = polygon.visualLongitude;
        }

        if (label.children.getIndex(0)) {
          label.children.getIndex(0).text = text;
        }
      });
    });
  } // if the external dropdown is enabled, calculate visual center


  if (typeof data.externalDropdown !== 'undefined' && im.bool(data.externalDropdown.enabled)) {
    regionSeries.calculateVisualCenter = true;
  } // add this series to map series to reference it later if needed


  im.maps[id].series.push(regionSeries);
  return regionSeries;
};

iMapsManager.pushRoundMarkerSeries = function (id, data) {
  var im = this,
    map = im.maps[id].map,
    // shorter reference for the map
    markerSeries,
    markerSeriesTemplate,
    circle,
    label,
    activeState,
    highlightState,
    hoverState;

  if (Array.isArray(data.roundMarkers) && data.roundMarkers.length) {
    // Create image series
    markerSeries = map.series.push(new am4maps.MapImageSeries());
    markerSeries.name = data.roundMarkersLegend && data.roundMarkersLegend.title !== "" ? data.roundMarkersLegend.title : data.title;
    markerSeries.hiddenInLegend = data.roundMarkersLegend ? !im.bool(data.roundMarkersLegend.enabled) : false; // Create a circle image in image series template so it gets replicated to all new images

    markerSeriesTemplate = markerSeries.mapImages.template;
    circle = markerSeriesTemplate.createChild(am4core.Circle);
    im.setupTooltip(id, markerSeries, data, circle); // default values

    //iOS focus scroll bug fix
    markerSeriesTemplate.focusable = false;

    circle.radius = data.markerDefaults.radius;
    circle.fill = data.markerDefaults.fill;
    circle.stroke = am4core.color("#FFFFFF");
    circle.strokeWidth = 1;
    circle.nonScaling = typeof igmRoundMarkersNonScaling !== 'undefined' ? igmRoundMarkersNonScaling : true;
    
    // label
    label = markerSeriesTemplate.createChild(am4core.Label);
    label.text = "{label}";
    label.fill = am4core.color("#fff");
    label.verticalCenter = "middle";
    label.horizontalCenter = "middle";
    label.nonScaling = typeof igmRoundMarkersNonScaling !== 'undefined' ? igmRoundMarkersNonScaling : true;
    label.fontSize = typeof igmClusterMarkerFontSize !== 'undefined' ? igmClusterMarkerFontSize : data.markerDefaults.radius;
    label.clickable = false;
    label.focusable = false;
    label.hoverable = false; 
    
    // check for custom tooltip template
    if (typeof data.roundMarkersTooltipTemplate !== 'undefined' && data.roundMarkersTooltipTemplate.trim() !== '') {
      circle.tooltipText = data.roundMarkersTooltipTemplate;
      circle.tooltipHTML = data.roundMarkersTooltipTemplate;
    } else {
      circle.tooltipText = data.tooltip && data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
      circle.tooltipHTML = data.tooltip && data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
    }

    circle.propertyFields.tooltipText = "tooltipTemplate";
    circle.propertyFields.tooltipHTML = "tooltipTemplate"; // fill can only be set if heatmap is not enabled and not a range

    if (data.heatMapMarkers && im.bool(data.heatMapMarkers.enabled)) {
      im.setupHeatMap(markerSeries, id, data); // setup the fill and radius if type is range

      if (typeof data.heatMapMarkers.type !== 'undefined' && data.heatMapMarkers.type === 'range') {
        circle.propertyFields.radius = "radius";
        circle.propertyFields.fill = "fill";
      }
    } else {
      circle.propertyFields.radius = "radius";
      circle.propertyFields.fill = "fill";
    }

    circle.propertyFields.userClassName = "customClass"; // Set property fields

    markerSeriesTemplate.propertyFields.radius = "radius";
    markerSeriesTemplate.propertyFields.latitude = "latitude";
    markerSeriesTemplate.propertyFields.longitude = "longitude";
    markerSeriesTemplate.setStateOnChildren = true; // hover & active

    hoverState = circle.states.create("hover");
    hoverState.properties.fill = data.hover;
    hoverState.propertyFields.fill = "hover"; // active

    activeState = circle.states.create("active");
    activeState.properties.fill = data.hover;
    activeState.propertyFields.fill = "hover"; // highlight - for legend hover

    highlightState = circle.states.create("highlight");
    highlightState.properties.fill = data.hover;
    highlightState.propertyFields.fill = "hover"; // text label below

    if (data.roundMarkerLabels && im.bool(data.roundMarkerLabels.enabled)) {

      var markerLabel = markerSeriesTemplate.createChild(am4core.Label);
      markerLabel.text = typeof data.roundMarkerLabels.source !== "undefined" && data.roundMarkerLabels.source !== '' ? data.roundMarkerLabels.source : "{name}";
      markerLabel.horizontalCenter = "middle";
      markerLabel.fontSize = data.roundMarkerLabels.fontSize;

      //for mobile devices
      if (data.roundMarkerLabels.mobileSize && parseInt(data.roundMarkerLabels.mobileSize) !== 100) {
        if (window.innerWidth <= 780) {
          markerLabel.fontSize = parseInt(data.roundMarkerLabels.fontSize) * parseInt(data.roundMarkerLabels.mobileSize) / 100;
        }
      }

      markerLabel.nonScaling = typeof igmRoundMarkersNonScaling !== 'undefined' ? igmRoundMarkersNonScaling : true; 
      //im.bool(data.roundMarkerLabels.nonScaling);

      markerLabel.fill = data.roundMarkerLabels.fill;
      markerLabel.clickable = false;
      markerLabel.focusable = false;
      markerLabel.hoverable = false;
      markerLabel.padding(0, 0, 0, 0);
      markerLabel.propertyFields.paddingTop = "radius";
      /*
      markerLabel.adapter.add("dy", function (dy, target) {
      	var circle = target.parent.children.getIndex(0);
      	return circle.pixelRadius;
      });
      */
    } // Add data


    markerSeries.data = data.roundMarkers; // For legend color

    markerSeries.fill = data.markerDefaults.fill; // Events

    markerSeriesTemplate.events.on("hit", function (ev) {
      im.singleHit(id, ev);
      im.setupHitEvents(id, ev);
    });
    markerSeriesTemplate.events.on("over", function (ev) {
      im.setupHoverEvents(id, ev);
    });
  } // enable small map


  if (data.zoom && data.zoom.smallMap && im.bool(data.zoom.smallMap)) {
    map.smallMap.series.push(markerSeries);
  } // add this series to map series to reference it later if needed


  im.maps[id].series.push(markerSeries); // if part of the parent map

  if (id === data.id) {
    // only add as base if not a cluster
    if (data.clusterMarkers && !im.bool(data.clusterMarkers.enabled)) {
      im.maps[id].allBaseSeries.push(markerSeries);
    } else if (data.clusterMarkers && im.bool(data.clusterMarkers.enabled)) { // it's a cluster, so the main series with all markers
      // markerSeries.hidden = true;
    }
  }

  return markerSeries;
};

iMapsManager.pushImageMarkerSeries = function (id, data) {
  var im = this,
    map = im.maps[id].map,
    // shorter reference for the map
    markerSeries,
    markerSeriesTemplate,
    imageMarker;

  if (Array.isArray(data.imageMarkers) && data.imageMarkers.length) {
    // Create image series
    markerSeries = map.series.push(new am4maps.MapImageSeries());
    markerSeries.name = data.imageMarkersLegend && data.imageMarkersLegend.title !== "" ? data.imageMarkersLegend.title : data.title;
    markerSeries.hiddenInLegend = data.imageMarkersLegend ? !im.bool(data.imageMarkersLegend.enabled) : false; // Create a circle image in image series template so it gets replicated to all new images

    markerSeriesTemplate = markerSeries.mapImages.template;

    //iOS focus scroll bug fix
    markerSeriesTemplate.focusable = false;

    imageMarker = markerSeriesTemplate.createChild(am4core.Image);
    imageMarker.propertyFields.href = "href";
    imageMarker.propertyFields.width = "size";
    imageMarker.propertyFields.height = "size";
    imageMarker.propertyFields.userClassName = "customClass"; //imageMarker.propertyFields.height = "height";

    imageMarker.propertyFields.horizontalCenter = "horizontalCenter";
    imageMarker.propertyFields.verticalCenter = "verticalCenter";
    imageMarker.nonScaling = true;
    imageMarker.propertyFields.nonScaling = "nonScaling";
    im.setupTooltip(id, markerSeries, data, imageMarker); // to have tooltip display above the image
    //imageMarker.tooltipX = am4core.percent(50);
    //imageMarker.tooltipY = am4core.percent(0);
    // check for custom tooltip template

    if (typeof data.imageMarkersTooltipTemplate !== 'undefined' && data.imageMarkersTooltipTemplate.trim() !== '') {
      imageMarker.tooltipText = data.imageMarkersTooltipTemplate;
      imageMarker.tooltipHTML = data.imageMarkersTooltipTemplate;
    } else {
      imageMarker.tooltipText = data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
      imageMarker.tooltipHTML = data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
    } // Set property fields


    markerSeriesTemplate.propertyFields.latitude = "latitude";
    markerSeriesTemplate.propertyFields.longitude = "longitude"; // Add data for the three cities

    markerSeries.data = data.imageMarkers; // Events

    markerSeriesTemplate.events.on("hit", function (ev) {
      im.singleHit(id, ev);
      im.setupHitEvents(id, ev);
    });
    markerSeriesTemplate.events.on("over", function (ev) {
      im.setupHoverEvents(id, ev);
    });
  } // enable small map


  if (data.zoom && data.zoom.smallMap && im.bool(data.zoom.smallMap)) {
    map.smallMap.series.push(markerSeries);
  } // add this series to map series to reference it later if needed


  im.maps[id].series.push(markerSeries);
  return markerSeries;
};

iMapsManager.pushIconMarkerSeries = function (id, data) {
  var im = this,
    map = im.maps[id].map,
    // shorter reference for the map
    markerSeries,
    markerSeriesTemplate,
    icon,
    hover,
    active,
    highlightState,
    label,
    clickableOverlay;

  if (Array.isArray(data.iconMarkers) && data.iconMarkers.length) {
    // Create image series
    markerSeries = map.series.push(new am4maps.MapImageSeries());
    markerSeries.hiddenInLegend = data.iconMarkersLegend ? !im.bool(data.iconMarkersLegend.enabled) : false;
    markerSeries.name = data.iconMarkersLegend && data.iconMarkersLegend.title !== "" ? data.iconMarkersLegend.title : data.title;
    markerSeriesTemplate = markerSeries.mapImages.template;
    markerSeriesTemplate.nonScaling = true;
    markerSeriesTemplate.setStateOnChildren = true; //apply parent's current state to children

    //iOS focus scroll bug fix
    markerSeriesTemplate.focusable = false;

    markerSeriesTemplate.states.create('hover'); //create dummy state for hover
    // Create a circle image in image series template so it gets replicated to all new images

    icon = markerSeriesTemplate.createChild(am4core.Sprite);
    icon.propertyFields.scale = "scale";
    icon.propertyFields.path = "path";
    icon.propertyFields.rotation = "rotation";

    /*
    if (typeof data.iconMarkersTooltipTemplate !== 'undefined' && data.iconMarkersTooltipTemplate.trim() !== '') {
    	icon.tooltipText = data.iconMarkersTooltipTemplate;
    	icon.tooltipHTML = data.iconMarkersTooltipTemplate;
    } else {
    	icon.tooltipText = data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
    	icon.tooltipHTML = data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
    }*/

    icon.propertyFields.horizontalCenter = "horizontalCenter";
    icon.propertyFields.verticalCenter = "verticalCenter";
    icon.propertyFields.fill = "fill"; // For legend color

    markerSeries.fill = data.iconMarkerDefaults.fill; // clickable overlay

    clickableOverlay = markerSeriesTemplate.createChild(am4core.Sprite);
    clickableOverlay.propertyFields.scale = "scale";
    clickableOverlay.path = "M-10,0a10,10 0 1,0 20,0a10,10 0 1,0 -20,0";
    clickableOverlay.opacity = 0;
    clickableOverlay.propertyFields.horizontalCenter = "horizontalCenter";
    clickableOverlay.propertyFields.verticalCenter = "verticalCenter";
    clickableOverlay.tooltipText = data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
    clickableOverlay.tooltipHTML = data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
    im.setupTooltip(id, markerSeries, data, clickableOverlay);

    if (data.iconMarkerLabels && im.bool(data.iconMarkerLabels.enabled)) {
      var markerLabel = markerSeriesTemplate.createChild(am4core.Label);
      markerLabel.text = typeof data.iconMarkerLabels.source !== "undefined" && data.iconMarkerLabels.source !== '' ? data.iconMarkerLabels.source : "{name}";
      markerLabel.horizontalCenter = "middle";
      markerLabel.verticalCenter = "top";
      markerLabel.fontSize = data.iconMarkerLabels.fontSize;
      markerLabel.nonScaling = false;
      markerLabel.fill = data.iconMarkerLabels.fill;
      markerLabel.clickable = false;
      markerLabel.focusable = false;
      markerLabel.hoverable = false;
      markerLabel.padding(0, 0, 0, 0);
      markerLabel.adapter.add("dy", function (dy, target) {
        var icon = target.parent.children.getIndex(0);
        var scale = icon.scale;
        var space;

        if (icon.verticalCenter === 'middle') {
          space = 10 * scale;
        }

        if (icon.verticalCenter === 'bottom') {
          space = 0;
        }

        if (icon.verticalCenter === 'top') {
          space = 20 * scale + 5;
        } // formula to get the label to distance at an accepted level


        return space;
      });
    } // hover & active


    hover = icon.states.create("hover");
    hover.propertyFields.fill = "hover";
    active = icon.states.create("active");
    active.propertyFields.fill = "hover"; // highlight - for legend hover

    highlightState = icon.states.create("highlight");
    highlightState.properties.fill = data.hover;
    highlightState.propertyFields.fill = "hover"; // Set property fields

    markerSeriesTemplate.propertyFields.latitude = "latitude";
    markerSeriesTemplate.propertyFields.longitude = "longitude"; // Add data

    markerSeries.data = data.iconMarkers; // Events

    markerSeriesTemplate.events.on("hit", function (ev) {
      im.singleHit(id, ev);
      im.setupHitEvents(id, ev);
    });
    markerSeriesTemplate.events.on("over", function (ev) {
      im.setupHoverEvents(id, ev);
    });
  } // enable small map


  if (data.zoom && data.zoom.smallMap && im.bool(data.zoom.smallMap)) {
    map.smallMap.series.push(markerSeries);
  } // add this series to map series to reference it later if needed


  im.maps[id].series.push(markerSeries);
  return markerSeries;
};


iMapsManager.pushLineSeries = function (id, data) {
  var im = this,
    map = im.maps[id].map,
    // shorter reference for the map
    lineSeries = {},
    lineData = []; // Lines

  if (Array.isArray(data.lines) && data.lines.length) {
    // Add line series
    if (data.projection === "Orthographic") {
      lineSeries = map.series.push(new am4maps.MapLineSeries());
      lineSeries.mapLines.template.propertyFields.shortestDistance = true;
    } else {
      lineSeries = map.series.push(new am4maps.MapArcSeries());
      lineSeries.mapLines.template.line.propertyFields.controlPointDistance = "curvature";
      lineSeries.mapLines.template.line.controlPointPosition = 0.5;
    }

    lineSeries.name = data.linesLegend && data.linesLegend.title !== "" ? data.linesLegend.title : data.title;
    lineSeries.hiddenInLegend = data.linesLegend ? !im.bool(data.linesLegend.enabled) : false;
    lineSeries.mapLines.template.nonScalingStroke = true;
    lineSeries.mapLines.template.propertyFields.strokeWidth = "strokeWidth";
    lineSeries.mapLines.template.propertyFields.strokeDasharray = "strokeDash";
    lineSeries.mapLines.template.propertyFields.stroke = "stroke";

    // arrow
    lineSeries.mapLines.template.arrow.position = 1; // 1 is the end
    lineSeries.mapLines.template.arrow.nonScaling = true;
    lineSeries.mapLines.template.arrow.propertyFields.fill = "stroke";
    lineSeries.mapLines.template.arrow.horizontalCenter = "middle";
    //lineSeries.mapLines.template.arrow.tooltipHTML = "{title}";

    //lineSeries.mapLines.template.arrow.disabled = true;
    lineSeries.mapLines.template.arrow.propertyFields.disabled = "arrowDisabled";

    data.lines.forEach(function (lineObj, index) {
      // make sure multiGeoLine is array of arrays:
      lineObj.multiGeoLine = [lineObj.multiGeoLine];
      lineData.push(lineObj);
    });      
    
    // events - to do
    lineSeries.mapLines.template.events.on("hit", function (ev) {
      im.singleHit(id, ev);
      im.setupHitEvents(id, ev);
    });
    lineSeries.mapLines.template.events.on("over", function (ev) {
      im.setupHoverEvents(id, ev);
    });

    lineSeries.data = lineData; 
    
    // enable small map
    if (data.zoom && data.zoom.smallMap && im.bool(data.zoom.smallMap)) {
      map.smallMap.series.push(lineSeries);
    } //let's hide this from legend, since they don't group in the same Series
    //lineSeries.hiddenInLegend = true;
    // add this series to map series to reference it later if needed


    im.maps[id].series.push(lineSeries); // For legend color

    lineSeries.fill = data.lineDefaults.stroke; // if it's part of the parent map

    if (id === data.id) {
      im.maps[id].allBaseSeries.push(lineSeries);
    }
  }

  return lineSeries;
};



iMapsManager.pushLabelSeries = function (id, data) {
  var im = this,
    map = im.maps[id].map,
    // shorter reference for the map
    labelSeries,
    labelSeriesTemplate,
    label,
    activeState,
    highlightState,
    hoverState,
    background = false;

  if (Array.isArray(data.labels) && data.labels.length) {
    // Create image series
    labelSeries = map.series.push(new am4maps.MapImageSeries());
    labelSeries.name = data.roundMarkersLegend && data.roundMarkersLegend.title !== "" ? data.roundMarkersLegend.title : data.title;
    labelSeries.hiddenInLegend = data.roundMarkersLegend ? !im.bool(data.roundMarkersLegend.enabled) : false; 
    
    labelSeriesTemplate = labelSeries.mapImages.template;
    labelSeriesTemplate.setStateOnChildren = true; 

    // label
    label = labelSeriesTemplate.createChild(am4core.Label);
    label.text = "{id}";

    //label.nonScaling = true;
    label.nonScaling = typeof igmLabelsNonScaling !== 'undefined' ? igmLabelsNonScaling : true;

    if (data.labelStyle) {
      label.fontFamily = data.labelStyle.fontFamily;
      label.fontWeight = data.labelStyle.fontWeight;
    }


    label.horizontalCenter = data.labelPosition.horizontalCenter;
    label.verticalCenter = data.labelPosition.verticalCenter;
    label.propertyFields.fill = "fill";
    label.propertyFields.fontSize = "fontSize";
    im.setupTooltip(id, labelSeries, data, label); // custom fields

    if (typeof data.labelsTooltipTemplate !== 'undefined' && data.labelsTooltipTemplate.trim() !== '') {
      label.tooltipText = data.labelsTooltipTemplate;
      label.tooltipHTML = data.labelsTooltipTemplate;
    } else {
      label.tooltipText = data.tooltip && data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
      label.tooltipHTML = data.tooltip && data.tooltip.template ? data.tooltip.template : "{tooltipContent}";
    } 
    
    // Set property fields
    labelSeriesTemplate.propertyFields.latitude = "latitude";
    labelSeriesTemplate.propertyFields.longitude = "longitude";
    labelSeriesTemplate.propertyFields.fill = "fill";
    labelSeriesTemplate.propertyFields.fontSize = "fontSize";
    label.propertyFields.verticalCenter = "verticalCenter";
    label.propertyFields.horizontalCenter = "horizontalCenter"; 
    

    // labels background - currently only possible through global
    background = typeof igmLabelsBackground !== 'undefined' ? igmLabelsBackground : false;
    if ( typeof background === 'object' ) {
      label.background = new am4core.RoundedRectangle();
      label.background.cornerRadius(...background.cornerRadius);
      label.background.fill = am4core.color( background.color );
      label.padding(...background.padding);
      label.background.stroke = am4core.color( background.stroke );
    }    

    // hover & active
    hoverState = label.states.create("hover");
    hoverState.properties.fill = data.hover;
    hoverState.propertyFields.fill = "hover"; // active

    activeState = label.states.create("active");
    activeState.properties.fill = data.hover;
    activeState.propertyFields.fill = "hover"; // highlight - for legend hover

    highlightState = label.states.create("highlight");
    highlightState.properties.fill = data.hover;
    highlightState.propertyFields.fill = "hover"; // Add data

    labelSeries.data = data.labels; // For legend color

    labelSeries.fill = data.labelDefaults.fill; // Events

    labelSeriesTemplate.events.on("hit", function (ev) {
      im.singleHit(id, ev);
      im.setupHitEvents(id, ev);
    });
    labelSeriesTemplate.events.on("over", function (ev) {
      im.setupHoverEvents(id, ev);
    });
  } // enable small map


  if (data.zoom && data.zoom.smallMap && im.bool(data.zoom.smallMap)) {
    map.smallMap.series.push(labelSeries);
  } // add this series to map series to reference it later if needed

  im.maps[id].labelSeries.push(labelSeries);
  im.maps[id].series.push(labelSeries); // if part of the parent map

  if (id === data.id) {
    im.maps[id].allBaseSeries.push(labelSeries);
  }

  return labelSeries;
};

iMapsManager.setupTooltip = function (id, series, data, marker) {
  var im = this,
    tooltip = data.tooltip,
    map = im.maps[id].map,
    shadow;
  marker = marker || false; // don't include it in export
  // regionSeries.tooltip.exportable = false;
  // default behaviour

  if (typeof data.tooltip === "undefined") {
    series.tooltip.disabled = false;
    series.tooltip.getFillFromObject = false;
    series.tooltip.getStrokeFromObject = false;
    series.tooltip.label.fill = am4core.color("#000000");
    series.tooltip.background.fill = am4core.color("#FFFFFF");
    return;
  }

  if (!im.bool(data.tooltip.enabled)) {
    series.tooltip.disabled = true;
    return series;
  }

  // if it's disabled on mobile/smaller screens
  if (_typeof(data.tooltip.disableMobile) !== undefined && im.bool(data.tooltip.disableMobile) && window.innerWidth <= 780) {
    series.tooltip.disabled = true;
    return series;
  }



  //if it's overlay, it might have a custom config

  // tooltip settings from map config
  series.tooltip.label.interactionsEnabled = im.bool(tooltip.fixed);
  series.tooltip.background.cornerRadius = tooltip.cornerRadius;
  if( tooltip.pointerLength ){
    series.tooltip.background.pointerLength = parseInt( tooltip.pointerLength );
  }
  series.tooltip.getFillFromObject = false;
  series.tooltip.getStrokeFromObject = false;
  series.tooltip.label.fill = tooltip.color;
  series.tooltip.background.fill = tooltip.backgroundColor;
  series.tooltip.fontFamily = tooltip.fontFamily;
  series.tooltip.fontSize = tooltip.fontSize;
  series.tooltip.fontWeight = tooltip.fontWeight;

  // animation - disable the fly out effect on markers
  series.tooltip.animationDuration = 0;

  // border
  if (tooltip.borderColor !== 'undefined') {
    series.tooltip.background.stroke = tooltip.borderColor;
    series.tooltip.background.strokeWidth = tooltip.borderWidth;
  }

  if (typeof tooltip.maxWidth !== 'undefined' && tooltip.maxWidth !== '') {
    series.tooltip.maxWidth = parseInt(tooltip.maxWidth);
    series.tooltip.contentWidth = parseInt(tooltip.maxWidth);
  }

  // box-shadow
  if (typeof tooltip.customShadow !== 'undefined' && im.bool(tooltip.customShadow)) {
    shadow = series.tooltip.background.filters.getIndex(0);
    shadow.dx = tooltip.customShadowOpts.dx;
    shadow.dy = tooltip.customShadowOpts.dy;
    shadow.blur = tooltip.customShadowOpts.blur;
    shadow.opacity = tooltip.customShadowOpts.opacity;
    shadow.color = tooltip.customShadowOpts.color;
  }

  // Set up fixed tooltips
  if (im.bool(tooltip.fixed)) {
    if (series.mapPolygons) {
      series.calculateVisualCenter = true;
      series.mapPolygons.template.tooltipPosition = "fixed";

      // in case it's a group and the tooltip is set to fixed, 
      // we need to set the tooltip hover to have the regions keep the hover state
      if( series.groupHover ){

         // set to false so that the tooltip does not 
        // inherit the highlight state color on hover
        series.tooltip.getFillFromObject = false;
        series.tooltip.events.on('over', function(ev) {
          ev.target.dataItem.component.mapPolygons.each(function(polygon) {
            polygon.setState("highlight");
          }); 
        });
        series.tooltip.events.on('out', function(ev) {
          ev.target.dataItem.component.mapPolygons.each(function(polygon) {
            polygon.setState("default");
          }); 
        });

      }

      series.tooltip.keepTargetHover = true;

      if (tooltip.showTooltipOn) {
        series.mapPolygons.template.showTooltipOn = tooltip.showTooltipOn;

        // to have the tooltip in the marker fixed, even after over out
        if (tooltip.showTooltipOn === 'hit') {
          series.tooltip.keepTargetHover = false;
        }
      }
    } else {
      // for markers
      series.mapImages.template.tooltipPosition = "fixed";
      series.tooltip.keepTargetHover = true;

      if (tooltip.showTooltipOn && marker) {
        series.mapImages.template.showTooltipOn = tooltip.showTooltipOn;
        marker.showTooltipOn = tooltip.showTooltipOn;

        // to have the tooltip in the marker fixed, even after over out
        if (tooltip.showTooltipOn === 'hit') {
          series.tooltip.keepTargetHover = false;
        }

        // to have the tooltip display after the map loads
        if (tooltip.showTooltipOn === 'always') {
          map.events.on("appeared", function () {
            marker.clones.each(function (clone) {
              clone.showTooltip();
            });
          });

          map.events.on("mappositionchanged",function(ev){
            marker.clones.each(function(clone){
              clone.showTooltip();
            })
          });

        }
      }
    }
  }
  return series;
};

iMapsManager.prepareURL = function (str) {

  if (typeof str !== "string") {
      return str;
  }

  // parse html entities
  str = str.replace(/&amp;/gi, '&');
  str.replace(/&#(\d+);/g, function (match, dec) {
      return String.fromCharCode(dec);
  });

  // check if allowed url
  var url, protocols;
  try {
      url = new URL(str);
  } catch (_) {
      url = false;
  }

  // check if valid url. If string it might be a anchor or relative path, so keep going
  if (url) {
      // acepted protocols
      protocols = [null, "http:", "https:", "mailto:", "tel:"];
      if (!protocols.includes(url.protocol)) {
          console.log('URL protocol not allowed');
          return '';
      }
  }

  return str;

};

iMapsManager.setupHitEvents = function (id, ev) {
  var im = this,
    data = im.maps[id].data,
    dataContext,
    map = im.maps[id].map,
    customActionName,
    targetType = im.getTargetSeriesType(ev.target),
    clicked = im.maps[id].clicked || false,
    zoomCluster = data.clusterMarkers ? parseFloat(data.clusterMarkers.zoomLevel) : 1,
    currentRegion, mapFunction, 
    container = document.getElementById(data.container),
    event = new Event("mapEntryClicked");

    if (ev.target.isLabels) {
        dataContext = ev.target.dataItems.first.dataContext;
    } else {
        dataContext = ev.target.dataItem.dataContext;
    } 

    // on certain devices, the click gets triggered twice, so the 'hold click actions' won't work properly
    // but we minize the issue by allowing it to run anyway
    if( map.lastClickedEntry === ev.target && ! im.maps[id].clicked ){
        // the clicked is used to control the "hold click actions" feature
        im.maps[id].clicked = dataContext;
        return;
    }

    // we reset it to null, in case it was clicked before, double tap
    im.maps[id].clicked = null;
    map.lastClickedEntry = ev.target;
    container.dispatchEvent(event);

  // if it's a cluster, zoom in a bit
  if (targetType === 'MapImage') {
    if (dataContext.cluster) {
      // if we're far from the max, let's just zoom half
      if (zoomCluster - parseInt(map.zoomLevel) > 5) {
        zoomCluster = parseInt(map.zoomLevel) + (zoomCluster/2);
      }
      ev.target.series.chart.zoomToMapObject(ev.target, zoomCluster);
    }
  } // for admin log


  console.log(dataContext); // Zoom on click

  if (data.zoom && im.bool(data.zoom.enabled) && im.bool(data.zoom.zoomOnClick)) {
    ev.zooming = true;
    im.zoomToRegion(ev, id);
  } // drill down


  if (targetType === "MapPolygon" && im.bool(data.drillDownOnClick)) {
    im.drillDown(id, ev);
  }

  if (dataContext.madeFromGeoData) {

    // check if we should clear selected or not?
    // iMapsManager.clearSelected(id);

    mapFunction = 'igm_inactive_' + data.id;

    if (typeof window[mapFunction] === 'function') {
      data = window[mapFunction](data);
    }

    return;
  }

  // if it's a marker with action to open specific map
  if (targetType === "MapImage" && _typeof(dataContext.action) !== undefined && dataContext.action === 'igm_display_map') {
    if (Array.isArray(iMapsManager.maps[id].seriesById[parseInt(dataContext.content)])) {
      currentRegion = iMapsManager.maps[id].seriesById[parseInt(dataContext.content)];
      im.drillTo(id, ev, currentRegion, true);
    }
  } // if admin, we don't trigger the actions


  if (im.bool(data.admin)) {
    return;
  } // if we need to hold action on mobile/smaller screens to show tooltip


  if (data.tooltip && _typeof(data.tooltip.enabled) !== undefined && im.bool(data.tooltip.enabled) && _typeof(data.tooltip.disableMobile) !== undefined && !im.bool(data.tooltip.disableMobile) && _typeof(data.tooltip.holdAction) !== undefined && im.bool(data.tooltip.holdAction) && window.innerWidth <= 780) {
    if ( ! clicked || clicked !== dataContext ) {
      console.log('Holding action for second tap.');
      im.maps[id].clicked = dataContext;
      return;
    } 
    
    // in case the previously clicked will now perform action, we reset it
    if (clicked === dataContext) {
      im.maps[id].clicked = false;
    } // if it's a different one, set it normally
    else {
      im.maps[id].clicked = dataContext;
    }
  }


  if (dataContext.action === "none") {
    // do nothing
    return;   
  } 

  // check urls
  if ( dataContext.action === "open_url" || dataContext.action === "open_url_new" ) {
    dataContext.content = iMapsManager.prepareURL(dataContext.content);
  }
  // open new url
  if (dataContext.action === "open_url" && dataContext.content !== "") {
    document.location = dataContext.content;
  } else if (dataContext.action === "open_url_new" && dataContext.content !== "") {
    window.open(dataContext.content);
  } // custom
  else if (dataContext.action && dataContext.action.includes("custom")) {
    customActionName = dataContext.action + "_" + dataContext.mapID;

    if (typeof window[customActionName] !== "undefined") {
      window[customActionName](dataContext);
    }
  } else {
    if (typeof window[dataContext.action] !== "undefined") {
      window[dataContext.action](id, dataContext);
    }
  }
};


iMapsManager.zoomToMap = function (ev, target, id) {
  var im = this;
  var baseMap = false;

  target.forEach(function (series) {
    if ("MapPolygonSeries" == im.getTargetSeriesType(series)) {
      baseMap = series;
      return;
    }
  });

  if (baseMap) {
    ev.target.series.chart.zoomToRectangle(baseMap.north, baseMap.east, baseMap.south, baseMap.west, 1, true);
    console.log('zoomed to specific map');
    return;
  }
  console.log('failed to zoom to specific map');
};

iMapsManager.zoomToRegion = function (ev, id) {
  var im = this,
    seriesType = im.getTargetSeriesType(ev.target),
    data = im.maps[id].data,
    map = im.maps[id].map,
    markerZoomLevel,
    dataContext; // do nothing if we clicked a label

  if (ev.target.isLabels) {
    return;
  }

  dataContext = ev.target.dataItem.dataContext; // if it's a marker, handle it differently

  if (seriesType == "MapImage") {
    // if it's a cluster marker
    if (dataContext.cluster) { //do nothing, we already zoomed
    } else {
      markerZoomLevel = typeof igmMarkerZoomLevelOnClick !== 'undefined' ? igmMarkerZoomLevelOnClick : ev.target.parent.chart.zoomLevel * 2;
      ev.target.series.chart.zoomToMapObject(ev.target, markerZoomLevel, true);
    }
  } else {
    if (dataContext.id === "asia") {
      ev.target.series.chart.deltaLongitudeOriginal = ev.target.series.chart.deltaLongitude;
      ev.target.series.chart.deltaLongitude = -10;
      ev.target.series.chart.zoomToGeoPoint({
        latitude: 34.076842,
        longitude: 100.693068
      }, 1.7, true);
    } else if (dataContext.id === "northAmerica" && data.map.startsWith('region/world/worldRegion')) {
      ev.target.series.chart.zoomToGeoPoint({
        latitude: 55.5,
        longitude: -105.5
      }, 3, true);
    } else if (dataContext.id === "ZA" && data.map.startsWith('world')) {
      ev.target.series.chart.zoomToGeoPoint({
        latitude: -28.6,
        longitude: 24.7
      }, 12.2, true);
    } else {
      if (typeof ev.target.series.chart.deltaLongitudeOriginal !== 'undefined') {
        ev.target.series.chart.deltaLongitude = ev.target.series.chart.deltaLongitude;
      }

      ev.target.series.chart.zoomToMapObject(ev.target, ev.target.zoomLevel * 2);
    }
  }
};

iMapsManager.setupHoverEvents = function (id, ev) {
  var im = this,
    selected = im.maps[id].selected || false,
    dataContext;

  if (ev.target.isLabels) {
    dataContext = ev.target.dataItems.first.dataContext;
  } else {
    dataContext = ev.target.dataItem.dataContext;
  }

  // when tooltip position is fixed, hovering regions might leave hovered state behind, when the overout goes through the tooltip
  if (ev.target.className !== 'MapImage' && ev.target.tooltipPosition === 'fixed') {

    if ( Array.isArray(im.tempHover) && im.tempHover.length >= 0) {
      im.tempHover[0].setState("default");
      im.tempHover = [];
    }
    im.tempHover = [ev.target];
  }


  if (dataContext.action && dataContext.action !== "none") {
    ev.target.cursorOverStyle = am4core.MouseCursorStyle.pointer;
  }

  if (Array.isArray(selected) && !selected.includes(ev.target) && ev.target.dataItem && typeof ev.target.dataItem.dataContext.madeFromGeoData === 'undefined' ) {
    selected.forEach(function (sel, index) {
      if (typeof sel === 'object' && typeof sel.isHover !== 'undefined') {
        sel.isHover = false;
      }
    });
  }

 
  // we exclude touch devices, since the hover event is also triggered on tap, otherwise we have 2 select events triggered
  if (im.bool(dataContext.triggerClickOnHover) && (!iMapsManager.isTouchScreendevice() || ev.type === 'over') && (typeof iMaps.maps[id].mapClicked === 'undefined' || iMaps.maps[id].mapClicked === false)) {
    iMapsManager.select(id, dataContext.id, false, true, dataContext.mapID, false);
  }

  // if it's a marker and we want to trigger hover event also on associated regions in marker value
  if(im.bool(dataContext.triggerRegionHover) && dataContext.val && dataContext.val !== '' ){
    iMapsManager.hover(id, dataContext.val );
    ev.target.events.on("out",function(ev){
      iMapsManager.clearHovered(id);
    });
  }
};

iMapsManager.singleHit = function (id, ev) {
  var im = this,
    dataContext,
    data = im.maps[id].data;

  if (ev.target.isLabels) {
    dataContext = ev.target.dataItems.first.dataContext;
  } else {
    dataContext = ev.target.dataItem.dataContext;
  }

  if (dataContext.madeFromGeoData) {
    return;
  }

  if (iMaps.maps[id].activeStateControl === true && dataContext.activeState) {
    iMaps.maps[id].mapClicked = true;
  }


  // causes issues on tap on mobile, we need to click twice? 
  // seems the clear selected is affecting

  // update:
  // let's try passing second argument as false if tooltip is set to display on click?
  let keepThis = true;
  if( data.tooltip && data.tooltip.showTooltipOn === 'hit' ){
    keepThis = false;
  }
  iMapsManager.clearSelected(id, keepThis, true );

  ev.target.isActive = true;
  ev.target.isHover = true;
  ev.target.setState("active");
  im.maps[id].selected = [ev.target];
};

iMapsManager.groupHit = function (id, ev) {
  var im = this,
    selected = im.maps[id].selected || false;

  if (ev.target.dataItem.dataContext.madeFromGeoData) {
    return;
  }

  if (iMaps.maps[id].activeStateControl === true && ev.target.dataItem.dataContext.activeState) {
    iMaps.maps[id].mapClicked = true;
  }

  //removed to improve click on group ?
  iMapsManager.clearSelected(id);

  selected = [];

  ev.target.parent.mapPolygons.each(function (polygon) {

    // added the typeof contorl to only run code on original clicked/hit entry and not the individual shape
    // removed, because the code wasn't runnning otherwise -> && typeof polygon.dataItem.dataContext.originalID === 'undefined'
    if (!polygon.dataItem.dataContext.madeFromGeoData ) {
      // toggle active state
      polygon.setState("active");

      if( ev.target === polygon ) {
        polygon.isHover = true;
      }

      //removed to fix bug when hovering groups after clicked ?
      polygon.isActive = true;
      polygon.isGroupActive = true;
      selected.push(polygon);
    }
  });

  im.maps[id].selected = selected;
};

iMapsManager.groupHover = function (id, ev) {
  var im = this,
    dataContext;

  if (ev.target.isLabels) {
    dataContext = ev.target.dataItems.first.dataContext;
  } else {
    dataContext = ev.target.dataItem.dataContext;
  }

  if (ev.target.dataItem.dataContext.madeFromGeoData) {
    return;
  }

  if (im.bool(dataContext.triggerClickOnHover) && (typeof iMaps.maps[id].mapClicked === 'undefined' || iMaps.maps[id].mapClicked === false)) {
    iMapsManager.select(id, dataContext.id, false, true, dataContext.mapID, false);
  } // set mouse hover pointer cursor

  if (ev.target.dataItem.dataContext.action && ev.target.dataItem.dataContext.action != "none") {
    ev.target.cursorOverStyle = am4core.MouseCursorStyle.pointer;
  } // hilight all polygons from this group


  ev.target.parent.mapPolygons.each(function (polygon) {
    if (!polygon.dataItem.dataContext.madeFromGeoData) {
      if (!polygon.isActive) {
        polygon.setState("highlight");
      }
    }
  });
};

iMapsManager.groupHoverOut = function (id, ev) {
  if (ev.target.dataItem.dataContext.madeFromGeoData) {
    return;
  }


  ev.target.parent.mapPolygons.each(function (polygon) {
    if (!polygon.isGroupActive ) {
      polygon.setState("default");
      polygon.isActive = false;
      polygon.isHover = false;
    }
  });
};


/** 
 * Get regions object collection based on value of a specific parameter
 * id - map id
 * value - value you want to filter by
 * parameter - parameter to search
 */
iMapsManager.getRegionsByValue = function (id, value, parameter) {

  var im = this,
    map = im.maps[id],
    series = map.series,
    seriesType = false,
    regions = [],
    tempArray;

  value = value || '*';
  parameter = parameter || 'val';

  series.forEach(function (serie) {
    seriesType = im.getTargetSeriesType(serie);
    if (seriesType === 'MapPolygonSeries') {
      tempArray = serie.mapPolygons.values.filter(function (entry) {
        if (entry.dataItem.dataContext[parameter] === value || value === '*' ) {
          return true;
        }
      });
      regions = regions.concat(tempArray);
    }
  });

  return regions;
}

/** Get markers object collection based on value of a specific parameter
 * It will search for all kinds of markers (icons, images, round markers)
 * id - map id
 * value - value you want to filter by
 * parameter - parameter to search
 */
iMapsManager.getMarkersByValue = function (id, value, parameter) {

  var im = this,
    map = im.maps[id],
    series = map.series,
    seriesType = false,
    markers = [],
    tempArray;

  value = value || '*';
  parameter = parameter || 'val';

  series.forEach(function (serie) {
    seriesType = im.getTargetSeriesType(serie);
    if (seriesType === 'MapImageSeries') {
      tempArray = serie.mapImages.values.filter(function (entry) {
        if (entry.dataItem.dataContext[parameter] === value || value === '*') {
          return true;
        }
      });
      markers = markers.concat(tempArray);
    }
  });

  return markers;
}

/**
 * Selects a element in the map
 * id - id of the map
 * elID - id of the element to select
 * forceFixedTooltip - if we should force the tooltip to be fixed or not
 * showTooltip
 * seriesMapID - map id of the series, which could be ab overlay
 * click - boolean - trigger true click event 
 */


iMapsManager.select = function (id, elID, forceFixedTooltip, showTooltip, seriesMapID, click) {
  var im = this,
    map = im.maps[id],
    data = im.maps[id].data,
    series = map.series,
    selected = [],
    select,
    group,
    defaultTooltipPosition,
    defaultTooltipShowOn,
    seriesByID = iMaps.maps[id].seriesById,
    thisSeries = false,
    ogID,
    customRegionGroup = true,
    triggered = false, // temp solution to prevent map from triggering multiple click actions if there are entries with same ID in different layers
    isGroup = false,
    keepThis = true;

  // map sure it's string
  if (Number.isInteger(elID)) {
    elID = elID.toString();
  }

  ogID = elID;

  // Force fixed position?
  if (typeof forceFixedTooltip === 'undefined') {
    forceFixedTooltip = true;
  }

  if (typeof showTooltip === 'undefined') {
    showTooltip = true;
  }

  if (typeof seriesMapID === 'undefined') {
    seriesMapID = id;
  }

  if (seriesByID.hasOwnProperty(seriesMapID)) {
    thisSeries = seriesByID[seriesMapID];
  }

  if (typeof click === 'undefined') {
    click = true;
  } else { 
    click = false 
  }

  // we pass the click as the 'skipReset' - if not a real click, its the hover and we skip the reset to prevent flickering
  // second argument, the keepThis set to true, to avoid removing hover status of selected/hovered entry
  // update - second argument might be set to false, if tooltip only displays on hit to fix issue where the tooltip wouldn't close
  if( data.tooltip && data.tooltip.showTooltipOn === 'hit' ){
    keepThis = false;
  }

  iMapsManager.clearSelected(id, keepThis, ! click);

  if (click) {
    iMaps.maps[id].activeStateControl = true;
  } else {
    iMaps.maps[id].activeStateControl = false;
  }

  // if we have to search in specific series and this is not that one, skip
  if (thisSeries) {
    series = thisSeries;
  }

  if (Array.isArray(series)) {
    for (var i = 0, len = series.length; i < len; i++) {

      if( triggered ){
        break;
      }

      // if it's one of the cluster series, ignore
      if (typeof series[i].isCluster !== "undefined" && series[i].isCluster) {
        continue;
      }
      // regionSeries
      if (series[i].mapPolygons) {

        //first trigger click
        // this might return false if there is no entry created for this group and we are selecting programatically
        select = series[i].getPolygonById(elID);

        if (select && typeof select.dataItem.dataContext.originalID !== 'undefined' && select.dataItem.dataContext.originalID.includes(',')) {
          elID = select.dataItem.dataContext.originalID;
        }

        // show series if it's hidden
        if( select ){
            series[i].show();
        }
        

        // check if group
        if (elID.includes(',')) {

          if (typeof select !== 'undefined' && select) {

            select.tooltip = false;
            select.dispatchImmediately("hit");
            select.hideTooltip();

            customRegionGroup = false;
            
            // reset true click events controller
            iMaps.maps[id].activeStateControl = true;
            
          }

          //then hilight the first one
          group = elID.split(',');
          group.forEach(function (rxid, indx) {

            select = series[i].getPolygonById(rxid.trim());

            if (typeof select !== 'undefined' && select) {

              if (select.dataItem.dataContext.madeFromGeoData) {
                return;
              }

              // if originalID is not the same as the current originalID from the group, let's ignore it, it could belong to another group
              if (select.dataItem.dataContext.originalID !== elID && !customRegionGroup) {
                return;
              }

              //the first one, let's trigger the tooltip and the hit
              if (indx === 0 && showTooltip) {

                triggered = true;
                if (forceFixedTooltip) {

                  defaultTooltipPosition = typeof select !== 'undefined' && typeof select.tooltipPosition !== 'undefined' ? select.tooltipPosition : 'hover';
                  select.tooltipPosition = 'fixed';

                  // experimental code to check if on hit tooltip remains
                  defaultTooltipShowOn = select.showTooltipOn;
                  select.showTooltipOn = 'always';

                  iMaps.maps[id].activeStateControl = true;

                  select.tooltipPosition = defaultTooltipPosition;
                  select.showTooltipOn = defaultTooltipShowOn;

                } else {
                  select.isHover = true;
                }
              }

              // if we have the active state, use it instead
              if (data.regionActiveState && im.bool(data.regionActiveState.enabled)) {
                select.setState("active");
              } else {
                select.setState("highlight");
              }


              selected.push(select);
            }
          });
        } else {


          // individual region
          select = series[i].getPolygonById(elID);

          if (typeof select !== 'undefined' && select) {

            let timeout = 0;
             //check if globe to rotate
             if (data.projection === 'Orthographic' ) {
              let map = iMaps.maps[id].map;
              map.animate( { property: "deltaLongitude", to: -select.longitude }, 500, am4core.ease.linear);
              map.animate( { property: "deltaLatitude", to: -select.latitude }, 550, am4core.ease.linear);
              timeout = 550;
            } 

            setTimeout(function(select){

              console.log( select.dataItem.dataContext );

              if (forceFixedTooltip && select.dataItem.dataContext && select.dataItem.dataContext.action !== 'igm_display_map' ) {
                defaultTooltipPosition = typeof select !== 'undefined' && typeof select.tooltipPosition !== 'undefined' ? select.tooltipPosition : 'hover';
                select.tooltipPosition = 'fixed';
                // experimental code to check if on hit tooltip remains
                defaultTooltipShowOn = select.showTooltipOn;
                select.showTooltipOn = 'always';
                select.dispatchImmediately("hit");
                triggered = true;
                select.tooltipPosition = defaultTooltipPosition;
                select.showTooltipOn = defaultTooltipShowOn;
                selected.push(select);
              } 
              // if action is set to display map it will zoom and we don't need to show tooltip
              else if (select.dataItem.dataContext && select.dataItem.dataContext.action === 'igm_display_map') {

                select.dispatchImmediately("hit");
                select.hideTooltip();
                triggered = true;
                selected.push(select);
              }
              else {
                select.dispatchImmediately("hit");
                triggered = true;
                selected.push(select);
              }
            },timeout, select);
            
          }
        }
      }
      // imageSeries
      if (series[i].mapImages) {
        // mutiple markers
        if (elID.includes(',')) {
          // hilight
          group = elID.split(',');
          group.forEach(function (rxid, indx) {

            select = series[i].getImageById(rxid);


            if (typeof select !== 'undefined' && select) {

                // show series if it's hidden
                series[i].show();

              if (forceFixedTooltip) {

                defaultTooltipShowOn = select.showTooltipOn;
                select.showTooltipOn = 'always';

                defaultTooltipPosition = typeof select && typeof select.tooltipPosition !== 'undefined' ? select.tooltipPosition : 'hover';
                select.tooltipPosition = 'fixed';
                select.dispatchImmediately("hit");
                triggered = true;
                select.tooltipPosition = defaultTooltipPosition;
                select.showTooltipOn = defaultTooltipShowOn;
                selected.push(select);
              } else {
                select.dispatchImmediately("hit");
                triggered = true;
                selected.push(select);
              }
            }
          });
        } 
        // single marker 
        else {
          let selectMarker = series[i].getImageById(elID);
          if (selectMarker) {

            // show series if it's hidden
            series[i].show();

            let timeoutM = 0;
             //check if globe to rotate
             if (data.projection === 'Orthographic' ) {
              let map = iMaps.maps[id].map;
              map.animate( { property: "deltaLongitude", to: -selectMarker.longitude }, 500, am4core.ease.linear);
              map.animate( { property: "deltaLatitude", to: -selectMarker.latitude }, 550, am4core.ease.linear);
              timeoutM = 550;
            } 

            setTimeout(function(){
              if (forceFixedTooltip) {
                defaultTooltipPosition = selectMarker.tooltipPosition;
                defaultTooltipShowOn = selectMarker.showTooltipOn;
                selectMarker.tooltipPosition = 'fixed';
                selectMarker.showTooltipOn = 'always';
                selectMarker.isHover = true;
                selectMarker.isActive = true;
                // to also show tooltip when using the select method 
                selectMarker.dispatchImmediately("hit");
                selectMarker.setState("active");
                selectMarker.children.each(function(child){
                  //if(child.className === 'Circle'){
                    child.showTooltip(0);
                  //}
                });
                triggered = true;
                selectMarker.tooltipPosition = defaultTooltipPosition;
                selectMarker.showTooltipOn = defaultTooltipShowOn;
                selected.push(selectMarker);
              } else {
                selectMarker.dispatchImmediately("hit");
                triggered = true;
                selected.push(selectMarker);
              }
            },timeoutM);
            
          }
        }
      }
    }
  }

  im.maps[id].selected = selected;
  return select;
};

iMapsManager.setupRangeHeatMap = function (series, id, data) {
  var im = this,
    regions = data.regions,
    markers = data.roundMarkers,
    reordered,
    seriesType = im.getTargetSeriesType(series);

  if (seriesType === "MapImageSeries") {
    if (!Array.isArray(data.heatMapMarkers.range) || data.heatMapMarkers.range.length === 0) {
      return;
    }

    // reorder ranges by value
    reordered = data.heatMapMarkers.range.slice(0);
    reordered.sort(function (a, b) {
      if (isNaN(a.rule)) {
        var x = a.rule.toLowerCase();
        var y = b.rule.toLowerCase();
        return x < y ? -1 : x > y ? 1 : 0;
      } else {
        return parseFloat(a.rule) - parseFloat(b.rule);
      }
    });

    if (Array.isArray(markers) && markers.length > 0) {
      markers.forEach(function (entry, index) {
        if (typeof entry[data.heatMapMarkers.source] === 'undefined') {
          return;
        }

        // default to correct value reference, since value will be only numeric
        if (data.heatMapMarkers.source === 'value') {
          data.heatMapMarkers.source = 'val';
        }

        var val = entry[data.heatMapMarkers.source];
        var start;

        if (!isNaN(val)) {
          val = parseFloat(val);
        }

        reordered.forEach(function (ruleData, index) {
          if (isNaN(ruleData.rule)) {
            start = ruleData.rule.trim(); // if it's not a number

            if (val == start) {
              entry.fill = ruleData.fill;
              entry.radius = parseFloat(ruleData.radius);
            }
          } else {
            start = parseFloat(ruleData.rule); // if it's a number

            if (val >= start) {
              entry.fill = ruleData.fill;
              entry.radius = parseFloat(ruleData.radius);
            }
          }
        });
      });
    }
  } else if (seriesType === "MapPolygonSeries") {
    if (!Array.isArray(data.heatMapRegions.range) || data.heatMapRegions.range.length === 0) {
      return;
    } // reorder ranges by value


    reordered = data.heatMapRegions.range.slice(0);
    reordered.sort(function (a, b) {
      if (isNaN(a.rule)) {
        var x = a.rule.toLowerCase();
        var y = b.rule.toLowerCase();
        return x < y ? -1 : x > y ? 1 : 0;
      } else {
        return parseFloat(a.rule) - parseFloat(b.rule);
      }
    });

    if (Array.isArray(regions) && regions.length > 0) {



      if (!isNaN(data.heatMapRegions.source)) {
        data.heatMapRegions.source = parseInt(data.heatMapRegions.source);
      }

      regions.forEach(function (entry, index) {
        if (typeof entry[data.heatMapRegions.source] === 'undefined') {
          return;
        }

        if (data.heatMapRegions.source === 'value') {
          data.heatMapRegions.source = 'val';
        }

        var val = entry[data.heatMapRegions.source];
        var start;

        if (!isNaN(val)) {
          val = parseFloat(val);
        }

        reordered.forEach(function (ruleData, index) {
          if (isNaN(ruleData.rule)) {
            start = ruleData.rule.trim(); // if it's a number

            if (val == start) {
              entry.fill = ruleData.fill;
            }
          } else {
            start = parseFloat(ruleData.rule); // if it's a number

            if (val >= start) {
              entry.fill = ruleData.fill;
            }
          }
        });
      });
    }
  }
};
/**
 *
 * @param {object} series
 * @param {int} id - parent map id
 * @param {object} data - current map data
 */


iMapsManager.setupHeatMap = function (series, id, data) {
  var im = this,
    map = im.maps[id].map,
    heatLegend,
    legendLabel,
    legendContainer,
    minRange,
    maxRange,
    target,
    minProp,
    maxProp,
    propTargets,
    dataSource,
    seriesType = im.getTargetSeriesType(series); // setup target

  if (seriesType === "MapImageSeries") {
    if (typeof data.heatMapMarkers.type !== 'undefined' && data.heatMapMarkers.type === 'range') {
      im.setupRangeHeatMap(series, data.id, data);
      return;
    }

    target = series.mapImages.template.children.values[0];
    propTargets = ["fill", "radius"];
    dataSource = data.heatMapMarkers;
  } else if (seriesType === "MapPolygonSeries") {
    if (typeof data.heatMapRegions.type !== 'undefined' && data.heatMapRegions.type === 'range') {
      im.setupRangeHeatMap(series, data.id, data);
      return;
    }

    target = series.mapPolygons.template;
    propTargets = ["fill"];
    dataSource = data.heatMapRegions;
  } else {
    return;
  }

  // make sure we read data form the correct property
  series.dataFields.value = dataSource.source;

  if (!Array.isArray(propTargets)) {
    propTargets = [propTargets];
  }

  propTargets.map(function (prop) {
    // setup min/max sources
    if (prop === "fill") {
      minProp = dataSource.minColor;
      maxProp = dataSource.maxColor;
    } else if (prop === "radius") {
      minProp = dataSource.minRadius;
      maxProp = dataSource.maxRadius;
    }

    series.heatRules.push({
      property: prop,
      target: target,
      min: minProp,
      max: maxProp
    });
  });

  if (im.bool(dataSource.legend)) {

    legendContainer = map.createChild(am4core.Container);
    legendContainer.align = typeof dataSource.legendAlign !== 'undefined' ? dataSource.legendAlign : 'right';
    legendContainer.valign = typeof dataSource.legendValign !== 'undefined' ? dataSource.legendValign : 'bottom';
    legendContainer.userClassName = "mapLegendContainer";
    legendContainer.marginRight = am4core.percent(4);
    //legendContainer.parent = map;

    if (window.innerWidth <= 780) {
      legendContainer.width = am4core.percent(40);
    } else {
      legendContainer.width = am4core.percent(25);
    }

    // legend title
    if (typeof dataSource.label !== 'undefined' && dataSource.label !== '') {
      legendLabel = legendContainer.createChild(am4core.Label);
      legendLabel.text = dataSource.label;
      legendLabel.horizontalCenter = "left";
      legendLabel.verticalCenter = "top";

      legendLabel.fontSize = 12;
      legendLabel.paddingBottom = 40;

      if (window.innerWidth <= 780) {
        legendLabel.fontSize = 10;
        legendLabel.paddingBottom = 48;
      } else {
        legendLabel.fontSize = 12;
        legendLabel.paddingBottom = 48;
      }

      legendLabel.nonScaling = false;
      legendLabel.clickable = false;
      legendLabel.focusable = false;
      legendLabel.hoverable = false;
    }

    //legendLabel.parent = heatLegend;
    //label.insertAfter(heatLegend.valueAxis);
    //heatLegend.valueAxis.title.text = 'test title';

    heatLegend = legendContainer.createChild(am4maps.HeatLegend);
    heatLegend.series = series;
    heatLegend.align = "right";
    heatLegend.valign = "bottom";
    heatLegend.width = am4core.percent(100);
    //heatLegend.marginRight = am4core.percent(4);
    heatLegend.minValue = 0;
    heatLegend.maxValue = 99999999999999; // Set up custom heat map legend labels using axis ranges

    minRange = heatLegend.valueAxis.axisRanges.create();
    minRange.value = heatLegend.minValue;
    minRange.label.text = dataSource.minLabel;
    maxRange = heatLegend.valueAxis.axisRanges.create();
    maxRange.value = heatLegend.maxValue;
    maxRange.label.text = dataSource.maxLabel;

    minRange.label.fontSize = 12;
    maxRange.label.fontSize = 12;

    if (window.innerWidth <= 780) {
      minRange.label.fontSize = 10;
      maxRange.label.fontSize = 10;
    } else {
      minRange.label.fontSize = 12;
      maxRange.label.fontSize = 12;
    }

    // Blank out internal heat legend value axis labels
    heatLegend.valueAxis.renderer.labels.template.adapter.add("text", function () {
      return "";
    });

  }
};

iMapsManager.drillTo = function (id, ev, currentRegion, customMap) {
  var im = iMapsManager,
    map = im.maps[id].map,
    data = im.maps[id].data,
    allCurrentSeries = iMapsManager.maps[id].series,
    baseSeries = iMapsManager.maps[id].baseSeries,
    allBaseSeries = iMapsManager.maps[id].allBaseSeries,
    bgSeries = im.maps[id].backgroundSeries,
    opacity,
    i,
    len; 
    
    opacity = typeof igmDrilldownBaseMapOpacity !== 'undefined' ? igmDrilldownBaseMapOpacity : 0.3,

   // hide all others except this one and baseSeries
  // if were opening a custom map
  customMap = customMap || false;

  // hide or fade series
  for (i = 0, len = allCurrentSeries.length; i < len; i++) {
    if (baseSeries.includes(allCurrentSeries[i])) {
      // let's check if new option to keep base map is enabled
      if (typeof data.alwaysKeepBase === 'undefined' || !im.bool(data.alwaysKeepBase)) {
        allCurrentSeries[i].opacity = opacity;

        // hide image background
        bgSeries.opacity = opacity;
      }
    } else if (allBaseSeries.includes(allCurrentSeries[i])) {
      // let's check if new option to keep base map is enabled
      if (typeof data.alwaysKeepBase === 'undefined' || !im.bool(data.alwaysKeepBase)) {
        allCurrentSeries[i].hide();
      }
    } else {
      allCurrentSeries[i].hide();
    }
  }

  for (i = 0, len = currentRegion.length; i < len; i++) {
    currentRegion[i].show();
  } 
  // is drilling
  iMapsManager.maps[id].isDrilling = true;

  // zoom to region - it will only work when zoom is enabled so that controls exist
  if (!ev.zooming) {

    if (customMap) {
      console.log('drill to specific map');
      im.zoomToMap(ev, currentRegion);
      return;
    }

    im.zoomToRegion(ev, id);
  }
};

iMapsManager.drillDown = function (id, ev) {
  var im = iMapsManager,
    mapName = iMapsRouter.iso2cleanName(ev.target.dataItem.dataContext.id, id),
    targetID = ev.target.dataItem.dataContext.id,
    allCurrentSeries = iMapsManager.maps[id].series,
    clicked = ev.target.dataItem.dataContext,
    currentRegion,
    customMap = false,
    baseSeries = iMapsManager.maps[id].baseSeries,
    allBaseSeries = iMapsManager.maps[id].allBaseSeries,
    i,
    len,
    checkSeries = [],
    seriesExists = false;
  console.log('Map Name:', mapName);
  console.log('Available Series:', iMapsManager.maps[id].seriesIndex);
  console.log('Available Series by ID: ', iMapsManager.maps[id].seriesById); // check if geofile info exists or return if the id is numeric

  if (!mapName || !isNaN(targetID)) {
    // well, let's only return if it's not showing another map
    // code needs better logic...
    if (_typeof(clicked) === undefined || clicked.action !== 'igm_display_map') {
      return false;
    }
  }

  if (ev.target.polygon) {
    // what we need to check
    checkSeries.push(mapName); // check if series exists for this map

    checkSeries.forEach(function (ser) {
      if (_typeof(clicked) !== undefined && clicked.action == 'igm_display_map') {
        console.log('Display custom map ' + clicked.content);

        if (Array.isArray(iMapsManager.maps[id].seriesById[parseInt(clicked.content)])) {
          seriesExists = true;
          currentRegion = iMapsManager.maps[id].seriesById[parseInt(clicked.content)];
          customMap = true;
        }
      } else {
        if (Array.isArray(iMapsManager.maps[id].seriesIndex[ser])) {
          seriesExists = true;
          currentRegion = iMapsManager.maps[id].seriesIndex[ser];
        }
      }
    });

    if (seriesExists) {
      iMapsManager.drillTo(id, ev, currentRegion, customMap);
      iMapsManager.maps[id].drilledTo = currentRegion[0].mapID;
    } else {
      // if the target is part of current series, do nothing
      if (currentRegion === ev.target.series) {
        iMapsManager.maps[id].isDrilling = false;
        return;
      } 
      // if target is base series, show it
      if (baseSeries.includes( ev.target.series ) ) {
        iMapsManager.maps[id].isDrilling = false;
        iMapsManager.maps[id].drilledTo = false; 
        
        // hide all except baseSeries
        for (i = 0, len = allCurrentSeries.length; i < len; i++) {
          if (allBaseSeries.includes(allCurrentSeries[i])) {
            allCurrentSeries[i].show();
          } else {
            allCurrentSeries[i].hide();
          }
        }
      }
    }
  }
};
/*HELPERS*/


iMapsManager.getSelected = function (id) {
  var im = this,
    map = im.maps[id],
    selected = map.selected || false,
    multiple = [];

  if (selected) {
    if (Array.isArray(selected)) {
      selected.forEach(function (sel) {
        multiple.push(sel.dataItem.dataContext);
      });
      return multiple;
    }

    return selected.dataItem.dataContext;
  } else {
    return false;
  }
};

iMapsManager.getHovered = function (id) {
  var im = this,
    map = im.maps[id],
    hovered = map.hovered || false,
    multiple = [];

  if (hovered) {
    if (Array.isArray(hovered)) {
      hovered.forEach(function (sel) {
        multiple.push(sel.dataItem.dataContext);
      });
      return multiple;
    }
  } else {
    return false;
  }
};

iMapsManager.getHighlighted = function (id) {
  var im = this,
    map = im.maps[id],
    highlighted = map.highlighted || false,
    multiple = [];

  if (highlighted) {
    if (Array.isArray(highlighted)) {
      highlighted.forEach(function (sel) {
        multiple.push(sel.dataItem.dataContext);
      });
      return multiple;
    }

    return highlighted.dataItem.dataContext;
  } else {
    return false;
  }
};

iMapsManager.clearSelected = function (id, keepThis, skipReset) {
  var im = this,
    map = im.maps[id],
    selected = map.selected || []; // to keep the state of this element

  keepThis = keepThis || false;
  skipReset = skipReset || false;

  if (Array.isArray(selected) && selected.length > 0) {
    selected.forEach(function (polygon, index) {
      if (polygon !== keepThis && typeof polygon === "object" && typeof polygon.isHover !== 'undefined') {
        polygon.isHover = false;
        polygon.isActive = false;
        polygon.isGroupActive = false;

        // there were some issues on mobile tap (singleHit) where the tooltip woudl disappear, 
        // so we added this check before hiding the tooltip
       if ( ! keepThis ) {
        polygon.hideTooltip(0); // needed to hide tooltip
       }

        polygon.setState("default");

      }
    });
    selected = [];
  }

  if (!keepThis && typeof iMapsActions !== 'undefined') {

    if( ! skipReset ){
      //iMapsActions.resetActions(id);
    }
    map.selected = [];
  } else {
    map.selected = [keepThis];
  }



  return map.selected;
};

iMapsManager.clearHighlighted = function (id) {
  var im = this,
    map = im.maps[id],
    highlighted = map.highlighted || [];

  if (Array.isArray(highlighted) && highlighted.length > 0) {
    highlighted.forEach(function (polygon, index) {
      polygon.isHover = false;
      polygon.isActive = false;
      polygon.setState("default");
    });
    highlighted = [];
  }

  return highlighted;
};
/*
 * setup hover events
 * id - id of the map
 * eID - hovered element ID
 * forcedFixedTooltip - if we should force the tooltip to be fixed or not
 */


iMapsManager.hover = function (id, eID, forceFixedTooltip) {
  var im = this,
    map = im.maps[id],
    data = map.data,
    series = map.series,
    hovered = map.hovered || [],
    hover,
    group,
    defaultShowTooltipOn,
    defaultTooltipPosition; // map sure it's string

  if (Number.isInteger(eID)) {
    eID = eID.toString();
  } // Force fixed position?


  if (typeof forceFixedTooltip === 'undefined') {
    forceFixedTooltip = true;
  }

  iMapsManager.clearHovered(id);
  hovered = [];

  if (Array.isArray(series)) {
    for (var i = 0, len = series.length; i < len; i++) {
      // regionSeries
      if (series[i].mapPolygons) {
        // multiple
        // check if group
        if (eID.includes(',')) {
          // foreach code
          group = eID.split(',');
          group.forEach(function (rxid, indx) {
            // single
            hover = series[i].getPolygonById(rxid.trim());

            if (hover) {
              if (forceFixedTooltip) {
                defaultTooltipPosition = typeof hover.tooltipPosition !== 'undefined' ? hover.tooltipPosition : 'fixed';
                defaultShowTooltipOn = typeof hover.showTooltipOn  !== 'undefined' ? hover.showTooltipOn : 'hover';
                hover.tooltipPosition = 'fixed';
                hover.showTooltipOn = 'always';
                hovered.push(hover);
                hover.dispatchImmediately("over");
                hover.isHover = true;
                hover.tooltipPosition = defaultTooltipPosition;
                hover.showTooltipOn = defaultShowTooltipOn;
              } else {
                hovered.push(hover);
                hover.dispatchImmediately("over");
                hover.isHover = true;
              }
            }
          });
        } else {
          // single region
          hover = series[i].getPolygonById(eID);
         
          if (hover) {
            if (forceFixedTooltip) {
              defaultTooltipPosition = hover.tooltipPosition;
              hover.tooltipPosition = 'fixed';
              hovered = [hover];
              hover.dispatchImmediately("over");
              hover.isHover = true;
              hover.tooltipPosition = defaultTooltipPosition;
            } else {
              hovered = [hover];
              hover.dispatchImmediately("over");
              hover.isHover = true;
            }
          }
        }
      } 
      // imageSeries
      if (series[i].mapImages) {
        // multiple markers
        if (eID.includes(',')) {
          // foreach code
          group = eID.split(',');
          group.forEach(function (rxid, indx) {
            hover = series[i].getImageById(rxid);

            if (hover) {
              if (forceFixedTooltip) {
                defaultTooltipPosition = hover.tooltipPosition;
                defaultShowTooltipOn = hover.showTooltipOn;
                hover.tooltipPosition = 'fixed';
                hover.showTooltipOn = 'always';
                hovered = [hover];
                hover.dispatchImmediately("over");
                hover.isHover = true;
                hover.setState("hover");
                hover.children.each(function(child){
                  if(child.className === 'Circle'){
                    child.showTooltip(0);
                  }
                });
                hover.tooltipPosition = defaultTooltipPosition;
                hover.showTooltipOn = defaultShowTooltipOn;
              } else {
                hovered = [hover];
                hover.dispatchImmediately("over");
                hover.isHover = true;
                hover.setState("hover");
              }
            }
          });
        } 
        // single marker
        else {

          hover = series[i].getImageById(eID);

          if (hover) {
            if (forceFixedTooltip) {
              defaultTooltipPosition = hover.tooltipPosition;
              hover.tooltipPosition = 'fixed';
              hovered = [hover];
              hover.dispatchImmediately("over");
              hover.isHover = true;
              hover.setState("hover");
              hover.children.each(function(child){
                if(child.className === 'Circle'){
                  child.showTooltip(0);
                }
              });
              hover.tooltipPosition = defaultTooltipPosition;
            } else {
              hovered = [hover];
              hover.dispatchImmediately("over");
              hover.isHover = true;
              hover.setState("hover");
            }
          }
        }
      }
    }
  }

  map.hovered = hovered;
  return hover;
};

iMapsManager.clearHovered = function (id, eID) {
  var im = this,
    map = im.maps[id],
    hovered = map.hovered || false,
    series = map.series,
    hover;
  eID = eID || false;

  if (eID) {
    if (Array.isArray(series)) {
      for (var i = 0, len = series.length; i < len; i++) {
        // regionSeries - specific region
        if (series[i].mapPolygons) {
          hover = series[i].getPolygonById(eID);

          if (hover) {
            hover.dispatchImmediately("out");
            hover.isHover = false;
          }
        } 
        
        // imageSeries - specific marker
        if (series[i].mapImages) {
          hover = series[i].getImageById(eID);

          if (hover) {
            hover.isHover = false;
            hover.setState("default");
            hover.dispatchImmediately("out");
          }
        }
      }
    }
  } 
  // all of the regions or markers
  else {
    if (hovered) {
      hovered.forEach(function (hov) {

        hov.dispatchImmediately("out");
        if( typeof hov.isGroupActive === 'undefined' || ! hov.isGroupActive ){
            hov.setState("default");
            hov.isHover = false;
        }
        if(typeof hov.children !== 'undefined'){
          hov.children.each(function(child){
            if(child.className === 'Circle'){
              child.hideTooltip(0);
            }
          });
        }
      });
      map.hovered = [];
      return true;
    }
  }

  return false;
};
/**
 * id - map ID
 * elID - element to highlight
 */


iMapsManager.highlight = function (id, elID) {
  var im = this,
    map = im.maps[id],
    series = map.series,
    select,
    highlighted = map.highlighted || [],
    group; // map sure it's string

  if (Number.isInteger(elID)) {
    elID = elID.toString();
  } //iMapsManager.clearSelected(id);


  if (Array.isArray(series)) {
    for (var i = 0, len = series.length; i < len; i++) {
      // regionSeries
      if (series[i].mapPolygons) {
        // check if group
        if (elID.includes(',')) {
          group = elID.split(',');
          group.forEach(function (rxid, indx) {
            select = series[i].getPolygonById(rxid.trim());

            if (typeof select !== 'undefined' && select) {
              if (select.dataItem.dataContext.madeFromGeoData) {
                return;
              }

              select.setState("highlight");
              highlighted.push(select);
            }
          });
        } else {
          select = series[i].getPolygonById(elID);

          if (typeof select !== 'undefined' && select) {
            select.setState("highlight");
            highlighted.push(select);
          }
        }
      } // imageSeries


      if (series[i].mapImages) {
        if (elID.includes(',')) {
          group = elID.split(',');
          group.forEach(function (rxid, indx) {
            select = series[i].getImageById(rxid);

            if (typeof select !== 'undefined' && select) {
              select.setStateOnChildren = true;
              select.setState("highlight");
              highlighted.push(select);
            }
          });
        } else {
          select = series[i].getImageById(elID);

          if (typeof select !== 'undefined' && select) {
            select.setStateOnChildren = true;
            select.setState("highlight");
            highlighted.push(select);
          }
        }
      }
    }
  }

  map.highlighted = highlighted;
  return select;
};

iMapsManager.getTargetSeriesType = function (el) {
  var className = el.className;
  return className;
};
/**
 * Setups clustered series based on coordinate values from data
 */


iMapsManager.setupClusters = function (data, id, overlay) {
  var im = this,
    map = im.maps[id],
    series = [],
    markerSeries,
    tempData = {},
    biasLevels = [],
    zoomLevels = [],
    biasSteps = 4,
    i = 0,
    prevBias = parseFloat(data.clusterMarkers.maxBias),
    maxZoomLevel = parseFloat(data.clusterMarkers.zoomLevel) || 20,
    tooltipTemplate = typeof data.clusterMarkers.tooltipTemplate !== 'undefined' ? data.clusterMarkers.tooltipTemplate : false;
  overlay = overlay || false;
  var currentMap = overlay ? overlay : id;

  while (i <= biasSteps) {
    biasLevels.push(prevBias);
    prevBias = prevBias / 2;
    zoomLevels.push(maxZoomLevel);

    // make sure the last one is set to 1, otherwise it will be hidden
    maxZoomLevel = i == 3 ? 1 : Math.ceil(maxZoomLevel / 2);
    i++;
  } 
  
  // reverse array to match detail level
  zoomLevels.reverse().pop();
  biasLevels.pop();

  if (typeof map.clusterSeries[currentMap] === 'undefined') {
    map.clusterSeries[currentMap] = {
      'zoomLevels': {},
      'overlay': overlay
    };
  }

  if (Array.isArray(data.roundMarkers)) {
    biasLevels.forEach(function (item, index) {
            
      series = geocluster(data.roundMarkers, item, data.markerDefaults, tooltipTemplate);
      tempData = Object.assign({}, data);
      tempData.roundMarkers = series;
      markerSeries = im.pushRoundMarkerSeries(id, tempData);
      markerSeries.name = tempData.title || "Map";
      markerSeries.hiddenInLegend = true; // set as cluster to be identified later in select method

      markerSeries.isCluster = true;
      map.clusterSeries[currentMap].zoomLevels[zoomLevels[index]] = markerSeries;

      if (index === 0 && !overlay) { // this somehow interfeers with the clusters after home button is clicked
        // im.maps[id].allBaseSeries.push(markerSeries);
      }

      // hide all series except the first
      //if (index > 0 ) {
        markerSeries.hidden = true;
      //}
    });
  }

  return true;
};

/**
 * triggers when map is ready
 * @param {int} id of the map 
 */
iMapsManager.triggerOnReady = function (id, data) {

  let isCustom = data.map == 'custom' ? true : false;
  let urlParams = new URLSearchParams(window.location.search);
  let myParam = urlParams.get('mregion');
  if (myParam) {
    //if it's a custom map, do nothing, we will trigger it on the map appeared event
    if (!isCustom) {
      iMapsManager.select(id, myParam, true, true);
    }
  }
}


/**
 * triggers when map is ready
 * @param {int} id of the map 
 */
iMapsManager.triggerOnAppeared = function (id, data) {

  let isCustom = data.map == 'custom' ? true : false;
  let urlParams = new URLSearchParams(window.location.search);
  let myParam = urlParams.get('mregion');
  if (myParam) {
    //if it's a custom map, do nothing, we will trigger it on the map appeared event
    if (isCustom) {
      setTimeout(function () {
        iMapsManager.select(id, myParam, true, true);
      }, 500);
    }
  }
}


/**
 * Adds a new region Series currently not loaded, adding the script to the body of the page and creating a new series after
 * id - map id to attach series
 * dataContent - object with data that would tipically be a polygon dataContent, like name and id
 * config - config object for the new series being added
 *
 * @return newSeries - the new created series object
 */


iMapsManager.addGeoFileSeries = function (id, dataContext, data) {
  var newSeries,
    geoFiles = iMapsRouter.getGeoFiles(dataContext);
  var scriptPromise = new Promise(function (resolve, reject) {
    var script = document.createElement("script");
    document.body.appendChild(script);
    script.onload = resolve;
    script.onerror = reject;
    script.async = true;
    script.src = geoFiles.src;
  });
  scriptPromise.then(function () {
    var data = {
      title: geoFiles.title,
      map: geoFiles.map,
      regions: [],
      config: data // not working, we changed the config to be at parent level with data

    };
    iMapsManager.maps[id].seriesIndex[geoFiles.map] = [];
    newSeries = iMapsManager.pushRegionSeries(id, data);
    iMapsManager.maps[id].seriesIndex[geoFiles.map].push(newSeries);
    return newSeries;
  });
  return false;
};

iMapsManager.handleInfoBox = function (id) {
  var im = this,
    map = im.maps[id].map,
    events = ["ready", "mappositionchanged", "zoomlevelchanged"],
    container = document.getElementById("map_visual_info"),
    coordinatesc = document.getElementById("map_click_events_coordinates"),
    series = im.maps[id].series;

  if (container) {
    iMapsManager.populateInfo(id, container); // zoom, etc

    events.forEach(function (event) {
      map.events.on(event, function (ev) {
        iMapsManager.populateInfo(id, container);
      }, this);
    });
  }

  if (coordinatesc) {
    
    map.events.on("hit", function (ev) {
      var coordinates = map.svgPointToGeo(ev.svgPoint);
      var lat = Number(coordinates.latitude).toFixed(6);

      var _long = Number(coordinates.longitude).toFixed(6); // latitude


      var latEl = document.createElement('div');
      var latLabelEl = document.createElement('span');
      var latValueEl = document.createElement('span');
      latValueEl.classList.add('map_clicked_lat');
      latLabelEl.innerHTML = 'LAT: ';
      latValueEl.innerHTML = lat;
      latEl.appendChild(latLabelEl);
      latEl.appendChild(latValueEl); // longitude

      var longEl = document.createElement('div');
      var longLabelEl = document.createElement('span');
      var longValueEl = document.createElement('span');
      longValueEl.classList.add('map_clicked_long');
      longLabelEl.innerHTML = 'LON: ';
      longValueEl.innerHTML = _long;
      longEl.appendChild(longLabelEl);
      longEl.appendChild(longValueEl);
      coordinatesc.innerHTML = '';
      coordinatesc.appendChild(latEl);
      coordinatesc.appendChild(longEl);
      coordinatesc.parentElement.style.display = 'block';
      var event = new CustomEvent("mapPointClicked", {
        detail: {
          latitude: lat,
          longitude: _long
        }
      });
      document.dispatchEvent(event);
    }, this);
  }
};

iMapsManager.populateInfo = function (id, container) {
  var im = this,
    map = im.maps[id].map,
    info = "";
    info += "Zoom Level: " + parseFloat(Number(map.zoomLevel).toFixed(2)) + "<br>";
    info += "Center Coordinates: <br>" + "LAT " + Number(map.zoomGeoPoint.latitude).toFixed(6) + "<br>" + "LONG " + Number(map.zoomGeoPoint.longitude).toFixed(6) + "<br>";
    container.innerHTML = info;

    let centerData = {
      "zoom" : parseFloat(Number(map.zoomLevel).toFixed(2)),
      "lat"  : Number(map.zoomGeoPoint.latitude).toFixed(6),
      "long" : Number(map.zoomGeoPoint.longitude).toFixed(6)
    };

    container.setAttribute('data-visual',JSON.stringify(centerData));
};

iMapsManager.hideAllSeries = function (id, keepBase) {

    id = parseInt(id);

  if (!id) {
    return;
  }

  keepBase = keepBase || false;
  var map = iMaps.maps[id];
  var baseRegionSeries = map.baseRegionSeries;
  var groupedSeries = map.groupedBaseRegionSeries;
  var allbaseSeries = map.allBaseSeries;

  for (var index = 0; index < map.series.length; index++) {
    var serie = map.series[index];

    //if (baseRegionSeries !== serie && !groupedSeries.includes(serie)) {
    // we don't need to check if it's not the base series at this point

    if (!groupedSeries.includes(serie)) {
      // in case we need to check also if we keep base series
      if (!keepBase || keepBase && !allbaseSeries.includes(serie)) {
        serie.hide();
      }
    }
  }
};

iMapsManager.showAllSeries = function (id) {

    id = parseInt(id);

  if (!id) {
    return;
  }

  var map = iMaps.maps[id];

  for (var index = 0; index < map.series.length; index++) {
    var serie = map.series[index];

    if (typeof serie.isCluster === 'undefined' || serie.isCluster === false) {
      serie.show();
    }
  }
};
/** Util function to return boolean value of string */


iMapsManager.bool = function (string) {
  var bool = Number(string) === 0 || string === "false" || typeof string === "undefined" ? false : true;
  return bool;
};

iMapsManager.isJSON = function (str) {
  try {
    JSON.parse(str);
  } catch (e) {
    return false;
  }

  return true;
};
/* Closest Polyfill */


if (!Element.prototype.matches) {
  Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
}

if (!Element.prototype.closest) {
  Element.prototype.closest = function (s) {
    var el = this;

    do {
      if (el.matches(s)) return el;
      el = el.parentElement || el.parentNode;
    } while (el !== null && el.nodeType === 1);

    return null;
  };
}
/** Library for elements
 */


iMapsManager.library = {
  icons: {
    goFullIconPath: "m15.78742,5.93715l-3.95414,3.95414l3.95414,3.95414l1.60393,-1.60393q0.32301,-0.34529 0.77969,-0.15594q0.4344,0.18935 0.4344,0.65717l0,4.99002q0,0.2896 -0.21163,0.50123t-0.50123,0.21163l-4.99002,0q-0.46781,0 -0.65717,-0.44554q-0.18935,-0.4344 0.15594,-0.76855l1.60393,-1.60393l-3.95414,-3.95414l-3.95414,3.95414l1.60393,1.60393q0.34529,0.33415 0.15594,0.76855q-0.18935,0.44554 -0.65717,0.44554l-4.99002,0q-0.2896,0 -0.50123,-0.21163t-0.21163,-0.50123l0,-4.99002q0,-0.46781 0.44554,-0.65717q0.4344,-0.18935 0.76855,0.15594l1.60393,1.60393l3.95414,-3.95414l-3.95414,-3.95414l-1.60393,1.60393q-0.21163,0.21163 -0.50123,0.21163q-0.13366,0 -0.26732,-0.05569q-0.44554,-0.18935 -0.44554,-0.65717l0,-4.99002q0,-0.2896 0.21163,-0.50123t0.50123,-0.21163l4.99002,0q0.46781,0 0.65717,0.44554q0.18935,0.4344 -0.15594,0.76855l-1.60393,1.60393l3.95414,3.95414l3.95414,-3.95414l-1.60393,-1.60393q-0.34529,-0.33415 -0.15594,-0.76855q0.18935,-0.44554 0.65717,-0.44554l4.99002,0q0.2896,0 0.50123,0.21163t0.21163,0.50123l0,4.99002q0,0.46781 -0.4344,0.65717q-0.1448,0.05569 -0.27846,0.05569q-0.2896,0 -0.50123,-0.21163l-1.60393,-1.60393z",
    exitFullIconPath: "m10.04411,10.81638l0,5.40556q0,0.31372 -0.22925,0.54297t-0.54297,0.22925t-0.54297,-0.22925l-1.7375,-1.7375l-4.00591,4.00591q-0.12066,0.12066 -0.27752,0.12066t-0.27752,-0.12066l-1.37552,-1.37552q-0.12066,-0.12066 -0.12066,-0.27752t0.12066,-0.27752l4.00591,-4.00591l-1.7375,-1.7375q-0.22925,-0.22925 -0.22925,-0.54297t0.22925,-0.54297t0.54297,-0.22925l5.40556,0q0.31372,0 0.54297,0.22925t0.22925,0.54297zm9.10982,-8.10834q0,0.15686 -0.12066,0.27752l-4.00591,4.00591l1.7375,1.7375q0.22925,0.22925 0.22925,0.54297t-0.22925,0.54297t-0.54297,0.22925l-5.40556,0q-0.31372,0 -0.54297,-0.22925t-0.22925,-0.54297l0,-5.40556q0,-0.31372 0.22925,-0.54297t0.54297,-0.22925t0.54297,0.22925l1.7375,1.7375l4.00591,-4.00591q0.12066,-0.12066 0.27752,-0.12066t0.27752,0.12066l1.37552,1.37552q0.12066,0.12066 0.12066,0.27752z"
  }
};

iMapsManager.handleExternalZoom = function (id) {
  var mapContainer, mapBox, controls, homeBtn, zoomInBtn, zoomOutBtn;
  mapContainer = document.getElementById('map_wrapper_' + id);
  var im = this,
    data = im.maps[id].data,
    allCurrentSeries = im.maps[id].series,
    allBaseSeries = im.maps[id].allBaseSeries;

  if (!mapContainer) {
    return;
  }

  mapBox = mapContainer.querySelector('.map_box');
  mapContainer.classList.add('map_has_external_controls'); // home button

  homeBtn = document.createElement('div');
  homeBtn.setAttribute("id", "map_home_buttom_" + id);
  homeBtn.setAttribute("data-map-id", id);
  homeBtn.classList.add('map_home_button');
  homeBtn.innerHTML = '<svg height="20" width="20"><path d="M16,8 L14,8 L14,16 L10,16 L10,10 L6,10 L6,16 L2,16 L2,8 L0,8 L8,0 L16,8 Z M16,8" /></svg>';
  homeBtn.addEventListener('click', function (ev) {
    var id = this.getAttribute('data-map-id');
    iMaps.maps[id].map.goHome();

    // in case drillDown is enabled, we hide everything else
    if (im.bool(data.drillDownOnClick)) {
      for (var i = 0, len = allCurrentSeries.length; i < len; i++) {
        allCurrentSeries[i].hide(); //map.deltaLongitude = 0;
      }

      for (var ib = 0, lenb = allBaseSeries.length; ib < lenb; ib++) {
        // this is messing the cluster markers on base map
        allBaseSeries[ib].show();
      }

      iMapsManager.maps[id].drilledTo = false;
      iMapsManager.maps[id].isDrilling = false;
    }

    // reset actions
    if (typeof iMapsActions !== 'undefined' && typeof iMapsActions.resetActions !== 'undefined') {
      iMapsActions.resetActions(id);
    }
  }); // zoom in

  zoomInBtn = document.createElement('div');
  zoomInBtn.setAttribute("id", "map_zoomin_buttom_" + id);
  zoomInBtn.setAttribute("data-map-id", id);
  zoomInBtn.classList.add('map_zoomin_button');
  zoomInBtn.innerHTML = '+';
  zoomInBtn.addEventListener('click', function (ev) {
    var id = this.getAttribute('data-map-id');
    iMaps.maps[id].map.zoomIn();
  }); // zoom out

  zoomOutBtn = document.createElement('div');
  zoomOutBtn.setAttribute("id", "map_zoomout_buttom_" + id);
  zoomOutBtn.setAttribute("data-map-id", id);
  zoomOutBtn.classList.add('map_zoomout_button');
  zoomOutBtn.innerHTML = '-';
  zoomOutBtn.addEventListener('click', function (ev) {
    var id = this.getAttribute('data-map-id');
    iMaps.maps[id].map.zoomOut();
  }); // controls container

  controls = document.createElement('div');
  controls.setAttribute("id", "map_controls_" + id);
  controls.classList.add('map_controls');
  controls.appendChild(homeBtn);
  controls.appendChild(zoomInBtn);
  controls.appendChild(zoomOutBtn); // add buttons

  mapBox.parentNode.insertBefore(controls, mapBox.nextSibling);
};

iMapsManager.toggleFullscreen = function(element) {

    // se não está em tela cheia
    if (!document.fullscreen && !document.webkitIsFullScreen) {     
        // solicitar tela cheia
        if (element.requestFullscreen) {               // default
        element.requestFullscreen();
        } else if (element.mozRequestFullScreen) {     // Mozilla
        element.mozRequestFullScreen();
        } else if (element.webkitRequestFullscreen) {  // Chrome e Safari
        element.webkitRequestFullscreen();
        } else if (element.msRequestFullscreen) {      // Internet Explorer
        element.msRequestFullscreen();
        }
    } else { // exit fullscreen 
        if (document.exitFullscreen) {              // default
            document.exitFullscreen();
        } else if (document.mozCancelFullScreen) {  // Mozilla
            document.mozCancelFullScreen();      
        } else if (document.webkitExitFullscreen) { // Chrome e Safari
            document.webkitExitFullscreen();      
        } else if (document.msExitFullscreen) {     // Internet Explorer
            document.msExitFullscreen();
        }
        iMapsManager.isFullScreen = false;
    }
}

iMapsManager.isTouchScreendevice = function () {
  return (('ontouchstart' in window) ||
    (navigator.maxTouchPoints > 0) ||
    (navigator.msMaxTouchPoints > 0));
};

/**
 * This function is same as PHP's nl2br() with default parameters.
 *
 * @param {string} str Input text
 * @param {boolean} replaceMode Use replace instead of insert
 * @param {boolean} isXhtml Use XHTML
 * @return {string} Filtered text
 */


iMapsManager.nl2br = function (str) {
  var replaceStr = 'HI<br>HI';
  return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, replaceStr);
};

iMapsManager.isFullScreen = false;
/**
 * Main app file.  Initializes app components.
 */

/**
 * The main app object.
 *
 */

var iMaps = {
  originalData: JSON.parse(JSON.stringify(iMapsData))
};

iMaps.reset = function () {
  if (iMaps.maps) {
    Object.keys(iMaps.maps).forEach(function (id) {
      iMaps.maps[id].map.dispose();
    });
  }

  iMapsData = iMaps.originalData;
  iMaps.init();
};
/**
 * Initializes the interactiveMaps app
 *
 */


iMaps.init = function (hold) {
  // if map should not render
  if (typeof hold === 'undefined' && typeof iMapsData.options !== 'undefined' && typeof iMapsData.options.hold !== 'undefined' && iMapsData.options.hold === "1") {
    hold = true;
  }

  if (hold) {
    return;
  }

  if( typeof am4core === 'undefined' ){
    console.log('Map files not loaded properly.');

    let oxygen = document.querySelector('.oxygen-body .map_wrapper .map_render');
    if(oxygen){
      oxygen.innerHTML = 'Map Container. <br> Map will not render in Oxygen preview, but will render in live page.<br>Consider enabling the "Async Loading" option in the Settings > Performance page.';
    }
    return;
  }

  am4core.ready(function () {
    var data;
    am4core.options.autoSetClassName = true;
    am4core.options.classNamePrefix = "imaps";
    am4core.options.commercialLicense = true;
    am4core.options.queue = true;

    if (typeof iMapsData.options !== "undefined" && typeof iMapsData.options.lazyLoad !== "undefined" && iMapsData.options.lazyLoad === "1") {
      am4core.options.onlyShowOnViewport = true;
    } // animated


    if (typeof iMapsData.options !== "undefined" && typeof iMapsData.options.animations !== "undefined" && (iMapsData.options.animations === "1" || iMapsData.options.animations === true)) {
      am4core.useTheme(am4themes_animated);
    }

    data = iMapsModel.prepareData(iMapsData.data);
    data.forEach(function (data, index) {
      if (index.disabled) {
        return;
      }

      iMapsManager.init(index);
    });
    iMaps.maps = iMapsManager.maps;
  });
};

iMaps.loadScript = function (url, callback) {
  var script = document.createElement('script');
  script.type = 'text/javascript';
  script.src = url;
  script.onreadystatechange = callback;
  script.onload = callback;
  document.head.appendChild(script);
};

iMaps.loadScripts = function (urls, callback) {
  var loadedCount = 0;

  var multiCallback = function multiCallback() {
    loadedCount++;

    if (loadedCount >= urls.length) {
      callback.call(this, arguments);
    }
  };

  urls.forEach(function (url, index) {
    iMaps.loadScript(url, multiCallback);
  });
};

if (typeof iMapsData.async !== 'undefined' && Array.isArray(iMapsData.async) && iMapsData.async.length > 0) {
  // we need to load the core file first
  iMaps.loadScript(iMapsData.async[0], function () {
    // then we load the rest - shift() removes the first entry
    iMapsData.async.shift();
    iMaps.loadScripts(iMapsData.async, function () {
      iMaps.init();
    });
  });
} else {
  iMaps.init();
}
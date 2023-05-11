/**
 * BLOCK: Basic
 *
 * Registering a basic block with Gutenberg.
 * Simple block, renders and saves the same content without any interactivity.
 *
 * Styles:
 *        editor.css — Editor styles for the block.
 *        style.css  — Editor & Front end styles for the block.
 */
(function () {
	var __ = wp.i18n.__; // The __() for internationalization.
	var el = wp.element.createElement; // The wp.element.createElement() function to create elements.
	var registerBlockType = wp.blocks.registerBlockType; // The registerBlockType() to register blocks.

	var attributes = {
		block_wrap: { type: "string", default: "" },
		id: { type: "string", default: "" },
		label: { type: "string", default: "" },
		image: { type: "string", default: "" },
		paddingTop: { type: "string", default: "56%" },
		maxWidth: { type: "string", default: "" },
		content: {
			type: "string",
			default: "Select a map in the block options"
		},
		imagePreview: {
			type: "string",
			default: ""
		},
		className: { type: "string", default: "" }
	};


	var preview = function (props) {
		var content = props.attributes.content;
		var match, hasImage, backgroundContainer;

		// empty map, not selected
		if (props.attributes.id == "" && !props.isSelected) {
			content = "No map selected";
		}

		if (props.attributes.label !== "") {
			content = props.attributes.label;
		}

		// if selected, but something changed
		if (props.attributes.id !== "") {
			match = igmMapBlockOptions.find(function (o) {
				return o.value == props.attributes.id;
			});

			if (typeof match === "undefined") {
				props.setAttributes({
					id: "",
					label: "Map not found!",
					paddingTop: "56%",
					maxWidth: "",
					imagePreview: ""
				});
			} else {
				// image changed
				if (props.attributes.imagePreview !== match.image) {
					props.setAttributes({
						id: props.attributes.id,
						label: match.label,
						paddingTop: match.paddingTop + "%",
						maxWidth: match.maxWidth !== "" ? match.maxWidth + "px" : "",
						imagePreview: match.image !== "" ? match.image : ""
					});
				}
			}
		}

		props.attributes.paddingTop =
			props.attributes.paddingTop !== "" ? props.attributes.paddingTop : "56%";

		if (props.attributes.imagePreview !== "") {
			content = "Preview Only";
			hasImage = " igm-block-preview-has-image";
		}

		backgroundContainer = el("div", {
			class: "igm-map-background" + hasImage,
			style: {
				backgroundImage:
					props.attributes.imagePreview !== ""
						? "url(" + props.attributes.imagePreview + ")"
						: "",
				backgroundSize: "cover"
			}
		});

		previewContent = el(
			"div",
			{
				class: "map_container"
			},
			el("span", {
				class: "dashicons dashicons-admin-site-alt"
			}),
			el("p", {}, content)
		);

		return el(
			"div",
			{
				class:
					props.attributes.className +
					" igm-block-preview map_wrapper" +
					hasImage,
				style: {
					maxWidth: props.attributes.maxWidth
				}
			},
			el(
				"div",
				{
					class: "map_aspect_ratio",
					style: {
						paddingTop: props.attributes.paddingTop
					}
				},
				backgroundContainer,
				previewContent
			)
		);
	};

	/**
	 * Register Basic Block.
	 *
	 * Registers a new block provided a unique name and an object defining its
	 * behavior. Once registered, the block is made available as an option to any
	 * editor interface where blocks are implemented.
	 *
	 * @param  {string}   name     Block name.
	 * @param  {Object}   settings Block settings.
	 * @return {?WPBlock}          The block, if it has been successfully
	 *                             registered; otherwise `undefined`.
	 */


	registerBlockType("interactive-geo-maps/display-map", {
		// Block name. Block names must be string that contains a namespace prefix. Example: my-plugin/my-custom-block.
		title: "Map", // Block title.
		description: "Display a previously created Interactive Map", // Block title.
		icon: "admin-site-alt", // Block icon from Dashicons → https://developer.wordpress.org/resource/dashicons/.
		category: "widgets", // Block category — Group blocks together based on common traits E.g. common, formatting, layout widgets, embed.
		keywords: ["map", "interactive-map"],
		attributes: attributes,
		// The "edit" property must be a valid function.
		edit: function (props) {
			return [
				el(wp.editor.BlockControls, { key: "controls" }),

				el(
					wp.editor.InspectorControls,
					{ key: "inspector" },
					el(
						wp.components.PanelBody,
						{},
						el(wp.components.SelectControl, {
							label: "Map to display:",
							value: props.attributes.id,
							options: igmMapBlockOptions,
							onChange: function (id) {
								var match = igmMapBlockOptions.find(function (o) {
									return o.value == id;
								});
								props.setAttributes({
									id: id,
									label: match.label,
									paddingTop: match.paddingTop + "%",
									maxWidth: match.maxWidth !== "" ? match.maxWidth + "px" : "",
									imagePreview: match.image !== "" ? match.image : ""
								});
							}
						})
					)
				),
				preview(props)
			]; // end return
		},

		// The "save" property must be specified and must be a valid function.
		save: function (props) {
			var attr = props.attributes;
			// build the shortcode.
			var content = "[display-map";
			if (attr.hasOwnProperty("id")) {
				content += " id='" + attr.id + "' ";
			}
			content += "]";

			return el('div', {
				className: attr.className
			},
				content);
		},

		deprecated: [
			{
				attributes: attributes,

				save: function (props) {

					var attr = props.attributes;
					// build the shortcode.
					var content = "[display-map";
					if (attr.hasOwnProperty("id")) {
						content += " id='" + attr.id + "' ";
					}
					content += "]";

					return el('', {
						className: attr.className
					},
						content);
				},
			}
		]
	});
})();

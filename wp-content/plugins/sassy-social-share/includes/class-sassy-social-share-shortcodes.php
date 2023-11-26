<?php

/**
 * The file that defines Shortcodes class
 *
 * A class definition that includes functions used for Shortcodes.
 *
 * @since      1.0
 *
 */

/**
 * Shortcodes class.
 *
 * This is used to define functions for Shortcodes.
 *
 * @since      1.0
 */
class Sassy_Social_Share_Shortcodes {

	/**
	 * Options saved in database.
	 *
	 * @since    1.0
	 */
	private $options;

	/**
	 * Member to assign object of Sassy_Social_Share_Public Class.
	 *
	 * @since    1.0
	 */
	private $public_class_object;

	/**
	 * Assign plugin options to private member $options.
	 *
	 * @since    1.0
	 */
	public function __construct( $options, $public_class_object ) {

		$this->options = $options;
		$this->public_class_object = $public_class_object;

	}

	/** 
	 * Shortcode for Social Sharing.
	 */
	public function follow_icons_shortcode( $params ) {

		if ( $this->public_class_object->is_amp_page() ) {
			return;
		}
		extract( shortcode_atts( array(
			'style' => '',
			'width' => '32',
			'height' => '32',
			'shape' => 'square',
			'social_networks' => '',
			'type' => 'standard',
			'theme' => '',
			'left' => '0',
			'right' => '0',
			'top' => '100',
			'align' => 'left',
			'title' => ''
		), $params ) );

		$svg = array(
			'facebook' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-5 -5 42 42"><path d="M17.78 27.5V17.008h3.522l.527-4.09h-4.05v-2.61c0-1.182.33-1.99 2.023-1.99h2.166V4.66c-.375-.05-1.66-.16-3.155-.16-3.123 0-5.26 1.905-5.26 5.405v3.016h-3.53v4.09h3.53V27.5h4.223z" fill="#fff"></path></svg>',
			'twitter' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-4 -4 39 39"><path d="M28 8.557a9.913 9.913 0 0 1-2.828.775 4.93 4.93 0 0 0 2.166-2.725 9.738 9.738 0 0 1-3.13 1.194 4.92 4.92 0 0 0-3.593-1.55 4.924 4.924 0 0 0-4.794 6.049c-4.09-.21-7.72-2.17-10.15-5.15a4.942 4.942 0 0 0-.665 2.477c0 1.71.87 3.214 2.19 4.1a4.968 4.968 0 0 1-2.23-.616v.06c0 2.39 1.7 4.38 3.952 4.83-.414.115-.85.174-1.297.174-.318 0-.626-.03-.928-.086a4.935 4.935 0 0 0 4.6 3.42 9.893 9.893 0 0 1-6.114 2.107c-.398 0-.79-.023-1.175-.068a13.953 13.953 0 0 0 7.55 2.213c9.056 0 14.01-7.507 14.01-14.013 0-.213-.005-.426-.015-.637.96-.695 1.795-1.56 2.455-2.55z" fill="#fff"></path></svg>',
			'instagram' => '<svg version="1.1" viewBox="-10 -10 148 148" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><g><g><path d="M86,112H42c-14.336,0-26-11.663-26-26V42c0-14.337,11.664-26,26-26h44c14.337,0,26,11.663,26,26v44    C112,100.337,100.337,112,86,112z M42,24c-9.925,0-18,8.074-18,18v44c0,9.925,8.075,18,18,18h44c9.926,0,18-8.075,18-18V42    c0-9.926-8.074-18-18-18H42z" fill="#fff"></path></g><g><path d="M64,88c-13.234,0-24-10.767-24-24c0-13.234,10.766-24,24-24s24,10.766,24,24C88,77.233,77.234,88,64,88z M64,48c-8.822,0-16,7.178-16,16s7.178,16,16,16c8.822,0,16-7.178,16-16S72.822,48,64,48z" fill="#fff"></path></g><g><circle cx="89.5" cy="38.5" fill="#fff" r="5.5"></circle></g></g></svg>',
			'parler' => '<svg xmlns="http://www.w3.org/2000/svg" height="100%" width="100%" viewBox="-24 -30 140 160"><g fill="#fff"><path d="M58.34 83.31h-25v-8.49c0-4.5 3.64-8.14 8.14-8.14h16.87c13.8 0 25.02-11.19 25.02-24.94 0-13.75-11.23-24.94-25.03-24.94h-.26l-5.3-.16H0C0 7.45 7.45 0 16.63 0h36.41l5.44.17C81.39.24 100 18.86 100 41.74c0 22.92-18.69 41.57-41.66 41.57z"></path><path d="M16.65 100C7.46 100 .02 92.55.02 83.37V49.49c0-8.92 7.23-16.16 16.16-16.16h42.19a8.32 8.32 0 010 16.64h-33.5c-4.53 0-8.21 3.67-8.21 8.21V100z"></path></g></svg>',
			'pinterest' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-2 -2 35 35"><path fill="#fff" d="M16.539 4.5c-6.277 0-9.442 4.5-9.442 8.253 0 2.272.86 4.293 2.705 5.046.303.125.574.005.662-.33.061-.231.205-.816.27-1.06.088-.331.053-.447-.191-.736-.532-.627-.873-1.439-.873-2.591 0-3.338 2.498-6.327 6.505-6.327 3.548 0 5.497 2.168 5.497 5.062 0 3.81-1.686 7.025-4.188 7.025-1.382 0-2.416-1.142-2.085-2.545.397-1.674 1.166-3.48 1.166-4.689 0-1.081-.581-1.983-1.782-1.983-1.413 0-2.548 1.462-2.548 3.419 0 1.247.421 2.091.421 2.091l-1.699 7.199c-.505 2.137-.076 4.755-.039 5.019.021.158.223.196.314.077.13-.17 1.813-2.247 2.384-4.324.162-.587.929-3.631.929-3.631.46.876 1.801 1.646 3.227 1.646 4.247 0 7.128-3.871 7.128-9.053.003-3.918-3.317-7.568-8.361-7.568z"/></svg>',
			'behance' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path d="M3.862 8.136h5.66c1.377 0 3.19 0 4.13.566a3.705 3.705 0 0 1 1.837 3.26c0 1.66-.88 2.905-2.32 3.494v.042c1.924.397 2.97 1.838 2.97 3.76 0 2.297-1.636 4.483-4.743 4.483H3.86V8.14zm2.078 6.71h4.152c2.36 0 3.322-.856 3.322-2.493 0-2.16-1.53-2.468-3.322-2.468H5.94v4.96zm0 7.144h5.2c1.792 0 2.93-1.09 2.93-2.797 0-2.03-1.64-2.598-3.388-2.598H5.94v5.395zm22.017-1.833C27.453 22.65 25.663 24 23.127 24c-3.607 0-5.31-2.49-5.422-5.944 0-3.386 2.23-5.878 5.31-5.878 4 0 5.225 3.74 5.116 6.47h-8.455c-.067 1.966 1.05 3.716 3.52 3.716 1.53 0 2.6-.742 2.928-2.206h1.838zm-1.793-3.15c-.088-1.77-1.42-3.19-3.256-3.19-1.946 0-3.106 1.466-3.236 3.19h6.492zM20.614 8h4.935v1.68h-4.94z" fill="#fff"></path></svg>',
			'flickr' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><g fill="#fff"><circle cx="23" cy="16" r="6"></circle><circle cx="9" cy="16" r="6"></circle></g></svg>',
			'foursquare' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="-4 -4 40 40"><path fill="#fff" d="M21.516 3H7.586C5.66 3 5 4.358 5 5.383v21.995c0 1.097.65 1.407.958 1.53.31.126 1.105.206 1.676-.36l6.72-7.455c.105-.12.49-.284.552-.284h4.184c1.79 0 1.81-1.45 1.997-2.206.157-.63 1.946-9.57 2.58-12.395.523-2.32-.104-3.21-2.15-3.21zM20.2 9.682c-.07.33-.368.66-.75.693h-5.44c-.61-.034-1.108.422-1.108 1.032v.665c0 .61.5 1.24 1.108 1.24h4.607c.43 0 .794.276.7.737-.093.46-.573 2.82-.627 3.07-.052.254-.282.764-.716.764h-3.62c-.682 0-1.36-.008-1.816.56-.458.573-4.534 5.293-4.534 5.293V6.403c0-.438.31-.746.715-.74h11.274c.41-.006.915.41.834 1L20.2 9.68z"></path></svg>',
			'github' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path fill="#fff" d="M16 3.32c-7.182 0-13 5.82-13 13 0 5.754 3.72 10.612 8.89 12.335.65.114.893-.276.893-.617 0-.31-.016-1.333-.016-2.42-3.266.6-4.11-.797-4.37-1.53-.147-.373-.78-1.527-1.334-1.835-.455-.244-1.105-.845-.016-.86 1.024-.017 1.755.942 2 1.332 1.17 1.966 3.038 1.414 3.785 1.073.114-.845.455-1.414.83-1.74-2.893-.324-5.916-1.445-5.916-6.418 0-1.414.504-2.584 1.333-3.494-.13-.325-.59-1.657.13-3.445 0 0 1.085-.34 3.57 1.337 1.04-.293 2.146-.44 3.25-.44s2.21.147 3.25.44c2.49-1.69 3.58-1.337 3.58-1.337.714 1.79.26 3.12.13 3.446.828.91 1.332 2.064 1.332 3.494 0 4.99-3.04 6.094-5.93 6.42.47.405.876 1.185.876 2.404 0 1.74-.016 3.136-.016 3.575 0 .34.244.743.894.613C25.28 26.933 29 22.053 29 16.32c0-7.182-5.817-13-13-13z"></path></svg>',
			'linkedin' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 0 1 0 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="#fff"></path></svg>',
			'linkedin_company' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 0 1 0 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="#fff"></path></svg>',
			'medium' => '<svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path d="M7.8 11a.8.8 0 0 0-.27-.7l-2-2.42v-.41h6.23L16.57 18l4.24-10.53h5.94v.36L25 9.47a.5.5 0 0 0-.19.48v12.1a.5.5 0 0 0 .19.48l1.68 1.64v.36h-8.4v-.36L20 22.49c.18-.17.18-.22.18-.49v-9.77l-4.82 12.26h-.65L9.09 12.23v8.22a1.09 1.09 0 0 0 .31.94l2.25 2.74v.36h-6.4v-.36l2.26-2.74a1.09 1.09 0 0 0 .29-.94z" fill="#fff"></path></svg>',
			'mewe' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="-3 -3 38 38"><g fill="#fff"><path d="M9.636 10.427a1.22 1.22 0 1 1-2.44 0 1.22 1.22 0 1 1 2.44 0zM15.574 10.431a1.22 1.22 0 0 1-2.438 0 1.22 1.22 0 1 1 2.438 0zM22.592 10.431a1.221 1.221 0 1 1-2.443 0 1.221 1.221 0 0 1 2.443 0zM29.605 10.431a1.221 1.221 0 1 1-2.442 0 1.221 1.221 0 0 1 2.442 0zM3.605 13.772c0-.471.374-.859.859-.859h.18c.374 0 .624.194.789.457l2.935 4.597 2.95-4.611c.18-.291.43-.443.774-.443h.18c.485 0 .859.387.859.859v8.113a.843.843 0 0 1-.859.845.857.857 0 0 1-.845-.845V16.07l-2.366 3.559c-.18.276-.402.443-.72.443-.304 0-.526-.167-.706-.443l-2.354-3.53V21.9c0 .471-.374.83-.845.83a.815.815 0 0 1-.83-.83v-8.128h-.001zM14.396 14.055a.9.9 0 0 1-.069-.333c0-.471.402-.83.872-.83.415 0 .735.263.845.624l2.23 6.66 2.187-6.632c.139-.402.428-.678.859-.678h.124c.428 0 .735.278.859.678l2.187 6.632 2.23-6.675c.126-.346.415-.609.83-.609.457 0 .845.361.845.817a.96.96 0 0 1-.083.346l-2.867 8.032c-.152.43-.471.706-.887.706h-.165c-.415 0-.721-.263-.872-.706l-2.161-6.328-2.16 6.328c-.152.443-.47.706-.887.706h-.165c-.415 0-.72-.263-.887-.706l-2.865-8.032z"></path></g></svg>',
			'odnoklassniki' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M16 16.16a6.579 6.579 0 0 1-6.58-6.58A6.578 6.578 0 0 1 16 3a6.58 6.58 0 1 1 .002 13.16zm0-9.817a3.235 3.235 0 0 0-3.236 3.237 3.234 3.234 0 0 0 3.237 3.236 3.236 3.236 0 1 0 .004-6.473zm7.586 10.62c.647 1.3-.084 1.93-1.735 2.99-1.395.9-3.313 1.238-4.564 1.368l1.048 1.05 3.877 3.88c.59.59.59 1.543 0 2.133l-.177.18c-.59.59-1.544.59-2.134 0l-3.88-3.88-3.877 3.88c-.59.59-1.543.59-2.135 0l-.176-.18a1.505 1.505 0 0 1 0-2.132l3.88-3.877 1.042-1.046c-1.25-.127-3.19-.465-4.6-1.37-1.65-1.062-2.38-1.69-1.733-2.99.37-.747 1.4-1.367 2.768-.29C13.035 18.13 16 18.13 16 18.13s2.968 0 4.818-1.456c1.368-1.077 2.4-.457 2.768.29z"></path></svg>',
			'telegram' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M25.515 6.896L6.027 14.41c-1.33.534-1.322 1.276-.243 1.606l5 1.56 1.72 5.66c.226.625.115.873.77.873.506 0 .73-.235 1.012-.51l2.43-2.363 5.056 3.734c.93.514 1.602.25 1.834-.863l3.32-15.638c.338-1.363-.52-1.98-1.41-1.577z"></path></svg>',
			'tumblr' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-2 -2 36 36"><path fill="#fff" d="M20.775 21.962c-.37.177-1.08.33-1.61.345-1.598.043-1.907-1.122-1.92-1.968v-6.217h4.007V11.1H17.26V6.02h-2.925s-.132.044-.144.15c-.17 1.556-.895 4.287-3.923 5.378v2.578h2.02v6.522c0 2.232 1.647 5.404 5.994 5.33 1.467-.025 3.096-.64 3.456-1.17l-.96-2.846z"/></svg>',
			'vimeo' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path fill="#fff" d="M26.926 10.627c-.103 2.25-1.675 5.332-4.716 9.245C19.066 23.957 16.406 26 14.23 26c-1.348 0-2.49-1.244-3.42-3.732l-1.867-6.844C8.25 12.937 7.51 11.69 6.715 11.69c-.173 0-.778.365-1.815 1.09l-1.088-1.4a300.012 300.012 0 0 0 3.374-3.01c1.522-1.315 2.666-2.007 3.427-2.076 1.8-.173 2.907 1.057 3.322 3.69.45 2.84.76 4.608.935 5.3.52 2.356 1.09 3.534 1.713 3.534.483 0 1.21-.764 2.18-2.294.97-1.528 1.488-2.692 1.558-3.49.14-1.32-.38-1.98-1.553-1.98-.554 0-1.125.126-1.712.378 1.137-3.722 3.308-5.53 6.513-5.426 2.378.068 3.498 1.61 3.36 4.62z"></path></svg>',
			'vkontakte' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-1 -2 34 34"><path fill-rule="evenodd" clip-rule="evenodd" fill="#fff" d="M15.764 22.223h1.315s.394-.044.6-.262c.184-.2.18-.574.18-.574s-.03-1.764.79-2.023c.81-.255 1.844 1.705 2.942 2.46.832.57 1.464.445 1.464.445l2.936-.04s1.538-.097.81-1.304c-.06-.1-.426-.894-2.186-2.526-1.843-1.71-1.594-1.434.624-4.39 1.353-1.804 1.893-2.902 1.724-3.374-.16-.45-1.153-.33-1.153-.33l-3.306.02s-.247-.034-.428.074c-.178.108-.293.356-.293.356s-.522 1.394-1.223 2.58c-1.47 2.5-2.06 2.633-2.3 2.476-.563-.36-.42-1.454-.42-2.23 0-2.423.365-3.435-.72-3.696-.357-.085-.623-.143-1.544-.15-1.182-.014-2.18.003-2.743.28-.378.185-.667.595-.49.62.218.027.713.13.975.49.34.46.33 1.496.33 1.496s.193 2.852-.46 3.206c-.442.245-1.056-.252-2.37-2.52-.67-1.163-1.18-2.446-1.18-2.446s-.1-.24-.273-.37c-.212-.155-.506-.204-.506-.204l-3.145.02s-.473.015-.647.22c-.154.183-.01.56-.01.56s2.46 5.757 5.245 8.657c2.553 2.66 5.454 2.485 5.454 2.485z"/></svg>',
			'whatsapp' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-5 -5 40 40"><path id="arc1" stroke="#fff" stroke-width="2" fill="none" d="M 11.579798566743314 24.396926207859085 A 10 10 0 1 0 6.808479557110079 20.73576436351046"></path><path d="M 7 19 l -1 6 l 6 -1" stroke="#fff" stroke-width="2" fill="none"></path><path d="M 10 10 q -1 8 8 11 c 5 -1 0 -6 -1 -3 q -4 -3 -5 -5 c 4 -2 -1 -5 -1 -4" fill="#fff"></path></svg>',
			'xing' => '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-6 -6 42 42"><path d="M 6 9 h 5 l 4 4 l -5 7 h -5 l 5 -7 z m 15 -4 h 5 l -9 13 l 4 8 h -5 l -4 -8 z" fill="#fff"></path></svg>',
			'youtube' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M26.78 11.6s-.215-1.515-.875-2.183c-.837-.876-1.774-.88-2.204-.932-3.075-.222-7.693-.222-7.693-.222h-.01s-4.618 0-7.697.222c-.43.05-1.368.056-2.205.932-.66.668-.874 2.184-.874 2.184S5 13.386 5 15.166v1.67c0 1.78.22 3.56.22 3.56s.215 1.516.874 2.184c.837.875 1.936.85 2.426.94 1.76.17 7.48.22 7.48.22s4.623-.007 7.7-.23c.43-.05 1.37-.056 2.205-.932.66-.668.875-2.184.875-2.184s.22-1.78.22-3.56v-1.67c0-1.78-.22-3.56-.22-3.56zm-13.052 7.254v-6.18l5.944 3.1-5.944 3.08z"></path></svg>',
			'youtube_channel' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M26.78 11.6s-.215-1.515-.875-2.183c-.837-.876-1.774-.88-2.204-.932-3.075-.222-7.693-.222-7.693-.222h-.01s-4.618 0-7.697.222c-.43.05-1.368.056-2.205.932-.66.668-.874 2.184-.874 2.184S5 13.386 5 15.166v1.67c0 1.78.22 3.56.22 3.56s.215 1.516.874 2.184c.837.875 1.936.85 2.426.94 1.76.17 7.48.22 7.48.22s4.623-.007 7.7-.23c.43-.05 1.37-.056 2.205-.932.66-.668.875-2.184.875-2.184s.22-1.78.22-3.56v-1.67c0-1.78-.22-3.56-.22-3.56zm-13.052 7.254v-6.18l5.944 3.1-5.944 3.08z"></path></svg>',
			'rss_feed' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="-4 -4 40 40"><g fill="#fff"><ellipse cx="7.952" cy="24.056" rx="2.952" ry="2.944"></ellipse><path d="M5.153 16.625c2.73 0 5.295 1.064 7.22 2.996a10.2 10.2 0 0 1 2.996 7.255h4.2c0-7.962-6.47-14.44-14.42-14.44v4.193zm.007-7.432c9.724 0 17.636 7.932 17.636 17.682H27C27 14.812 17.203 5 5.16 5v4.193z"></path></g></svg>',
			'gab' => '<svg focusable="false" aria-hidden="true" version="1.1" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-14.5 3.5 46 30" xml:space="preserve"><g><path fill="#fff" d="M13.8,7.6h-2.4v0.7V9l-0.4-0.3C10.2,7.8,9,7.2,7.7,7.2c-0.2,0-0.4,0-0.4,0c-0.1,0-0.3,0-0.5,0 c-5.6,0.3-8.7,7.2-5.4,12.1c2.3,3.4,7.1,4.1,9.7,1.5l0.3-0.3l0,0.7c0,1-0.1,1.5-0.4,2.2c-1,2.4-4.1,3-6.8,1.3 c-0.2-0.1-0.4-0.2-0.4-0.2c-0.1,0.1-1.9,3.5-1.9,3.6c0,0.1,0.5,0.4,0.8,0.6c2.2,1.4,5.6,1.7,8.3,0.8c2.7-0.9,4.5-3.2,5-6.4 c0.2-1.1,0.2-0.8,0.2-8.4l0-7.1H13.8z M9.7,17.6c-2.2,1.2-4.9-0.4-4.9-2.9C4.8,12.6,7,11,9,11.6C11.8,12.4,12.3,16.1,9.7,17.6z"></path></g></svg>',
			'gettr' => '<svg focusable="false" aria-hidden="true" width="100%" height="100%" viewBox="-178 -102 1380 1380" fill="none" xmlns="http://www.w3.org/2000/svg"><rect width="1024" height="1024" rx="240"></rect><path d="M620.01 810.414C548.28 810.414 476.551 810.414 405.435 810.414C407.274 820.836 409.113 831.871 410.952 842.293C426.279 842.293 439.154 853.329 441.606 868.042C450.189 920.154 459.385 971.652 467.968 1023.76C498.008 1023.76 528.049 1023.76 557.476 1023.76C566.059 971.652 575.256 920.154 583.839 868.042C586.291 853.329 599.165 842.293 614.492 842.293C616.331 831.871 618.171 820.836 620.01 810.414C618.171 820.836 616.331 831.871 614.492 842.293Z" fill="#fff"></path><path fill="#fff" d="M789.83 628.333C604.682 628.333 420.148 628.333 235 628.333C235 636.303 235 644.273 235 652.243C344.74 677.992 379.072 718.455 394.399 762.596C472.872 762.596 551.958 762.596 630.431 762.596C645.145 718.455 680.09 677.992 789.83 652.243C789.83 644.273 789.83 636.303 789.83 628.333Z"></path><path fill="#fff" d="M610.2 250.68C640.241 298.499 659.246 345.093 652.502 388.008C640.241 471.999 534.179 529.014 512.722 581.126C435.475 502.039 388.268 448.089 380.911 398.43C369.263 305.243 502.912 229.835 512.722 125C536.631 155.041 543.988 208.378 543.988 238.418C555.637 223.092 562.38 204.086 562.994 184.468C585.677 211.443 593.034 258.037 593.034 292.982C602.843 281.333 609.587 266.62 610.2 250.68Z"></path></svg>',
			'x' => '<svg width="100%" height="100%" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="#fff" d="M21.751 7h3.067l-6.7 7.658L26 25.078h-6.172l-4.833-6.32-5.531 6.32h-3.07l7.167-8.19L6 7h6.328l4.37 5.777L21.75 7Zm-1.076 16.242h1.7L11.404 8.74H9.58l11.094 14.503Z"></path></svg>',
			'yelp' => '<svg width="100%" height="100%" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="#fff" d="m12.281 19.143 1.105-.256a1.1 1.1 0 0 0 .109-.029 1.22 1.22 0 0 0 .872-1.452l-.005-.02a1.216 1.216 0 0 0-.187-.411 1.537 1.537 0 0 0-.451-.387 4.174 4.174 0 0 0-.642-.297l-1.211-.442c-.68-.253-1.36-.5-2.047-.74-.445-.158-.822-.297-1.15-.399-.061-.019-.13-.038-.185-.057-.396-.122-.675-.172-.91-.173a1.084 1.084 0 0 0-.46.083 1.173 1.173 0 0 0-.398.283c-.056.063-.108.13-.157.198a2.342 2.342 0 0 0-.232.464A6.289 6.289 0 0 0 6 17.572c.004.634.021 1.448.37 2 .084.142.197.265.33.36.25.171.5.194.762.213.39.028.768-.068 1.145-.155l3.671-.848h.003Zm12.329-5.868a6.276 6.276 0 0 0-1.2-1.71 2.374 2.374 0 0 0-.412-.315 2.352 2.352 0 0 0-.226-.109 1.169 1.169 0 0 0-.482-.08c-.157.01-.311.053-.45.127-.21.105-.439.273-.742.555-.042.042-.095.089-.142.133-.25.235-.529.525-.86.863-.512.517-1.016 1.037-1.517 1.563l-.896.93c-.164.17-.313.353-.446.548-.114.164-.194.35-.237.545a1.22 1.22 0 0 0 .01.452l.005.02a1.218 1.218 0 0 0 1.419.923.992.992 0 0 0 .11-.021l4.779-1.104c.376-.087.758-.167 1.097-.363.226-.132.442-.262.59-.525a1.18 1.18 0 0 0 .14-.469c.074-.65-.266-1.39-.54-1.963Zm-8.551 2.01c.346-.435.345-1.084.376-1.614.104-1.77.214-3.542.3-5.314.034-.671.106-1.333.067-2.01-.033-.557-.037-1.198-.39-1.656-.621-.807-1.947-.74-2.852-.616-.277.039-.555.09-.83.157-.275.066-.548.138-.815.223-.868.285-2.088.807-2.295 1.807-.116.565.16 1.144.374 1.66.26.625.614 1.189.937 1.778.855 1.554 1.725 3.099 2.593 4.645.259.462.541 1.047 1.043 1.286.033.014.066.027.101.038a1.213 1.213 0 0 0 1.312-.302c.027-.026.054-.054.079-.082Zm-.415 4.741a1.106 1.106 0 0 0-1.23-.415 1.134 1.134 0 0 0-.153.064 1.468 1.468 0 0 0-.217.135c-.2.148-.367.34-.52.532-.038.049-.074.114-.12.156l-.768 1.057c-.436.592-.866 1.186-1.292 1.79-.278.389-.518.718-.708 1.009-.036.054-.073.115-.108.164-.227.352-.356.61-.422.838a1.08 1.08 0 0 0-.046.472c.02.166.076.325.163.468.046.07.096.14.149.206a2.325 2.325 0 0 0 .386.356c.53.37 1.111.634 1.722.84a6.09 6.09 0 0 0 1.572.3 2.403 2.403 0 0 0 .523-.041c.083-.02.165-.044.245-.072.156-.058.298-.149.417-.265.113-.113.2-.25.254-.4.09-.22.148-.502.186-.92.003-.059.012-.13.018-.195.03-.346.044-.753.066-1.232.038-.735.067-1.468.09-2.202l.05-1.306c.011-.3.002-.634-.081-.934a1.397 1.397 0 0 0-.176-.405Zm8.676 2.044c-.161-.176-.388-.352-.747-.568-.052-.03-.112-.068-.168-.101-.299-.18-.658-.369-1.078-.597-.645-.354-1.291-.7-1.943-1.042l-1.151-.61c-.06-.018-.12-.061-.177-.088a2.864 2.864 0 0 0-.7-.25 1.5 1.5 0 0 0-.254-.027c-.055 0-.11.003-.164.01a1.107 1.107 0 0 0-.923.914c-.018.146-.012.294.016.439.056.306.193.61.334.875l.615 1.152c.343.65.689 1.297 1.044 1.94.229.421.42.78.598 1.079.034.056.072.116.101.168.217.358.392.584.569.746a1.104 1.104 0 0 0 .895.302 2.37 2.37 0 0 0 .25-.044 2.384 2.384 0 0 0 .49-.193 6.104 6.104 0 0 0 1.28-.96c.46-.452.867-.945 1.183-1.51.044-.08.082-.162.114-.248.03-.079.055-.16.077-.24a2.46 2.46 0 0 0 .043-.252 1.19 1.19 0 0 0-.057-.491 1.093 1.093 0 0 0-.248-.404Z"></path></svg>',
			'threads' => '<svg width="100%" height="100%" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="#fff" d="M22.067 15.123a8.398 8.398 0 0 0-.315-.142c-.185-3.414-2.05-5.368-5.182-5.388h-.042c-1.873 0-3.431.8-4.39 2.255l1.722 1.181c.716-1.087 1.84-1.318 2.669-1.318h.028c1.031.006 1.81.306 2.313.89.367.426.612 1.015.733 1.757a13.165 13.165 0 0 0-2.96-.143c-2.977.172-4.892 1.909-4.763 4.322.065 1.224.675 2.277 1.717 2.964.88.582 2.015.866 3.194.802 1.558-.085 2.78-.68 3.632-1.766.647-.825 1.056-1.894 1.237-3.241.742.448 1.292 1.037 1.596 1.745.516 1.205.546 3.184-1.068 4.797-1.415 1.414-3.116 2.025-5.686 2.044-2.851-.02-5.008-.935-6.41-2.717-1.313-1.67-1.991-4.08-2.016-7.165.025-3.085.703-5.496 2.016-7.165 1.402-1.782 3.558-2.696 6.41-2.718 2.872.022 5.065.94 6.521 2.731.714.879 1.252 1.983 1.607 3.27l2.018-.538c-.43-1.585-1.107-2.95-2.027-4.083C22.755 5.2 20.025 4.024 16.509 4h-.014c-3.51.024-6.209 1.205-8.022 3.51C6.86 9.56 6.028 12.414 6 15.992v.016c.028 3.578.86 6.431 2.473 8.482 1.813 2.305 4.512 3.486 8.022 3.51h.014c3.12-.022 5.319-.838 7.13-2.649 2.371-2.368 2.3-5.336 1.518-7.158-.56-1.307-1.629-2.369-3.09-3.07Zm-5.387 5.065c-1.305.074-2.66-.512-2.727-1.766-.05-.93.662-1.969 2.807-2.092.246-.015.487-.021.724-.021.78 0 1.508.075 2.171.22-.247 3.088-1.697 3.59-2.975 3.66Z"></path></svg>',
			'tiktok' => '<svg width="100%" height="100%" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path d="M23.34 8.81A5.752 5.752 0 0 1 20.72 4h-4.13v16.54c-.08 1.85-1.6 3.34-3.47 3.34a3.48 3.48 0 0 1-3.47-3.47c0-1.91 1.56-3.47 3.47-3.47.36 0 .7.06 1.02.16v-4.21c-.34-.05-.68-.07-1.02-.07-4.19 0-7.59 3.41-7.59 7.59 0 2.57 1.28 4.84 3.24 6.22 1.23.87 2.73 1.38 4.35 1.38 4.19 0 7.59-3.41 7.59-7.59v-8.4a9.829 9.829 0 0 0 5.74 1.85V9.74a5.7 5.7 0 0 1-3.13-.93Z" style="fill:#fff"></path></svg>',
			'google_maps' => '<svg width="100%" height="100%" focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32"><path fill="#fff" fill-rule="evenodd" d="M10.714 8.619C11.789 7.329 13.683 6 16.293 6c1.26 0 2.21.33 2.22.333l.004-.002c1.802.582 3.343 1.805 4.276 3.607l-.002.001c.017.03.824 1.423.824 3.423 0 2.16-.835 3.708-.835 3.708-.583 1.207-1.774 2.69-2.25 3.282-.08.1-.14.174-.173.218-.863 1.082-1.74 2.209-2.447 3.476-.36.657-.588 1.352-.879 2.262-.14.418-.321.689-.72.689-.367 0-.538-.16-.727-.692-.307-.964-.483-1.507-.904-2.307a21.213 21.213 0 0 0-1.65-2.44v-.003a37.276 37.276 0 0 0-.907-1.158c-.873-1.086-1.832-2.28-2.403-3.587 0 0-.72-1.414-.72-3.461 0-1.935.75-3.627 1.714-4.73Zm3.441 2.903.002.002-.014.017c-.093.114-.628.81-.628 1.786a2.781 2.781 0 0 0 2.791 2.794c1.06 0 1.756-.585 2.025-.86l.126-.144.052-.065c.15-.203.59-.866.59-1.746 0-1.594-1.339-2.786-2.786-2.786-1.368 0-2.154 1-2.154 1v-.002l-.004.004Z" clip-rule="evenodd"></path></svg>',
			'snapchat' => '<svg xmlns="http://www.w3.org/2000/svg" height="100%" width="100%" viewBox="0 0 32 32"><path fill="#fff" d="M26.177 20.978c-2.867-.473-4.157-3.414-4.21-3.54l-.01-.02c-.153-.31-.187-.57-.1-.772.164-.39.774-.583 1.177-.71.113-.037.22-.07.306-.105.715-.28 1.073-.625 1.066-1.03-.006-.312-.252-.593-.642-.732a1.168 1.168 0 0 0-.44-.084.975.975 0 0 0-.405.083c-.34.16-.65.246-.91.258a.789.789 0 0 1-.357-.087l.027-.45.005-.062c.09-1.432.203-3.215-.266-4.264C20.03 6.34 17.073 6.1 16.2 6.1h-.052l-.363.003c-.87 0-3.818.243-5.208 3.36-.47 1.05-.357 2.833-.268 4.264l.03.513a.83.83 0 0 1-.41.09c-.276 0-.6-.087-.97-.26a.795.795 0 0 0-.335-.067c-.43 0-.946.282-1.026.704-.06.305.077.748 1.054 1.134.087.036.193.07.305.105.403.128 1.012.322 1.18.71.084.203.05.463-.103.773l-.01.022c-.054.125-1.344 3.068-4.21 3.54a.437.437 0 0 0-.366.455.6.6 0 0 0 .048.196c.216.504 1.123.87 2.775 1.13.055.075.113.34.148.5.036.16.07.32.12.494.05.17.18.374.514.374.133 0 .292-.03.475-.067.275-.053.652-.127 1.124-.127.26 0 .532.022.805.067.532.09.985.41 1.51.78.75.53 1.6 1.132 2.894 1.132.034 0 .07 0 .105-.005.04.002.095.004.153.004 1.29 0 2.142-.6 2.892-1.132.526-.37.978-.69 1.51-.78.274-.045.545-.068.807-.068.45 0 .805.056 1.123.12.2.037.36.057.476.057h.024c.246 0 .42-.13.488-.365.05-.17.086-.327.12-.49.037-.16.094-.422.15-.496 1.65-.256 2.56-.624 2.773-1.125a.568.568 0 0 0 .047-.196.433.433 0 0 0-.363-.458z"></path></svg>',
			'google_news' => '<svg height="100%" width="100%" focusable="false" aria-hidden="true" viewBox="35 45 80 80" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path fill="#fff" d="M99.6,77.8H78.2v-5.6h21.4c0.6,0,1.1,0.5,1.1,1.1v3.4C100.7,77.3,100.2,77.8,99.6,77.8z"></path><path fill="#fff" d="M99.6,99.2H78.2v-5.6h21.4c0.6,0,1.1,0.5,1.1,1.1v3.4C100.7,98.7,100.2,99.2,99.6,99.2z"></path><path fill="#fff" d="M103,88.5H78.2v-5.6H103c0.6,0,1.1,0.5,1.1,1.1v3.4C104.1,88,103.6,88.5,103,88.5z"></path><path fill="#fff" d="M59.1,83.4v5.1h7.3c-0.6,3.1-3.3,5.3-7.3,5.3c-4.4,0-8-3.7-8-8.2c0-4.4,3.6-8.2,8-8.2c2,0,3.8,0.7,5.2,2v0 l3.9-3.9c-2.3-2.2-5.4-3.5-9-3.5c-7.5,0-13.5,6-13.5,13.5c0,7.5,6,13.5,13.5,13.5C66.9,99.2,72,93.7,72,86c0-0.9-0.1-1.7-0.2-2.6 H59.1z"></path></svg>'

		);

		$bg_color = array(
			'facebook' => '3c589a',
			'twitter' => '55acee',
			'instagram' => '53beee',
			'parler' => '3c589a',
			'pinterest' => 'cc2329',
			'behance' => '053eff',
			'flickr' => 'ff0084',
			'foursquare' => 'f94877',
			'github' => '2a2a2a',
			'linkedin' => '0077b5',
			'linkedin_company' => '0077b5',
			'medium' => '2a2a2a',
			'mewe' => '007da1',
			'odnoklassniki' => 'f2720c',
			'telegram' => '3da5f1',
			'tumblr' => '29435d',
			'vimeo' => '1ab7ea',
			'vkontakte' => 'cc2329',
			'whatsapp' => '55eb4c',
			'xing' => '00797d',
			'youtube' => 'ff0000',
			'youtube_channel' => 'ff0000',
			'rss_feed' => 'e3702d',
			'gab' => '25CC80',
			'gettr' => 'E50000',
			'truth_social' => '6700ed',
			'x' => '2a2a2a',
			'yelp' => 'ff1a1a',
			'threads' => '2a2a2a',
			'tiktok' => '2a2a2a',
			'google_maps' => '34a853',
			'snapchat' => 'ffe900',
			'google_news' => '55acee'
		);

		$html = '';

		if ( $social_networks ) {
			$networks = explode( ',', esc_attr( $social_networks ) );

			$icon_style = 'width:' . esc_attr( $width ) . 'px;height:' . esc_attr( $height ) . 'px;' . ( $shape == 'round' ? 'border-radius:999px;' : '' );

			if ( $theme == '' ) {
				$icon_theme = $theme;
			} elseif ( $theme == 'standard' ) {
				$icon_theme = $theme . '_';
			} elseif ( $theme == 'floating' ) {
				$icon_theme = $theme . '_';
			}

			$html .= '<div ' . ( $type == 'floating' ? 'style="position:fixed;top:' . esc_attr( $top ) . 'px;' . esc_attr( $align ) . ':' . esc_attr( $$align ) . 'px;width:' . esc_attr( $width ) . 'px;"'  : '' ) . 'class="heateor_sss_' . esc_attr( $icon_theme ) . 'follow_icons_container">';

			if ( ! empty( $title ) ) {
				if ( $type == 'floating' ) {
					$html .= '<div class="heateor_sss_follow_icons_title" style="text-align:center;font-size:' . esc_attr( $width )*30/100 . 'px">';
				}
				$html .= esc_html( $title );
				if ( $type == 'floating' ) {
					$html .= '</div>';
				}
			}

			$html .= '<div class="heateor_sss_follow_ul">';
			
			// follow icons
			foreach ( $networks as $value ) {
				$networks_link = explode( '=', trim( $value ) );
				$html .= '<a aria-label="' . ucfirst( trim( str_replace( "_", " ", $networks_link[0] ) ) ) . '" class="heateor_sss_' . strtolower( trim( $networks_link[0] ) ) . '" href="' . urldecode( trim( $networks_link[1] ) ) . '" title="' . ucfirst( trim( str_replace( "_", " ", $networks_link[0] ) ) ) . '" rel="noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#' . $bg_color[strtolower( trim( $networks_link[0] ) )] . ';display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;' . $icon_style . '" class="heateor_sss_svg">' . $svg[strtolower( trim( $networks_link[0] ) )] . '</span></a>';
			}
			$html .= '</div>';
			$html .= '<div style="clear:both"></div>';
			$html .= '</div>';

		}

		return $html;

	}

	/** 
	 * Shortcode for Social Sharing
	 *
	 * @since    1.0
	 */ 
	public function sharing_shortcode( $params ) {
		
		extract( shortcode_atts( array(
			'style' => '',
			'type' => 'standard',
			'left' => '0',
			'right' => '0',
			'top' => '100',
			'url' => '',
			'count' => 0,
			'align' => 'left',
			'title' => '',
			'total_shares' => 'OFF'
		), $params ) );
		
		$type = strtolower( $type );

		if ( ( $type == 'standard' && ! isset( $this->options['hor_enable'] ) ) || ( $type == 'floating' && ! isset( $this->options['vertical_enable'] ) ) || ( ! isset( $this->options['amp_enable'] ) && $this->public_class_object->is_amp_page() ) ) {
			return;
		}
		global $post;
		if ( ! is_object( $post ) ) {
	        return;
		}
		if ( $url ) {
			$target_url = $url;
			$post_id = 0;
		} elseif ( is_front_page() ) {
			$target_url = esc_url( home_url() );
			$post_id = 0;
		} elseif ( ! is_singular() && $type == 'vertical' ) {
			$target_url = esc_url_raw( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
			$post_id = 0;
		} elseif ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] ) {
			$target_url = esc_url_raw( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
			$post_id = $post -> ID;
		} elseif ( get_permalink( $post -> ID ) ) {
			$target_url = get_permalink( $post -> ID );
			$post_id = $post -> ID;
		} else {
			$target_url = esc_url_raw( $this->public_class_object->get_http_protocol() . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"] );
			$post_id = 0;
		}
		$share_count_url = $target_url;
		if ( $url == '' && is_singular() ) {
			$share_count_url = get_permalink( $post -> ID );
		}
		$custom_post_url = $this->public_class_object->apply_target_share_url_filter( $target_url, esc_attr( $type ), false );
		if ( $custom_post_url != $target_url ) {
			$target_url = $custom_post_url;
			$share_count_url = $target_url;
		}
		// generate short url
		$short_url = $this->public_class_object->get_short_url( $target_url, $post_id );
		$alignment_offset = 0;
		if ( $left) {
			$alignment_offset = $left;
		} elseif ( $right) {
			$alignment_offset = $right;
		}

		// share count transient ID
		$this->public_class_object->share_count_transient_id = $this->public_class_object->get_share_count_transient_id( $target_url );
		$cached_share_count = $this->public_class_object->get_cached_share_count( $this->public_class_object->share_count_transient_id );

		if ( isset( $this->options['js_when_needed'] ) && ! wp_script_is( 'heateor_sss_sharing_js' ) ) {
			$in_footer = isset( $this->options['footer_script'] ) ? true : false;
			$inline_script = 'function heateorSssLoadEvent(e) {var t=window.onload;if (typeof window.onload!="function") {window.onload=e}else{window.onload=function() {t();e()}}};';
			$inline_script .= 'var heateorSssSharingAjaxUrl = \''. get_admin_url() .'admin-ajax.php\', heateorSssCloseIconPath = \''. plugins_url( '../images/close.png', __FILE__ ) .'\', heateorSssPluginIconPath = \''. plugins_url( '../images/logo.png', __FILE__ ) .'\', heateorSssHorizontalSharingCountEnable = '. ( isset( $this->options['hor_enable'] ) && ( isset( $this->options['horizontal_counts'] ) || isset( $this->options['horizontal_total_shares'] ) ) ? 1 : 0 ) .', heateorSssVerticalSharingCountEnable = '. ( isset( $this->options['vertical_enable'] ) && ( isset( $this->options['vertical_counts'] ) || isset( $this->options['vertical_total_shares'] ) ) ? 1 : 0 ) .', heateorSssSharingOffset = '. ( isset( $this->options['alignment'] ) && $this->options['alignment'] != '' && isset( $this->options[$this->options['alignment'].'_offset'] ) && $this->options[$this->options['alignment'].'_offset'] != '' ? $this->options[$this->options['alignment'].'_offset'] : 0 ) . '; var heateorSssMobileStickySharingEnabled = ' . ( isset( $this->options['vertical_enable'] ) && isset( $this->options['bottom_mobile_sharing'] ) && $this->options['horizontal_screen_width'] != '' ? 1 : 0 ) . ';';
			$inline_script .= 'var heateorSssCopyLinkMessage = "' . htmlspecialchars( __( 'Link copied.', 'sassy-social-share' ), ENT_QUOTES ) . '";';
			if ( isset( $this->options['horizontal_counts'] ) && isset( $this->options['horizontal_counter_position'] ) ) {
				$inline_script .= in_array( $this->options['horizontal_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceHorizontalSvgWidth = true;' : '';
				$inline_script .= in_array( $this->options['horizontal_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceHorizontalSvgHeight = true;' : '';
			}
			if ( isset( $this->options['vertical_counts'] ) ) {
				$inline_script .= isset( $this->options['vertical_counter_position'] ) && in_array( $this->options['vertical_counter_position'], array( 'inner_left', 'inner_right' ) ) ? 'var heateorSssReduceVerticalSvgWidth = true;' : '';
				$inline_script .= ! isset( $this->options['vertical_counter_position'] ) || in_array( $this->options['vertical_counter_position'], array( 'inner_top', 'inner_bottom' ) ) ? 'var heateorSssReduceVerticalSvgHeight = true;' : '';
			}
			$inline_script .= 'var heateorSssUrlCountFetched = [], heateorSssSharesText = \''. htmlspecialchars( __( 'Shares', 'sassy-social-share' ), ENT_QUOTES ) . '\', heateorSssShareText = \'' . htmlspecialchars( __( 'Share', 'sassy-social-share' ), ENT_QUOTES ) . '\';';
			$inline_script .= 'function heateorSssPopup(e) {window.open(e,"popUpWindow","height=400,width=600,left=400,top=100,resizable,scrollbars,toolbar=0,personalbar=0,menubar=no,location=no,directories=no,status")}';
			if ( $this->public_class_object->facebook_like_recommend_enabled() || $this->public_class_object->facebook_share_enabled() ) {
				$inline_script .= 'function heateorSssInitiateFB() {FB.init({appId:"",channelUrl:"",status:!0,cookie:!0,xfbml:!0,version:"v18.0"})}window.fbAsyncInit=function() {heateorSssInitiateFB(),' . ( defined( 'HEATEOR_SOCIAL_SHARE_MYCRED_INTEGRATION_VERSION' ) && $this->public_class_object->facebook_like_recommend_enabled() ? 1 : 0 ) . '&&(FB.Event.subscribe("edge.create",function(e) {heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"")}),FB.Event.subscribe("edge.remove",function(e) {heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"","Minus point(s) for undoing Facebook like-recommend")}) ),'. ( defined( 'HEATEOR_SHARING_GOOGLE_ANALYTICS_VERSION' ) ? 1 : 0 ) .'&&(FB.Event.subscribe("edge.create",function(e) {heateorSsgaSocialPluginsTracking("Facebook","Like",e?e:"")}),FB.Event.subscribe("edge.remove",function(e) {heateorSsgaSocialPluginsTracking("Facebook","Unlike",e?e:"")}) )},function(e) {var n,i="facebook-jssdk",o=e.getElementsByTagName("script")[0];e.getElementById(i)||(n=e.createElement("script"),n.id=i,n.async=!0,n.src="//connect.facebook.net/'. ( $this->options['language'] ? $this->options['language'] : 'en_GB' ) .'/sdk.js",o.parentNode.insertBefore(n,o) )}(document);';
			}
			wp_enqueue_script( 'heateor_sss_sharing_js', plugins_url( '../public/js/sassy-social-share-public.js', __FILE__ ), array( 'jquery' ), $this->public_class_object->version, $in_footer );
			wp_add_inline_script( 'heateor_sss_sharing_js', $inline_script, $position = 'before' );
		}

		$html = '<div class="heateor_sss_sharing_container heateor_sss_' . ( $type == 'standard' ? 'horizontal' : 'vertical' ) . '_sharing' . ( $type == 'floating' && isset( $this->options['hide_mobile_sharing'] ) ? ' heateor_sss_hide_sharing' : '' ) . ( $type == 'floating' && isset( $this->options['bottom_mobile_sharing'] ) ? ' heateor_sss_bottom_sharing' : '' ) . '" data-heateor-ss-offset="' . esc_attr( $alignment_offset ) . '" ' . ( $this->public_class_object->is_amp_page() ? "" : "data-heateor-sss-href='" . esc_url( isset( $share_count_url ) && $share_count_url ? $share_count_url : $target_url ) . "'" ) . ( ( $cached_share_count === false || $this->public_class_object->is_amp_page() ) ? "" : 'data-heateor-sss-no-counts="1" ' );
		$vertical_offsets = '';
		if ( $type == 'floating' ) {
			$vertical_offsets = esc_attr( $align ) . ': ' . esc_attr( $$align ) . 'px; top: ' . esc_attr( $top ) . 'px;width:' . ( ( $this->options['vertical_sharing_size'] ? $this->options['vertical_sharing_size'] : '35' ) + 4 ) . "px;";
		}
		// style 
		if ( $style != "" || $vertical_offsets != '' ) {
			$html .= 'style="';
			if ( strpos( $style, 'background' ) === false ) { $html .= '-webkit-box-shadow:none;box-shadow:none;'; }
			$html .= $vertical_offsets;
			$html .= esc_attr( $style );
			$html .= '"';
		}
		$html .= '>';
		if ( $type == 'standard' && $title != '' ) {
			$html .= '<div class="heateor_sss_sharing_title" style="font-weight:bold">' . esc_html( ucfirst( $title ) ) . '</div>';
		}
		
		$html .= $this->public_class_object->prepare_sharing_html( $short_url ? $short_url : $target_url, $type == 'standard' ? 'horizontal' : 'vertical', $count, $total_shares == 'ON' ? 1 : 0 );
		$html .= '</div>';
		if ( ( $count || $total_shares == 'ON' )  && $cached_share_count === false ) {
			$html .= '<script>heateorSssLoadEvent(function(){heateorSssGetSharingCounts();});</script>';
		}
		return $html;
	}

}

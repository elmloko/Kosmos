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
			'behance' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path d="M3.862 8.136h5.66c1.377 0 3.19 0 4.13.566a3.705 3.705 0 0 1 1.837 3.26c0 1.66-.88 2.905-2.32 3.494v.042c1.924.397 2.97 1.838 2.97 3.76 0 2.297-1.636 4.483-4.743 4.483H3.86V8.14zm2.078 6.71h4.152c2.36 0 3.322-.856 3.322-2.493 0-2.16-1.53-2.468-3.322-2.468H5.94v4.96zm0 7.144h5.2c1.792 0 2.93-1.09 2.93-2.797 0-2.03-1.64-2.598-3.388-2.598H5.94v5.395zm22.017-1.833C27.453 22.65 25.663 24 23.127 24c-3.607 0-5.31-2.49-5.422-5.944 0-3.386 2.23-5.878 5.31-5.878 4 0 5.225 3.74 5.116 6.47h-8.455c-.067 1.966 1.05 3.716 3.52 3.716 1.53 0 2.6-.742 2.928-2.206h1.838zm-1.793-3.15c-.088-1.77-1.42-3.19-3.256-3.19-1.946 0-3.106 1.466-3.236 3.19h6.492zM20.614 8h4.935v1.68h-4.94z" fill="#fff"></path></svg>',
			'flickr' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path d="M3.862 8.136h5.66c1.377 0 3.19 0 4.13.566a3.705 3.705 0 0 1 1.837 3.26c0 1.66-.88 2.905-2.32 3.494v.042c1.924.397 2.97 1.838 2.97 3.76 0 2.297-1.636 4.483-4.743 4.483H3.86V8.14zm2.078 6.71h4.152c2.36 0 3.322-.856 3.322-2.493 0-2.16-1.53-2.468-3.322-2.468H5.94v4.96zm0 7.144h5.2c1.792 0 2.93-1.09 2.93-2.797 0-2.03-1.64-2.598-3.388-2.598H5.94v5.395zm22.017-1.833C27.453 22.65 25.663 24 23.127 24c-3.607 0-5.31-2.49-5.422-5.944 0-3.386 2.23-5.878 5.31-5.878 4 0 5.225 3.74 5.116 6.47h-8.455c-.067 1.966 1.05 3.716 3.52 3.716 1.53 0 2.6-.742 2.928-2.206h1.838zm-1.793-3.15c-.088-1.77-1.42-3.19-3.256-3.19-1.946 0-3.106 1.466-3.236 3.19h6.492zM20.614 8h4.935v1.68h-4.94z" fill="#fff"></path></svg>',
			'foursquare' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-4 -4 40 40"><path fill="#fff" d="M21.516 3H7.586C5.66 3 5 4.358 5 5.383v21.995c0 1.097.65 1.407.958 1.53.31.126 1.105.206 1.676-.36l6.72-7.455c.105-.12.49-.284.552-.284h4.184c1.79 0 1.81-1.45 1.997-2.206.157-.63 1.946-9.57 2.58-12.395.523-2.32-.104-3.21-2.15-3.21zM20.2 9.682c-.07.33-.368.66-.75.693h-5.44c-.61-.034-1.108.422-1.108 1.032v.665c0 .61.5 1.24 1.108 1.24h4.607c.43 0 .794.276.7.737-.093.46-.573 2.82-.627 3.07-.052.254-.282.764-.716.764h-3.62c-.682 0-1.36-.008-1.816.56-.458.573-4.534 5.293-4.534 5.293V6.403c0-.438.31-.746.715-.74h11.274c.41-.006.915.41.834 1L20.2 9.68z"></path></svg>',
			'github' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path fill="#fff" d="M16 3.32c-7.182 0-13 5.82-13 13 0 5.754 3.72 10.612 8.89 12.335.65.114.893-.276.893-.617 0-.31-.016-1.333-.016-2.42-3.266.6-4.11-.797-4.37-1.53-.147-.373-.78-1.527-1.334-1.835-.455-.244-1.105-.845-.016-.86 1.024-.017 1.755.942 2 1.332 1.17 1.966 3.038 1.414 3.785 1.073.114-.845.455-1.414.83-1.74-2.893-.324-5.916-1.445-5.916-6.418 0-1.414.504-2.584 1.333-3.494-.13-.325-.59-1.657.13-3.445 0 0 1.085-.34 3.57 1.337 1.04-.293 2.146-.44 3.25-.44s2.21.147 3.25.44c2.49-1.69 3.58-1.337 3.58-1.337.714 1.79.26 3.12.13 3.446.828.91 1.332 2.064 1.332 3.494 0 4.99-3.04 6.094-5.93 6.42.47.405.876 1.185.876 2.404 0 1.74-.016 3.136-.016 3.575 0 .34.244.743.894.613C25.28 26.933 29 22.053 29 16.32c0-7.182-5.817-13-13-13z"></path></svg>',
			'linkedin' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 0 1 0 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="#fff"></path></svg>',
			'linkedin_company' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path d="M6.227 12.61h4.19v13.48h-4.19V12.61zm2.095-6.7a2.43 2.43 0 0 1 0 4.86c-1.344 0-2.428-1.09-2.428-2.43s1.084-2.43 2.428-2.43m4.72 6.7h4.02v1.84h.058c.56-1.058 1.927-2.176 3.965-2.176 4.238 0 5.02 2.792 5.02 6.42v7.395h-4.183v-6.56c0-1.564-.03-3.574-2.178-3.574-2.18 0-2.514 1.7-2.514 3.46v6.668h-4.187V12.61z" fill="#fff"></path></svg>',
			'medium' => '<svg focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path d="M7.8 11a.8.8 0 0 0-.27-.7l-2-2.42v-.41h6.23L16.57 18l4.24-10.53h5.94v.36L25 9.47a.5.5 0 0 0-.19.48v12.1a.5.5 0 0 0 .19.48l1.68 1.64v.36h-8.4v-.36L20 22.49c.18-.17.18-.22.18-.49v-9.77l-4.82 12.26h-.65L9.09 12.23v8.22a1.09 1.09 0 0 0 .31.94l2.25 2.74v.36h-6.4v-.36l2.26-2.74a1.09 1.09 0 0 0 .29-.94z" fill="#fff"></path></svg>',
			'mewe' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-3 -3 38 38"><g fill="#fff"><path d="M9.636 10.427a1.22 1.22 0 1 1-2.44 0 1.22 1.22 0 1 1 2.44 0zM15.574 10.431a1.22 1.22 0 0 1-2.438 0 1.22 1.22 0 1 1 2.438 0zM22.592 10.431a1.221 1.221 0 1 1-2.443 0 1.221 1.221 0 0 1 2.443 0zM29.605 10.431a1.221 1.221 0 1 1-2.442 0 1.221 1.221 0 0 1 2.442 0zM3.605 13.772c0-.471.374-.859.859-.859h.18c.374 0 .624.194.789.457l2.935 4.597 2.95-4.611c.18-.291.43-.443.774-.443h.18c.485 0 .859.387.859.859v8.113a.843.843 0 0 1-.859.845.857.857 0 0 1-.845-.845V16.07l-2.366 3.559c-.18.276-.402.443-.72.443-.304 0-.526-.167-.706-.443l-2.354-3.53V21.9c0 .471-.374.83-.845.83a.815.815 0 0 1-.83-.83v-8.128h-.001zM14.396 14.055a.9.9 0 0 1-.069-.333c0-.471.402-.83.872-.83.415 0 .735.263.845.624l2.23 6.66 2.187-6.632c.139-.402.428-.678.859-.678h.124c.428 0 .735.278.859.678l2.187 6.632 2.23-6.675c.126-.346.415-.609.83-.609.457 0 .845.361.845.817a.96.96 0 0 1-.083.346l-2.867 8.032c-.152.43-.471.706-.887.706h-.165c-.415 0-.721-.263-.872-.706l-2.161-6.328-2.16 6.328c-.152.443-.47.706-.887.706h-.165c-.415 0-.72-.263-.887-.706l-2.865-8.032z"></path></g></svg>',
			'odnoklassniki' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M16 16.16a6.579 6.579 0 0 1-6.58-6.58A6.578 6.578 0 0 1 16 3a6.58 6.58 0 1 1 .002 13.16zm0-9.817a3.235 3.235 0 0 0-3.236 3.237 3.234 3.234 0 0 0 3.237 3.236 3.236 3.236 0 1 0 .004-6.473zm7.586 10.62c.647 1.3-.084 1.93-1.735 2.99-1.395.9-3.313 1.238-4.564 1.368l1.048 1.05 3.877 3.88c.59.59.59 1.543 0 2.133l-.177.18c-.59.59-1.544.59-2.134 0l-3.88-3.88-3.877 3.88c-.59.59-1.543.59-2.135 0l-.176-.18a1.505 1.505 0 0 1 0-2.132l3.88-3.877 1.042-1.046c-1.25-.127-3.19-.465-4.6-1.37-1.65-1.062-2.38-1.69-1.733-2.99.37-.747 1.4-1.367 2.768-.29C13.035 18.13 16 18.13 16 18.13s2.968 0 4.818-1.456c1.368-1.077 2.4-.457 2.768.29z"></path></svg>',
			'telegram' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M25.515 6.896L6.027 14.41c-1.33.534-1.322 1.276-.243 1.606l5 1.56 1.72 5.66c.226.625.115.873.77.873.506 0 .73-.235 1.012-.51l2.43-2.363 5.056 3.734c.93.514 1.602.25 1.834-.863l3.32-15.638c.338-1.363-.52-1.98-1.41-1.577z"></path></svg>',
			'tumblr' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-2 -2 36 36"><path fill="#fff" d="M20.775 21.962c-.37.177-1.08.33-1.61.345-1.598.043-1.907-1.122-1.92-1.968v-6.217h4.007V11.1H17.26V6.02h-2.925s-.132.044-.144.15c-.17 1.556-.895 4.287-3.923 5.378v2.578h2.02v6.522c0 2.232 1.647 5.404 5.994 5.33 1.467-.025 3.096-.64 3.456-1.17l-.96-2.846z"/></svg>',
			'vimeo' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-2 -2 36 36"><path fill="#fff" d="M26.926 10.627c-.103 2.25-1.675 5.332-4.716 9.245C19.066 23.957 16.406 26 14.23 26c-1.348 0-2.49-1.244-3.42-3.732l-1.867-6.844C8.25 12.937 7.51 11.69 6.715 11.69c-.173 0-.778.365-1.815 1.09l-1.088-1.4a300.012 300.012 0 0 0 3.374-3.01c1.522-1.315 2.666-2.007 3.427-2.076 1.8-.173 2.907 1.057 3.322 3.69.45 2.84.76 4.608.935 5.3.52 2.356 1.09 3.534 1.713 3.534.483 0 1.21-.764 2.18-2.294.97-1.528 1.488-2.692 1.558-3.49.14-1.32-.38-1.98-1.553-1.98-.554 0-1.125.126-1.712.378 1.137-3.722 3.308-5.53 6.513-5.426 2.378.068 3.498 1.61 3.36 4.62z"></path></svg>',
			'vkontakte' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-1 -2 34 34"><path fill-rule="evenodd" clip-rule="evenodd" fill="#fff" d="M15.764 22.223h1.315s.394-.044.6-.262c.184-.2.18-.574.18-.574s-.03-1.764.79-2.023c.81-.255 1.844 1.705 2.942 2.46.832.57 1.464.445 1.464.445l2.936-.04s1.538-.097.81-1.304c-.06-.1-.426-.894-2.186-2.526-1.843-1.71-1.594-1.434.624-4.39 1.353-1.804 1.893-2.902 1.724-3.374-.16-.45-1.153-.33-1.153-.33l-3.306.02s-.247-.034-.428.074c-.178.108-.293.356-.293.356s-.522 1.394-1.223 2.58c-1.47 2.5-2.06 2.633-2.3 2.476-.563-.36-.42-1.454-.42-2.23 0-2.423.365-3.435-.72-3.696-.357-.085-.623-.143-1.544-.15-1.182-.014-2.18.003-2.743.28-.378.185-.667.595-.49.62.218.027.713.13.975.49.34.46.33 1.496.33 1.496s.193 2.852-.46 3.206c-.442.245-1.056-.252-2.37-2.52-.67-1.163-1.18-2.446-1.18-2.446s-.1-.24-.273-.37c-.212-.155-.506-.204-.506-.204l-3.145.02s-.473.015-.647.22c-.154.183-.01.56-.01.56s2.46 5.757 5.245 8.657c2.553 2.66 5.454 2.485 5.454 2.485z"/></svg>',
			'whatsapp' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-5 -5 40 40"><path id="arc1" stroke="#fff" stroke-width="2" fill="none" d="M 11.579798566743314 24.396926207859085 A 10 10 0 1 0 6.808479557110079 20.73576436351046"></path><path d="M 7 19 l -1 6 l 6 -1" stroke="#fff" stroke-width="2" fill="none"></path><path d="M 10 10 q -1 8 8 11 c 5 -1 0 -6 -1 -3 q -4 -3 -5 -5 c 4 -2 -1 -5 -1 -4" fill="#fff"></path></svg>',
			'xing' => '<svg xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-6 -6 42 42"><path d="M 6 9 h 5 l 4 4 l -5 7 h -5 l 5 -7 z m 15 -4 h 5 l -9 13 l 4 8 h -5 l -4 -8 z" fill="#fff"></path></svg>',
			'youtube' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M26.78 11.6s-.215-1.515-.875-2.183c-.837-.876-1.774-.88-2.204-.932-3.075-.222-7.693-.222-7.693-.222h-.01s-4.618 0-7.697.222c-.43.05-1.368.056-2.205.932-.66.668-.874 2.184-.874 2.184S5 13.386 5 15.166v1.67c0 1.78.22 3.56.22 3.56s.215 1.516.874 2.184c.837.875 1.936.85 2.426.94 1.76.17 7.48.22 7.48.22s4.623-.007 7.7-.23c.43-.05 1.37-.056 2.205-.932.66-.668.875-2.184.875-2.184s.22-1.78.22-3.56v-1.67c0-1.78-.22-3.56-.22-3.56zm-13.052 7.254v-6.18l5.944 3.1-5.944 3.08z"></path></svg>',
			'youtube_channel' => '<svg focusable="false" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="0 0 32 32"><path fill="#fff" d="M26.78 11.6s-.215-1.515-.875-2.183c-.837-.876-1.774-.88-2.204-.932-3.075-.222-7.693-.222-7.693-.222h-.01s-4.618 0-7.697.222c-.43.05-1.368.056-2.205.932-.66.668-.874 2.184-.874 2.184S5 13.386 5 15.166v1.67c0 1.78.22 3.56.22 3.56s.215 1.516.874 2.184c.837.875 1.936.85 2.426.94 1.76.17 7.48.22 7.48.22s4.623-.007 7.7-.23c.43-.05 1.37-.056 2.205-.932.66-.668.875-2.184.875-2.184s.22-1.78.22-3.56v-1.67c0-1.78-.22-3.56-.22-3.56zm-13.052 7.254v-6.18l5.944 3.1-5.944 3.08z"></path></svg>',
			'rss_feed' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="-4 -4 40 40"><g fill="#fff"><ellipse cx="7.952" cy="24.056" rx="2.952" ry="2.944"></ellipse><path d="M5.153 16.625c2.73 0 5.295 1.064 7.22 2.996a10.2 10.2 0 0 1 2.996 7.255h4.2c0-7.962-6.47-14.44-14.42-14.44v4.193zm.007-7.432c9.724 0 17.636 7.932 17.636 17.682H27C27 14.812 17.203 5 5.16 5v4.193z"></path></g></svg>',
			'gab' => '<svg version="1.1" xmlns="http://www.w3.org/2000/svg" width="100%" height="100%" viewBox="-14.5 3.5 46 30" xml:space="preserve"><g><path fill="#fff" d="M13.8,7.6h-2.4v0.7V9l-0.4-0.3C10.2,7.8,9,7.2,7.7,7.2c-0.2,0-0.4,0-0.4,0c-0.1,0-0.3,0-0.5,0 c-5.6,0.3-8.7,7.2-5.4,12.1c2.3,3.4,7.1,4.1,9.7,1.5l0.3-0.3l0,0.7c0,1-0.1,1.5-0.4,2.2c-1,2.4-4.1,3-6.8,1.3 c-0.2-0.1-0.4-0.2-0.4-0.2c-0.1,0.1-1.9,3.5-1.9,3.6c0,0.1,0.5,0.4,0.8,0.6c2.2,1.4,5.6,1.7,8.3,0.8c2.7-0.9,4.5-3.2,5-6.4 c0.2-1.1,0.2-0.8,0.2-8.4l0-7.1H13.8z M9.7,17.6c-2.2,1.2-4.9-0.4-4.9-2.9C4.8,12.6,7,11,9,11.6C11.8,12.4,12.3,16.1,9.7,17.6z"></path></g></svg>'
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
			'gab' => '25CC80'
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
				$html .= '<a class="heateor_sss_' . strtolower( trim( $networks_link[0] ) ) . '" href="' . urldecode( trim( $networks_link[1] ) ) . '" title="' . ucfirst( trim( $networks_link[0] ) ) . '" rel="nofollow noopener" target="_blank" style="font-size:32px!important;box-shadow:none;display:inline-block;vertical-align:middle;"><span style="background-color:#' . $bg_color[strtolower( trim( $networks_link[0] ) )] . ';display:inline-block;opacity:1;float:left;font-size:32px;box-shadow:none;display:inline-block;font-size:16px;padding:0 4px;vertical-align:middle;background-repeat:repeat;overflow:hidden;padding:0;cursor:pointer;' . $icon_style . '" class="heateor_sss_svg">' . $svg[strtolower( trim( $networks_link[0] ) )] . '</span></a>';
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
				$inline_script .= 'function heateorSssInitiateFB() {FB.init({appId:"",channelUrl:"",status:!0,cookie:!0,xfbml:!0,version:"v16.0"})}window.fbAsyncInit=function() {heateorSssInitiateFB(),' . ( defined( 'HEATEOR_SOCIAL_SHARE_MYCRED_INTEGRATION_VERSION' ) && $this->public_class_object->facebook_like_recommend_enabled() ? 1 : 0 ) . '&&(FB.Event.subscribe("edge.create",function(e) {heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"")}),FB.Event.subscribe("edge.remove",function(e) {heateorSsmiMycredPoints("Facebook_like_recommend","",e?e:"","Minus point(s) for undoing Facebook like-recommend")}) ),'. ( defined( 'HEATEOR_SHARING_GOOGLE_ANALYTICS_VERSION' ) ? 1 : 0 ) .'&&(FB.Event.subscribe("edge.create",function(e) {heateorSsgaSocialPluginsTracking("Facebook","Like",e?e:"")}),FB.Event.subscribe("edge.remove",function(e) {heateorSsgaSocialPluginsTracking("Facebook","Unlike",e?e:"")}) )},function(e) {var n,i="facebook-jssdk",o=e.getElementsByTagName("script")[0];e.getElementById(i)||(n=e.createElement("script"),n.id=i,n.async=!0,n.src="//connect.facebook.net/'. ( $this->options['language'] ? $this->options['language'] : 'en_GB' ) .'/sdk.js",o.parentNode.insertBefore(n,o) )}(document);';
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

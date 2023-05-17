<?php

if ( ! function_exists( 'rplg_urlopen' ) ) {

	define( 'RPLG_USER_AGENT', 'RPLG-WPPlugin/1.0' );
	define( 'RPLG_SOCKET_TIMEOUT', 10 );

	function rplg_json_decode( $data ) {
		return json_decode( $data );
	}

	function rplg_json_urlopen( $url, $postdata = false, $headers = array() ) {
		if ( ! ( $response = rplg_urlopen( $url, $postdata, $headers ) ) || ! $response['code'] ) {
			// $this->last_error = 'COULDNT_CONNECT';
			return false;
		}
		return rplg_json_decode( $response['data'] );
	}

	function rplg_urlopen( $url, $postdata = false, $headers = array() ) {
		$response = array(
			'data' => '',
			'code' => 0,
		);

		$url = preg_replace( '/\s+/', '+', $url );

		if ( function_exists( 'curl_init' ) ) {
			if ( ! function_exists( 'curl_setopt_array' ) ) {
				function curl_setopt_array( &$ch, $curl_options ) {
					foreach ( $curl_options as $option => $value ) {
						if ( ! curl_setopt( $ch, $option, $value ) ) {
							return false;
						}
					}
					return true;
				}
			}
			_rplg_curl_urlopen( $url, $postdata, $headers, $response );
		} elseif ( ini_get( 'allow_url_fopen' ) && function_exists( 'stream_get_contents' ) ) {
			_rplg_fopen_urlopen( $url, $postdata, $headers, $response );
		} else {
			_rplg_fsockopen_urlopen( $url, $postdata, $headers, $response );
		}
		return $response;
	}

	/*-------------------------------- curl --------------------------------*/
	function _rplg_curl_urlopen( $url, $postdata, $headers, &$response ) {
		$c            = curl_init( $url );
		$postdata_str = rplg_get_query_string( $postdata );

		$c_options = array(
			CURLOPT_USERAGENT      => RPLG_USER_AGENT,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_POST           => ( $postdata_str ? 1 : 0 ),
			CURLOPT_HEADER         => true,
			CURLOPT_HTTPHEADER     => array_merge( array( 'Expect:' ), $headers ),
			CURLOPT_TIMEOUT        => RPLG_SOCKET_TIMEOUT,
		);
		if ( $postdata ) {
			$c_options[ CURLOPT_POSTFIELDS ] = $postdata_str;
		}
		curl_setopt_array( $c, $c_options );

		$open_basedir = ini_get( 'open_basedir' );
		if ( empty( $open_basedir ) ) {
			curl_setopt( $c, CURLOPT_FOLLOWLOCATION, true );
		}
		curl_setopt( $c, CURLOPT_SSL_VERIFYPEER, false );

		$data = curl_exec( $c );

		// cURL automatically handles Proxy rewrites, remove the "HTTP/1.0 200 Connection established" string
		if ( stripos( $data, "HTTP/1.0 200 Connection established\r\n\r\n" ) !== false ) {
			$data = str_ireplace( "HTTP/1.0 200 Connection established\r\n\r\n", '', $data );
		}

		list($resp_headers, $response['data']) = explode( "\r\n\r\n", $data, 2 );

		$response['headers'] = _rplg_get_response_headers( $resp_headers, $response );
		$response['code']    = curl_getinfo( $c, CURLINFO_HTTP_CODE );
		curl_close( $c );
	}

	/*-------------------------------- fopen --------------------------------*/
	function _rplg_fopen_urlopen( $url, $postdata, $headers, &$response ) {
		$params = array();

		if ( $postdata ) {
			$params = array(
				'http' => array(
					'method'  => 'POST',
					'header'  => implode( "\r\n", array_merge( array( 'Content-Type: application/x-www-form-urlencoded' ), $headers ) ),
					'content' => rplg_get_query_string( $postdata ),
					'timeout' => RPLG_SOCKET_TIMEOUT,
				),
			);
		} else {
			$params = array(
				'http' => array(
					'header' => implode( "\r\n", $headers ),
				),
			);
		}

		ini_set( 'user_agent', RPLG_USER_AGENT );
		$ctx = stream_context_create( $params );
		$fp  = fopen( $url, 'rb', false, $ctx );
		if ( ! $fp ) {
			return false; }

		$response_header_array = explode( ' ', $http_response_header[0], 3 );

		$unused = $response_header_array[0];

		$response['code'] = $response_header_array[0];

		$unused = $response_header_array[2];

		$resp_headers = array_slice( $http_response_header, 1 );

		foreach ( $resp_headers as $unused => $header ) {
			$header                                   = explode( ':', $header );
			$header[0]                                = trim( $header[0] );
			$header[1]                                = trim( $header[1] );
			$resp_headers[ strtolower( $header[0] ) ] = strtolower( $header[1] );
		}
		$response['data']    = stream_get_contents( $fp );
		$response['headers'] = $resp_headers;
	}

	/*-------------------------------- fsockpen --------------------------------*/
	function _rplg_fsockopen_urlopen( $url, $postdata, $headers, &$response ) {
		$buf          = '';
		$req          = '';
		$length       = 0;
		$postdata_str = rplg_get_query_string( $postdata );
		$url_pieces   = parse_url( $url );
		$host         = $url_pieces['host'];

		if ( ! isset( $url_pieces['port'] ) ) {
			switch ( $url_pieces['scheme'] ) {
				case 'http':
					$url_pieces['port'] = 80;
					break;
				case 'https':
					$url_pieces['port'] = 443;
					$host               = 'ssl://' . $url_pieces['host'];
					break;
			}
		}

		if ( ! isset( $url_pieces['path'] ) ) {
			$url_pieces['path'] = '/'; }

		if ( ( $url_pieces['port'] == 80 && $url_pieces['scheme'] == 'http' ) ||
			( $url_pieces['port'] == 443 && $url_pieces['scheme'] == 'https' ) ) {
			$req_host = $url_pieces['host'];
		} else {
			$req_host = $url_pieces['host'] . ':' . $url_pieces['port'];
		}

		$fp = @fsockopen( $host, $url_pieces['port'], $errno, $errstr, RPLG_SOCKET_TIMEOUT );
		if ( ! $fp ) {
			return false; }

		$path = $url_pieces['path'];
		if ( isset( $url_pieces['query'] ) ) {
			$path .= '?' . $url_pieces['query'];
		}

		$req .= ( $postdata_str ? 'POST' : 'GET' ) . ' ' . $path . " HTTP/1.1\r\n";
		$req .= 'Host: ' . $req_host . "\r\n";
		$req .= rplg_get_http_headers_for_request( $postdata_str, $headers );
		if ( $postdata_str ) {
			$req .= "\r\n\r\n" . $postdata_str;
		}
		$req .= "\r\n\r\n";

		fwrite( $fp, $req );
		while ( ! feof( $fp ) ) {
			$buf .= fgets( $fp, 4096 );
		}

		list($headers, $response['data']) = explode( "\r\n\r\n", $buf, 2 );

		$headers = _rplg_get_response_headers( $headers, $response );

		if ( isset( $headers['transfer-encoding'] ) && 'chunked' == strtolower( $headers['transfer-encoding'] ) ) {
			$chunk_data  = $response['data'];
			$joined_data = '';
			while ( true ) {
				list($chunk_length, $chunk_data) = explode( "\r\n", $chunk_data, 2 );
				$chunk_length                    = hexdec( $chunk_length );
				if ( ! $chunk_length || ! strlen( $chunk_data ) ) {
					break; }

				$joined_data .= substr( $chunk_data, 0, $chunk_length );
				$chunk_data   = substr( $chunk_data, $chunk_length + 1 );
				$length      += $chunk_length;
			}
			$response['data'] = $joined_data;
		} else {
			$length = $headers['content-length'];
		}
		$response['headers'] = $headers;
	}

	/*-------------------------------- helpers --------------------------------*/
	function rplg_get_query_string( $params ) {
		$query = '';

		if ( $params ) {
			foreach ( $params as $key => $value ) {
				$query .= urlencode( $key ) . '=' . urlencode( $value ) . '&';
			}
		}
		return $query;
	}

	function _rplg_get_response_headers( $headers, &$response ) {
		$headers = explode( "\r\n", $headers );

		$header_array = explode( ' ', $headers[0], 3 );

		$unused = $header_array[0];

		$response['code'] = $header_array[1];

		$unused = $header_array[2];

		$headers = array_slice( $headers, 1 );
		foreach ( $headers as $unused => $header ) {
			$header                              = explode( ':', $header );
			$header[0]                           = trim( $header[0] );
			$header[1]                           = trim( $header[1] );
			$headers[ strtolower( $header[0] ) ] = $header[1];
		}
		return $headers;
	}

	function rplg_get_http_headers_for_request( $content, $headers ) {
		$req_headers   = array();
		$req_headers[] = 'User-Agent: ' . RPLG_USER_AGENT;
		$req_headers[] = 'Connection: close';
		if ( $content ) {
			$req_headers[] = 'Content-Length: ' . strlen( $content );
			$req_headers[] = 'Content-Type: application/x-www-form-urlencoded';
		}
		return implode( "\r\n", array_merge( $req_headers, $headers ) );
	}

	function rplg_url_method() {
		if ( function_exists( 'curl_init' ) ) {
			return 'curl';
		} elseif ( ini_get( 'allow_url_fopen' ) && function_exists( 'stream_get_contents' ) ) {
			return 'fopen';
		} else {
			return 'fsockopen';
		}
	}
}



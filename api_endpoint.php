<?php

    class Api_endpoint extends WP_REST_Controller {

        public function register_routes() {
            $version = '1';
            $namespace = 'api/v' . $version;
            $base = 'testimonials';
            register_rest_route( $namespace, '/' . $base, array(
              array(
                'methods'             => 'WP_REST_Server::READABLE',
                'callback'            => array($this, 'api_get_items'),
                'permission_callback' => array(),
                'args'                => array(),
              ),
            ));
        }

        public function api_get_items()
        {
            $arg    = array('post_type' => 'wt9-testimonial');
            $data   = new WP_Query($arg);

            //return a response or error based on some conditional
            if ( $data ) {
                return new WP_REST_Response( $data, 200 );
            } else {
                return new WP_Error( 'code', __( 'message', 'text-domain' ) );
            }
        }
    }

?>
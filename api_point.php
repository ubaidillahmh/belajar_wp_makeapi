<?php

    add_action('rest_api_init', 'api_regis_show');

    function api_regis_show()
    {
        $version = '1';
        $namespace = 'api/v' . $version;
        $base = 'testimonials';
        register_rest_route($namespace, '/'.$base, array(
            array(
                'methods'   => 'GET',
                'callback'  => 'api_getdata',
                'args'      => array(),
            ),
            array(
                'methods'   => 'POST',
                'callback'  => 'api_insertdata',
                'args'      => array(
                    'title'    => array(
                        'required'          => true,
                        'validate_callback' => function($param, $request, $key) {
                            return is_string( $param );
                          },
                    ),
                    'author'    => array(
                        'required'          => true,
                        'validate_callback' => function($param, $request, $key) {
                            return is_string( $param );
                          },
                    ),
                    'content'   => array(
                        'required'          => true,
                        'validate_callback' => function($param, $request, $key) {
                            return is_string( $param );
                          },
                    ),
                    'date'      => array(
                        'required'  => true,
                        'validate_callback' => function($param, $request, $key) {
                            $checkdate = checkdate(date('m', strtotime($param)), date('d', strtotime($param)), date('Y', strtotime($param)));
                            return $checkdate;
                        }
                    ),
                    'rate'      => array(
                        'required'  => true,
                        'validate_callback' => function ($param, $request, $key){
                            $checkdata = [1, 2, 3, 4, 5];
                            $checkavailability = in_array($param, $checkdata);

                            return $checkavailability;
                        }
                    )
                ),
            ), 
        ));
        register_rest_route($namespace, '/'.$base.'/(?P<id>\d+)', array(
            array(
                'methods'    => 'GET',
                'callback'  => 'api_getone',
                'args'      => array(
                    'id'    => array(
                        'validate_callback' => function($param, $request, $key) {
                            return is_numeric( $param );
                        },
                        'required'          => true
                    )
                )
            ),
            array(
                'methods'   => 'DELETE',
                'callback'  => 'api_deleteone',
                'args'      => array(
                    'id'    => array(
                        'validate_callback' => function($param, $request, $key) {
                            return is_numeric( $param );
                        },
                        'required'          => true
                    )
                )
            ),
            array(
                'methods'   => 'PATCH',
                'callback'  => 'api_updateone',
                'args'      => array(
                    'id'    => array(
                        'validate_callback' => function($param, $request, $key) {
                            return is_numeric( $param );
                        },
                        'required'          => true
                    )
                )
            )

        ));
    }

    function api_getdata($request)
    {
        if($request['page'] != null || $request['per_page'] != null)
        {
            $arg = array(
                'post_type' => 'wt9-testimonial',
                'posts_per_page' => $request['per_page'] != null ? $request['per_page'] : '1',
                'paged'     => $request['page'] != null ? $request['page'] : '1'
            );
            // $data[0]['test'] = $request['page'];
        }else{
            $arg    = array('post_type' => 'wt9-testimonial');
        }

        $init   = new WP_Query($arg);

        $i = 0;
        $data = array();
        while($init->have_posts()):$init->the_post();
            $data[$i]['id']    = get_the_ID() ;
            $data[$i]['title']    = get_the_title() ;
            $data[$i]['content']  = get_the_content(); 
            $data[$i]['author']   = get_the_author();
            $data[$i]['date']     = get_the_date();
            $data[$i]['rate']     = get_post_meta(get_the_ID(), 'rate', true);
            $i++;
        endwhile;

        $return = array(
            'status'    => 'success',
            'message'   => 'Get data Successful',
            'data'      => $data
        );

        //return a response or error based on some conditional
        if ( $data ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 400, 'Something Error' );
        }
    }

    function api_getone($request)
    {
        $arg    = array(
            'post_type' => 'wt9-testimonial',
            'p'         => $request['id']
        );

        $init   = new WP_Query($arg);

        $i = 0;
        $data = array();
        while($init->have_posts()):$init->the_post();
            $data[$i]['id']    = get_the_ID() ;
            $data[$i]['title']    = get_the_title() ;
            $data[$i]['content']  = get_the_content(); 
            $data[$i]['author']   = get_the_author();
            $data[$i]['date']     = get_the_date();
            $data[$i]['rate']     = get_post_meta(get_the_ID(), 'rate', true);
            $i++;
        endwhile;

        $return = array(
            'status'    => 'success',
            'message'   => 'Get data Successful',
            'data'      => $data
        );

        //return a response or error based on some conditional
        if ( $data ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 400, 'Something error' );
        }
    }

    function api_insertdata($request)
    {
        $arg = array(
            'post_type'     => 'wt9-testimonial',
            'post_title'    => $request['title'],
            'post_content'  => $request['content'],
            'post_status'   => 'publish',
            'post_author'   => $request['author'],
            'post_category' => ''
        );

        $ins = wp_insert_post( $arg );
        update_post_meta($ins, 'rate', $request['rate']);

        if($ins)
        {
            $argu    = array(
                'post_type' => 'wt9-testimonial',
                'p'         => $ins
            );
    
            $init   = new WP_Query($argu);
    
            $i = 0;
            $data = array();
            while($init->have_posts()):$init->the_post();
                $data[$i]['id']    = get_the_ID() ;
                $data[$i]['title']    = get_the_title() ;
                $data[$i]['content']  = get_the_content(); 
                $data[$i]['author']   = get_the_author();
                $data[$i]['date']     = get_the_date();
                $data[$i]['rate']     = get_post_meta(get_the_ID(), 'rate', true);
                $i++;
            endwhile;
        }

        $return = array(
            'status'    => 'success',
            'message'   => 'Create data Successful',
            'data'      => $data
        );

        //return a response or error based on some conditional
        if ( $data ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 400, 'Something error' );
        }
    }

    function api_deleteone($request)
    {
        $del    = wp_delete_post( $request['id'], true );

        $return = array(
            'status'    => 'success',
            'message'   => 'Delete data Successful',
        );

        if ( $del ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 400, 'Something Error' );
        }
    }

    function api_updateone($request)
    {
        $arg = array(
            'ID'            => $request['id'],
            'post_title'    => $request['title'],
            'post_content'  => $request['content'],
        );

        $ins = wp_update_post( $arg );

        if($ins)
        {
            $argu    = array(
                'post_type' => 'wt9-testimonial',
                'p'         => $ins
            );
    
            $init   = new WP_Query($argu);
    
            $i = 0;
            $data = array();
            while($init->have_posts()):$init->the_post();
                $data[$i]['id']    = get_the_ID() ;
                $data[$i]['title']    = get_the_title() ;
                $data[$i]['content']  = get_the_content(); 
                $data[$i]['author']   = get_the_author();
                $data[$i]['date']     = get_the_date();
                $i++;
            endwhile;
        }

        $return = array(
            'status'    => 'success',
            'message'   => 'Update data Successful',
            'data'      => $data
        );

        //return a response or error based on some conditional
        if ( $data ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 400, 'Something Error' );
        }
    }

?>
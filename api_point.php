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
                        'required'  => true
                    ),
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

    function api_getdata($isi)
    {
        if($isi['page'] != null || $isi['per_page'] != null)
        {
            $arg = array(
                'post_type' => 'wt9-testimonial',
                'posts_per_page' => $isi['per_page'] != null ? $isi['per_page'] : '1',
                'paged'     => $isi['page'] != null ? $isi['page'] : '1'
            );
            $data[0]['test'] = $isi['page'];
        }else{
            $arg    = array('post_type' => 'wt9-testimonial');
        }

        $inis   = new WP_Query($arg);

        $i = 0;
        while($inis->have_posts()):$inis->the_post();
            $data[$i]['id']    = get_the_ID() ;
            $data[$i]['title']    = get_the_title() ;
            $data[$i]['content']  = get_the_content(); 
            $data[$i]['author']   = get_the_author();
            $data[$i]['date']     = get_the_date();
            $i++;
        endwhile;

        $return = array(
            'status'    => 'success',
            'message'   => 'Berhasil',
            'data'      => $data
        );

        //return a response or error based on some conditional
        if ( $data ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 'code', __( 'message', 'text-domain' ) );
        }
    }

    function api_getone($isi)
    {
        $arg    = array(
            'post_type' => 'wt9-testimonial',
            'p'         => $isi['id']
        );

        $inis   = new WP_Query($arg);

        $i = 0;
        while($inis->have_posts()):$inis->the_post();
            $data[$i]['id']    = get_the_ID() ;
            $data[$i]['title']    = get_the_title() ;
            $data[$i]['content']  = get_the_content(); 
            $data[$i]['author']   = get_the_author();
            $data[$i]['date']     = get_the_date();
            $i++;
        endwhile;

        $return = array(
            'status'    => 'success',
            'message'   => 'Berhasil',
            'data'      => $data
        );

        //return a response or error based on some conditional
        if ( $data ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 'code', __( 'message', 'text-domain' ) );
        }
    }

    function api_insertdata($isi)
    {
        $arg = array(
            'post_type'     => 'wt9-testimonial',
            'post_title'    => $isi['title'],
            'post_content'  => $isi['content'],
            'post_status'   => 'publish',
            'post_author'   => '1',
            'post_category' => ''
        );

        $ins = wp_insert_post( $arg );

        if($ins)
        {
            $argu    = array(
                'post_type' => 'wt9-testimonial',
                'p'         => $ins
            );
    
            $inis   = new WP_Query($argu);
    
            $i = 0;
            while($inis->have_posts()):$inis->the_post();
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
            'message'   => 'Berhasil',
            'data'      => $data
        );

        //return a response or error based on some conditional
        if ( $data ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 'code', __( 'message', 'text-domain' ) );
        }
    }

    function api_deleteone($isi)
    {
        $del    = wp_delete_post( $isi['id'], true );

        $return = array(
            'status'    => 'success',
            'message'   => 'Berhasil menghapus data',
        );

        if ( $del ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 'code', __( 'message', 'text-domain' ) );
        }
    }

    function api_updateone($isi)
    {
        $arg = array(
            'ID'            => $isi['id'],
            'post_title'    => $isi['title'],
            'post_content'  => $isi['content'],
        );

        $ins = wp_update_post( $arg );

        if($ins)
        {
            $argu    = array(
                'post_type' => 'wt9-testimonial',
                'p'         => $ins
            );
    
            $inis   = new WP_Query($argu);
    
            $i = 0;
            while($inis->have_posts()):$inis->the_post();
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
            'message'   => 'Berhasil',
            'data'      => $data
        );

        //return a response or error based on some conditional
        if ( $data ) {
            return new WP_REST_Response( $return, 200 );
        } else {
            return new WP_Error( 'code', __( 'message', 'text-domain' ) );
        }
    }

?>
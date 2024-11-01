<?php
add_shortcode( 'wlsf_search_form', 'wlsf_search_form_func' ); 

function wlsf_search_form_func( $atts ) {

    global $_wp_listings_taxonomies;

    $listings_taxonomies = $_wp_listings_taxonomies->get_taxonomies();

    printf(__('<div class="inner-80 %s">', 'wp-listings-custom-search-form'), $inlineWrapper);

        printf(__('<form role="search" method="get" id="searchformshortcode" action="%1$s" ><input type="hidden" value="" name="s" /><input type="hidden" value="listing" name="post_type" />', 'wp-listings-custom-search-form'), home_url( '/' ));

        foreach ( $listings_taxonomies as $tax => $data ) {

            if(is_array($atts)){
                if (!in_array($tax, $atts))
                continue;

                $terms = get_terms( $tax, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 100, 'hierarchical' => false ) );
                if ( empty( $terms ) )
                    continue;

                $current = ! empty( $wp_query->query_vars[$tax] ) ? $wp_query->query_vars[$tax] : '';

                echo "<select name='$tax' id='$tax' class='wp-listings-taxonomy'>\n\t";
                echo '<option value="" ' . selected( $current == '', true, false ) . ">{$data['labels']['name']}</option>\n";
                foreach ( (array) $terms as $term )
                    echo "\t<option value='{$term->slug}' " . selected( $current, $term->slug, false ) . ">{$term->name}</option>\n";

                echo '</select>';
            }

            else{

                $terms = get_terms( $tax, array( 'orderby' => 'count', 'order' => 'DESC', 'number' => 100, 'hierarchical' => false ) );
                if ( empty( $terms ) )
                    continue;

                $current = ! empty( $wp_query->query_vars[$tax] ) ? $wp_query->query_vars[$tax] : '';

                echo "<select name='$tax' id='$tax' class='wp-listings-taxonomy'>\n\t";
                echo '<option value="" ' . selected( $current == '', true, false ) . ">{$data['labels']['name']}</option>\n";
                foreach ( (array) $terms as $term )
                    echo "\t<option value='{$term->slug}' " . selected( $current, $term->slug, false ) . ">{$term->name}</option>\n";

                echo '</select>';
            }
        }

        printf(__('<div class="Counties %2$s"><button type="submit" class="searchsubmit"><i class="fa fa-search" aria-hidden="true"></i><span class="button-text">%1$s</span></button></div>', 'wp-listings-custom-search-form'), esc_attr( $instance['button_text'] ), $inlineSubmit);
        _e('<div class="clear"></div></form></div>', 'wp-listings-custom-search-form');

}

?>
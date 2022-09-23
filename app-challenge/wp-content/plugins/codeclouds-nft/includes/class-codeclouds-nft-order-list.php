<?php

/**
 * Fired during visiting the order lists in admin.
 *
 * This class defines all code necessary to run during visiting the order lists in admin..
 *
 * @since      1.0.0
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/includes
 * @author     Codeclouds Team <team@codeclouds.biz>
 */
class Codeclouds_Nft_Order_List extends WP_List_Table
{
    function get_columns(): array
    {
        return array(
            'order_number' => __( 'Order Number', 'codeclouds-nft' ),
            'customer_email' => __( 'Customer Email', 'codeclouds-nft' ),
            'order_total'    => __( 'Total', 'codeclouds-nft' ) ,
            'account_address'    => __( 'Account Address', 'codeclouds-nft' ),
            'coupon_title'      => __( 'Coupon Title', 'codeclouds-nft' ),
            'coupon_domain'      => __( 'Coupon Domain', 'codeclouds-nft' ),
            'coupon_amount'      => __( 'Coupon Amount', 'codeclouds-nft' ),
            'order_status'      => __( 'Status', 'codeclouds-nft' ),
            'order_date'      => __( 'Date', 'codeclouds-nft' )
        );
    }

    function column_default( $item, $column_name ) {
        $currency_symbol = get_woocommerce_currency_symbol($item['order_currency']);
        switch( $column_name ) {
            case 'order_number':
                return '<a target="_blank" href="'. $item[ 'order_url' ] . '">' . $item[ 'order_number' ] . '</a>';
            case 'customer_email':
                return '<a target="_blank" href="'.get_edit_user_link($item[ 'user_id' ]).'">' . $item[ $column_name ] . '</a>';
            case 'account_address':
            case 'coupon_title':
            case 'coupon_domain':
                return $item[ $column_name ];
            case 'order_total':
            case 'coupon_amount':
                $amount = !empty($item[ $column_name ]) ? $item[ $column_name ] : '0.00';
                return $currency_symbol.$amount;
            case 'order_status':
                return ucwords($item[ $column_name ]);
            case 'order_date':
                return Codeclouds_Nft_Utils::timeElapsedString($item[ $column_name ]);
            default:
                return '--' ;
        }
    }

    function get_sortable_columns(): array
    {
        return array(
            'order_date'   => array('order_date', true)
        );
    }

    function get_views(): array
    {
        $all_nft_order_query_args = array(
            'post_type'         => 'shop_order',
            'post_status'       => array('wc-pending', 'wc-processing'),
            'meta_query'        => array(
                array(
                    'key' => CODECLOUDS_NFT_COUPON_KEY,
                    'value' => 1
                )
            )
        );
        $all_nft_order_query = new WP_Query( $all_nft_order_query_args );

        $pending_nft_order_query_args = array(
            'post_type'         => 'shop_order',
            'post_status'       => 'wc-pending',
            'meta_query'        => array(
                array(
                    'key' => CODECLOUDS_NFT_COUPON_KEY,
                    'value' => 1
                )
            )
        );
        $pending_nft_order_query = new WP_Query( $pending_nft_order_query_args );

        $processing_nft_order_query_args = array(
            'post_type'         => 'shop_order',
            'post_status'       => 'wc-processing',
            'meta_query'        => array(
                array(
                    'key' => CODECLOUDS_NFT_COUPON_KEY,
                    'value' => 1
                )
            )
        );
        $processing_nft_order_query = new WP_Query( $processing_nft_order_query_args );

        $hold_nft_order_query_args = array(
            'post_type'         => 'shop_order',
            'post_status'       => 'wc-on-hold',
            'meta_query'        => array(
                array(
                    'key' => CODECLOUDS_NFT_COUPON_KEY,
                    'value' => 1
                )
            )
        );
        $hold_nft_order_query = new WP_Query( $hold_nft_order_query_args );

        $completed_nft_order_query_args = array(
            'post_type'         => 'shop_order',
            'post_status'       => 'wc-completed',
            'meta_query'        => array(
                array(
                    'key' => CODECLOUDS_NFT_COUPON_KEY,
                    'value' => 1
                )
            )
        );
        $completed_nft_order_query = new WP_Query( $completed_nft_order_query_args );

        $views = array();
        $current = ( !empty($_REQUEST['post_type']) ? $_REQUEST['post_type'] : 'all');

        $class = ($current == 'all' ? ' class="current"' :'');
        $all_url = remove_query_arg('post_type');
        $views['all'] = "<a href='{$all_url }' {$class} >" . __('All','codeclouds-nft') . "</a> (" . $all_nft_order_query->found_posts . ")";

        $pending_url = add_query_arg('post_type','wc-pending', $_SERVER['PHP_SELF'] . '?page=codeclouds-nft-order');
        $class = ($current == 'wc-pending' ? ' class="current"' :'');
        $views['pending'] = "<a href='{$pending_url}' {$class} >" . __('Pending Payment','codeclouds-nft') . "</a> (" . $pending_nft_order_query->found_posts . ")";

        $processing_url = add_query_arg('post_type','wc-processing', $_SERVER['PHP_SELF'] . '?page=codeclouds-nft-order');
        $class = ($current == 'wc-processing' ? ' class="current"' :'');
        $views['processing'] = "<a href='{$processing_url}' {$class} >" . __('Processing','codeclouds-nft') . "</a> (" . $processing_nft_order_query->found_posts . ")";

        $hold_url = add_query_arg('post_type','wc-on-hold', $_SERVER['PHP_SELF'] . '?page=codeclouds-nft-order');
        $class = ($current == 'wc-on-hold' ? ' class="current"' :'');
        $views['hold'] = "<a href='{$hold_url}' {$class} >" . __('On Hold','codeclouds-nft') . "</a> (" . $hold_nft_order_query->found_posts . ")";

        $completed_url = add_query_arg('post_type','wc-completed', $_SERVER['PHP_SELF'] . '?page=codeclouds-nft-order');
        $class = ($current == 'wc-completed' ? ' class="current"' :'');
        $views['completed'] = "<a href='{$completed_url}' {$class} >" . __('Completed','codeclouds-nft') . "</a> (" . $completed_nft_order_query->found_posts . ")";

        return $views;
    }

    function extra_tablenav( $which ) {
        if ( $which == "top" ){
            $user_id = ( ! empty($_GET['user_id'] ) ) ? absint($_GET['user_id']) : 0;
            ?>
            <div class="alignleft actions" id="cc-nft-user-filter-section">
                <select name="ccNftUserId" id="cc-nft-user" class="cc-nft-user-select">
                    <?php if($user_id > 0) {
                        $user_info = get_userdata($user_id);
                        $user_text = $user_info->first_name .' '.$user_info->last_name . '(#'.$user_id.' - '. $user_info->user_email .')';
                    ?>
                    <option value="<?php echo $user_id;?>"><?php echo $user_text;?></option>
                    <?php } ?>
                </select>
                <input type="submit" name="ccNftFilterAction" id="nft-filter-query-submit" class="button" value="Filter">
            </div>
            <?php
        }
    }

    public function no_items() {
        _e( 'No orders found.', 'codeclouds-nft' );
    }

    function prepare_items() {
        $nft_order_data = [];
        $user_meta_query = [];
        $post_type = ( ! empty( $_GET['post_type'] ) ) ? sanitize_text_field($_GET['post_type']) : '';
        $orderby = ( ! empty( $_GET['orderby'] ) ) ? sanitize_text_field($_GET['orderby']) : 'the_date';
        $order = ( ! empty($_GET['order'] ) ) ? sanitize_text_field($_GET['order']) : 'DESC';
        $user_id = ( ! empty($_GET['user_id'] ) ) ? absint($_GET['user_id']) : 0;
        $orderby = match ($orderby) {
            'order_date' => 'the_date',
            default => 'the_date',
        };
        $search = ( ! empty($_GET['s'] ) ) ? sanitize_text_field($_GET['s']) : '';
        $per_page = $this->get_items_per_page('orders_per_page', 5);;
        $current_page = $this->get_pagenum();
        if ($user_id > 0) {
            $user_meta_query = array(
                'key' => '_customer_user',
                'value' => $user_id
            );
        }

        if(!empty($search)) {
            $nft_order_meta_query = array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => '_billing_email',
                        'value' => $search,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => CODECLOUDS_NFT_META_KEY . '_title',
                        'value' => $search,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => CODECLOUDS_NFT_META_KEY . '_domain',
                        'value' => $search,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => CODECLOUDS_NFT_META_KEY . '_amount',
                        'value' => $search,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => CODECLOUDS_NFT_META_KEY . '_account_address',
                        'value' => $search,
                        'compare' => 'LIKE'
                    ),
                    array(
                        'key' => '_order_total',
                        'value' => $search,
                        'compare' => 'LIKE'
                    ),
                ),
                array(
                    'key' => CODECLOUDS_NFT_COUPON_KEY,
                    'value' => 1
                ),
                $user_meta_query
            );
        }else {
            $nft_order_meta_query = array(
                'relation' => 'AND',
                array(
                    'key' => CODECLOUDS_NFT_COUPON_KEY,
                    'value' => 1
                ),
                $user_meta_query
            );
        }

        $nft_order_query_args = array(
            'post_type'         => 'shop_order',
            'posts_per_page'    => $per_page,
            'paged'             => $current_page,
            'post_status'       => !empty($post_type) ? $post_type : array('wc-pending', 'wc-processing', 'wc-completed', 'wc-on-hold'),
            'orderby'           => $orderby,
            'order'             => $order,
            'meta_query'        => $nft_order_meta_query
        );
        $nft_order_query = new WP_Query( $nft_order_query_args );
        $nft_orders = $nft_order_query->get_posts();

        foreach ($nft_orders as $key => $nft_order) {
            $order = wc_get_order( $nft_order->ID );
            $customer_email = $order->get_billing_email();
            $coupon_title = $order->get_meta(CODECLOUDS_NFT_META_KEY . '_title');
            $coupon_domain = $order->get_meta(CODECLOUDS_NFT_META_KEY . '_domain');
            $coupon_amount = $order->get_meta(CODECLOUDS_NFT_META_KEY . '_amount');
            $account_address = $order->get_meta(CODECLOUDS_NFT_META_KEY . '_account_address');
            $created_date = $order->get_date_created();
            $nft_order_data[$key] = array(
                'user_id' => $order->get_user_id(),
                'customer_email' => $customer_email,
                'order_status' => $order->get_status(),
                'order_date' => $created_date->date('Y-m-d H:i:s'),
                'order_total' => $order->get_total(),
                'order_currency' => $order->get_currency(),
                'order_url' => $order->get_edit_order_url(),
                'order_number' => $order->get_order_number(),
                'coupon_title' => $coupon_title,
                'coupon_domain' => $coupon_domain,
                'coupon_amount' => $coupon_amount,
                'account_address' => $account_address,
            );
        }

        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);
        $total_items = $nft_order_query->found_posts;
        $this->set_pagination_args( array(
                'total_items' => $total_items,
                'per_page' => $per_page,
            )
        );
        $this->items = $nft_order_data;
    }
}
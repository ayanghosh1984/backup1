<?php
$ccNftOrderList = new Codeclouds_Nft_Order_List();
echo '<div class="wrap"><h2>';
echo __( "Codeclouds NFT Orders", "codeclouds-nft" );
echo '</h2>';
$ccNftOrderList->prepare_items();
echo '<form method="GET" name="nft_search_order" action="' . $_SERVER['REQUEST_URI'] . '">
<input type="hidden" name="page" value="codeclouds-nft-order">';
$ccNftOrderList->search_box('Search Order(s)', 'search_nft_order_id');
echo '</form>';
$ccNftOrderList->views();
$ccNftOrderList->display();
echo '</div>';

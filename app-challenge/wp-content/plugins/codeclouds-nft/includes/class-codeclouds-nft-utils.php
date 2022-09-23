<?php
/**
 * The utility plugin class.
 *
 * This is used to define utilities that has been
 * used throughout the sites.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Codeclouds_Nft
 * @subpackage Codeclouds_Nft/includes
 * @author     Codeclouds Team <team@codeclouds.biz>
 */

class Codeclouds_Nft_Utils
{
    public static function is_coupon_valid($token_meta_data, $cartTotal) : bool {
        $min_cart_total = isset($token_meta_data['minCartTotal']) ? $token_meta_data['minCartTotal'] : 0;
        $can_use_coupon = isset($token_meta_data['canUse']) ? $token_meta_data['canUse'] : 1;
        $coupon_domain = isset($token_meta_data['domain']) ? $token_meta_data['domain'] : '';
        $cc_nft_wc_data =  new WC_CC_NFT_Integration();
        $cc_nft_allowed_domains = !empty($cc_nft_wc_data->cc_nft_allowed_domains) ? explode(',', $cc_nft_wc_data->cc_nft_allowed_domains) : [];
        if($cartTotal >= $min_cart_total && $can_use_coupon == 1 && (count($cc_nft_allowed_domains) == 0 || empty($coupon_domain) || in_array($coupon_domain, $cc_nft_allowed_domains))) {
            return true;
        }

        return false;
    }

    public static function timeElapsedString($datetime, $full = false): string{
        $now = new DateTime(current_datetime()->format('Y-m-d H:i:s'));
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' ago' : 'just now';
    }
}
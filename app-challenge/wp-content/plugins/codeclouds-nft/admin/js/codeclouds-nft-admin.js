(function( $ ) {
	'use strict';
	const ccNftUserEl = document.getElementById('cc-nft-user');
	const ccNftFilterQuerySubmit = document.getElementById('nft-filter-query-submit');
	jQuery(ccNftUserEl).select2({
		ajax: {
			url: nftAdminJsData.ajax_url,
			delay: 500,
			data: function (params) {
				const query = {
					nonce: nftAdminJsData._nonce,
					action: 'CCNftGetUsers',
					search: params.term,
					page: params.page || 1
				};

				return query;
			},
			processResults: function (data, params) {
				return {
					results: data.items,
					pagination: {
						more: (params.page * 3) < parseInt(data.total_count)
					}
				};
			},
			cache: true
		},
		placeholder: 'Filter by user',
		minimumInputLength: 1,
		dropdownAutoWidth : true
	});

	jQuery(ccNftFilterQuerySubmit).on('click', (e) => {
		e.preventDefault();
		let userId = parseInt(jQuery(ccNftUserEl).val());
		if(userId > 0) {
			window.location.href = window.location.href + '&user_id=' + userId;
		}
	})
})( jQuery );

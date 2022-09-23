(function( $ ) {
	'use strict';
	toastr.options = {
		"closeButton": false,
		"debug": false,
		"newestOnTop": false,
		"progressBar": false,
		"positionClass": "toast-bottom-right",
		"preventDuplicates": true,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "5000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	};
	window.userWalletAddress = null;
	nftJsData.enableDiscount = parseInt(nftJsData.enableDiscount);
	const ccNftToggleClass = 'cc-nft-coupon-toggle';
	const ccNftFormShowClass = 'cc-nft-ooupon-form-show';
	const ccNftApplyClass = 'cc-nft-coupon-apply';
	const ccNftRemoveClass = 'cc-nft-coupon-remove';
	const ccNftListSingleClass = 'cc-nft-coupon-list-single';
	const ccNftLoginEl = '#cc-nft-coupon-login';
	const ccNftListEl = '#cc-nft-coupon-list';
	const ccNftSectionEl = '#cc-nft-section';
	const ccNftFormEl = '#cc-nft-coupon-form';
	const ccNftAppliedEl = '#cc-nft-coupon-applied';
	const ccNftRemoveEl = '#' + ccNftRemoveClass;
	const ccNftFormSectionEl = '#cc-nft-coupon-form-section';
	const ccNftAppliedTxtEl = '#cc-nft-coupon-applied-text';
	const ccNftCouponSectionEl = '#cc-nft-coupon-section';
	const waitMeConfig = {
		effect : 'win8',
		text : '',
		bg : 'rgba(255,255,255,0.7)',
		color : '#000',
		maxSize : '',
		waitTime : -1,
		textPos : 'vertical',
		fontSize : '',
		source : ''
	};
	const ethObj = window.ethereum;
	const targetNetworkId = '0x539';
	const web3 = new Web3(ethObj);
	const setNftCouponSection = () => {
		if(nftJsData.enableDiscount) {
			let ccNftCouponText = nftJsData.tokenMetaData.length > 0 && typeof nftJsData.tokenMetaData.title !== 'undefined' && nftJsData.tokenMetaData.title != '' ? `Codeclouds NFT coupon <b>${nftJsData.tokenMetaData.title}</b> applied successfully!` : `Codeclouds NFT coupon applied successfully!`;
			jQuery(ccNftAppliedTxtEl).html(ccNftCouponText);
			jQuery(ccNftFormEl).hide();
			jQuery(ccNftCouponSectionEl).hide();
			jQuery(ccNftAppliedEl).show();
			if (jQuery(ccNftFormSectionEl).hasClass(ccNftToggleClass)) {
				jQuery(ccNftFormSectionEl).removeClass(ccNftToggleClass);
			}
		}else {
			jQuery(ccNftCouponSectionEl).show();
			jQuery(ccNftAppliedEl).hide();
			if (!jQuery(ccNftFormSectionEl).hasClass(ccNftToggleClass)) {
				jQuery(ccNftFormSectionEl).addClass(ccNftToggleClass);
			}
		}
	}
	const installMetamask = () => {
		jQuery(ccNftLoginEl).text('Click to install Metamask')
		jQuery(ccNftLoginEl).removeClass('bg-purple-500 text-white').addClass('bg-gray-500 text-gray-100 cursor-not-allowed')
		jQuery(document).on('click', ccNftLoginEl, () => {
			window.open("https://metamask.io/", "_blank");
		})
		return false;
	};
	const toggleButton = () => {
		if(jQuery(ccNftLoginEl).length > 0) {
			if (!ethObj) {
				installMetamask()
			}
			jQuery(document).on('click', ccNftLoginEl, () => {
				jQuery('body').waitMe(waitMeConfig);
				loginWithMetaMask().then(r => {
					jQuery('body').waitMe('hide')
				} );
			})
		}
	};
	const loginWithMetaMask = async () => {
		if (ethObj) {
			const currentChainId = await ethObj.request({method: 'eth_chainId'});
			// return true if network id is the same
			if (currentChainId == targetNetworkId) {
				const accounts = await ethObj.request({ method: 'eth_requestAccounts' })
					.catch((e) => {
						toastr["error"](e.message);
						return;
					})
				if (!accounts) {
					toastr["error"]('Sorry! You do not have any accounts');
					return;
				}

				window.userWalletAddress = accounts[0];

				await getAccountBalance(window.userWalletAddress);
				if(nftJsData.current_user_id == 0 ) {
					await createUser(window.userWalletAddress);
					return;
				}
				if(jQuery(`.${ccNftListSingleClass}`).length > 0) {
					jQuery(ccNftFormSectionEl).toggleClass(ccNftToggleClass);
				}else {
					jQuery(ccNftAppliedEl).hide();
					toastr["error"]('Sorry !! there is no coupon to apply')
					return;
				}
			} else {
				jQuery(ccNftFormEl).hide();
				jQuery(ccNftAppliedEl).hide();
				toastr["error"]('Choose correct newwork to continue')
				return;
			}
		}else {
			installMetamask()
		}
	};
	const getAccountBalance = async (accountAddress) => {
		const contract = new web3.eth.Contract(nftJsData.contractData.abi ? JSON.parse(nftJsData.contractData.abi) : JSON.parse(nftJsData.contractData), nftJsData.contractAddress)
		contract.defaultAccount = accountAddress;
		const tokenBalance = await contract.methods.balanceOf(accountAddress).call();

		let couponList = '';
		let cartSubTotal = parseFloat(nftJsData.subTotal);
		for (let i = 0; i < tokenBalance; i++) {
			const tokenId = await contract.methods.tokenOfOwnerByIndex(accountAddress, i).call();
			let tokenMetadata = await contract.methods.get(tokenId).call();
			let minCartTotal = parseFloat(tokenMetadata['minCartTotal']);
			if( nftJsData.allowedDomains.length === 0 || nftJsData.allowedDomains.includes(tokenMetadata['domain']) ) {
				tokenMetadata['tokenId'] = tokenId;
				couponList += `<div class="${ccNftListSingleClass} ${cartSubTotal < minCartTotal ? 'cc-nft-coupon-list-fade' : ''}"> 
								<input id="coupon${i+1}" type="radio" name="ccNftCoupon" class="cc-nft-coupon-select" value="${tokenMetadata['amount']}" data-token-meta='${JSON.stringify(tokenMetadata)}' ${cartSubTotal < minCartTotal ? 'disabled' : ''} /> 
								<label for="coupon${i+1}">
									<b>${tokenMetadata['title']}</b> - Discount Amount :${tokenMetadata['amount']}
								</label>
							</div>`;
			}
			//console.log(tokenMetadata)
		}
		if (couponList.length > 0 && tokenBalance > 0 && nftJsData.current_user_id > 0) {
			jQuery(ccNftFormEl).show();
			jQuery(ccNftListEl).html(couponList);

		}
	};
	const createUser = async (accountAddress) => {
		const data = {
			action: 'CCNftCreateUser',
			nonce: nftJsData._nonce,
			account_address: accountAddress
		};
		jQuery.ajax({
			method: 'POST',
			url: nftJsData.ajax_url,
			data: data,
			success: function(response){
				if(response.success) {
					location.reload();
				}else {
					toastr['error'](response.data.message);
				}
			},
			error: function (request, status, error) {
				toastr["error"](request.responseText)
			}
		});
	};
	const setCoupon = (enable_discount, token_meta_data = '') => {
		const data = enable_discount ?
			{
				action: 'CCNftCouponActions',
				enable_discount,
				token_meta_data,
				nonce: nftJsData._nonce,
			}
			:
			{
				action: 'CCNftCouponActions',
				enable_discount,
				nonce: nftJsData._nonce,
			}
		jQuery.ajax( {
			type: 'POST',
			url: nftJsData.ajax_url,
			data,
			beforeSend: function() {
				jQuery('body').waitMe(waitMeConfig);
			},
			success: function (res) {
				jQuery('body').waitMe('hide');
				if (res.success) {
					nftJsData.enableDiscount = enable_discount;
					nftJsData.tokenMetaData = enable_discount ? token_meta_data : [];
					setNftCouponSection()
					jQuery('body').trigger('update_checkout');
					toastr['success'](res.data.message);
				}else {
					toastr['error'](res.data.message);
				}
			},
			error: function (request, status, error) {
				toastr["error"](request.responseText)
			}
		});
	}

	/* metamask handler */
	if (ethObj) {
		ethObj.on('accountsChanged', function (accounts) {
			// Time to reload your interface with accounts[0]!
			window.userWalletAddress = accounts[0];
			if (!jQuery(ccNftFormSectionEl).hasClass(ccNftToggleClass)) {
				jQuery(ccNftFormSectionEl).addClass(ccNftToggleClass);
			}
			jQuery(ccNftListEl).html('');
			if(nftJsData.enableDiscount) {
				jQuery(ccNftRemoveEl).trigger('click');
			}
		})
		// detect Network account change
		ethObj.on('networkChanged', function(networkId){
			if (!jQuery(ccNftFormSectionEl).hasClass(ccNftToggleClass)) {
				jQuery(ccNftFormSectionEl).addClass(ccNftToggleClass);
			}
			jQuery(ccNftListEl).html('');
			if(nftJsData.enableDiscount) {
				jQuery(ccNftRemoveEl).trigger('click');
			}
			if(networkId !== '5777') {
				jQuery(ccNftFormEl).hide();
				jQuery(ccNftAppliedEl).hide();
				toastr["error"]('Choose correct newwork to continue')
				return false;
			}
		});

	}

	/* load cc-nft coupon field */
	window.addEventListener('DOMContentLoaded', () => {
		toggleButton()
		if (!ethObj && jQuery(ccNftSectionEl).length > 0) {
			jQuery(ccNftSectionEl).hide();
		}else {
			setNftCouponSection();
		}
	});

	jQuery('form.checkout').on('click', `.${ccNftFormShowClass}`, function(){
		if(!nftJsData.enableDiscount) {
			jQuery('body').waitMe(waitMeConfig);
			loginWithMetaMask().then(r => {
				jQuery('body').waitMe('hide')
			} );
		}else {
			jQuery(ccNftFormSectionEl).toggleClass(ccNftToggleClass);
		}
	});

	/* apply cc-nft coupon */
	jQuery('form.checkout').on('click', `#${ccNftApplyClass}`, function(){
		if(jQuery("input[name='ccNftCoupon']").length === 0) {
			toastr['error']('Sorry !! there is no coupon to apply');
			return;
		}

		if(!jQuery("input[name='ccNftCoupon']").is(':checked')) {
			toastr['error']('Please select one NFT Coupon to continue');
			return;
		}

		jQuery(ccNftAppliedEl).hide();
		let tokenMetaData = jQuery("input[name='ccNftCoupon']:checked").attr('data-token-meta');
		if(typeof tokenMetaData === 'undefined') {
			toastr['error']('Please select one NFT Coupon to continue');
			return;
		}

		tokenMetaData = JSON.parse(tokenMetaData)
		tokenMetaData['accountAddress'] = window.userWalletAddress;
		if(typeof tokenMetaData['amount'] !== 'undefined' && tokenMetaData['amount'] > 0) {
			setCoupon(1, tokenMetaData);
		} else {
			toastr['error']('Please select one NFT Coupon to continue');
		}
	});

	/* remove cc-nft coupon */
	jQuery('form.checkout').on('click', `#${ccNftRemoveClass}`, function(){
		setCoupon(0)
	});
})( jQuery );

window.userWalletAddress = null
const loginButton = document.getElementById('loginButton')
const userWallet = document.getElementById('userWallet')
const userWalletBalance = document.getElementById('userWalletBalance')
const couponListSection = document.getElementById('couponListSection')
const ccNFTSection = document.getElementById('cc-nft-section')
const applyButton = document.getElementById('apply_cc_coupon');
const targetNetworkId = '0x539';

const web3 = new Web3(window.ethereum)
if (window.ethereum) {
    window.ethereum.on('accountsChanged', function (accounts) {
        // Time to reload your interface with accounts[0]!
        window.userWalletAddress = accounts[0];
        document.getElementById('removeccNft').click();
        console.log(accounts);
    })
    // detect Network account change
    window.ethereum.on('networkChanged', function(networkId){
        console.log('networkChanged',networkId);
        document.getElementById('removeccNft').click();
        if(networkId !== 5777) {
            document.getElementById('cc-coupon-form').style.display = 'none';
            document.getElementById('cc_nft_coupon_success').style.display = 'none';
            document.getElementById('emptyCouponListSection').style.display = 'initial';
            applyButton.style.display = 'none';
            document.getElementById('emptyCouponListSection').innerHTML = '<div>Sorry !! choose correct newwork to continue</div>';
            console.log('Choose correct newwork to continue');
            return false;
        }
    });
    
}
function toggleButton() {
    if(loginButton) {
        if (!window.ethereum) {
            // loginButton.innerText = 'MetaMask is not installed'
            loginButton.innerText = 'Click to install Metamask'
            loginButton.classList.remove('bg-purple-500', 'text-white')
            loginButton.classList.add('bg-gray-500', 'text-gray-100', 'cursor-not-allowed')
            loginButton.addEventListener('click', installMetamask)
            return false
        }
    
        loginButton.addEventListener('click', loginWithMetaMask)
    }
}

function installMetamask() {
    window.open(
    "https://metamask.io/", "_blank");
}

async function loginWithMetaMask() {
    if (window.ethereum) {
        const currentChainId = await window.ethereum.request({method: 'eth_chainId'});

        // return true if network id is the same
        if (currentChainId == targetNetworkId) {

            const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' })
                .catch((e) => {
                console.error(e.message)
                return
                })
            if (!accounts) { return }

            window.userWalletAddress = accounts[0];

            getAccountBalance(window.userWalletAddress);

            createUser(window.userWalletAddress);
            
            if(loginButton) {
                loginButton.innerText = 'Sign out of MetaMask'

                loginButton.removeEventListener('click', loginWithMetaMask)
                setTimeout(() => {
                    loginButton.addEventListener('click', signOutOfMetaMask)
                }, 200)
            }
        }
        else {
            document.getElementById('cc-coupon-form').style.display = 'none';
            document.getElementById('cc_nft_coupon_success').style.display = 'none';
            document.getElementById('emptyCouponListSection').style.display = 'initial';
            applyButton.style.display = 'none';
            document.getElementById('emptyCouponListSection').innerHTML = '<div>Sorry !! choose correct newwork to continue</div>';
            console.log('Choose correct newwork to continue');
            return;
        }
    }
}

async function signOutOfMetaMask() {
    window.userWalletAddress = null
    userWallet.innerText = ''
    userWalletBalance.innerText = ''
    couponListSection.innerHTML = ''
    loginButton.innerText = 'Sign in with MetaMask'

    loginButton.removeEventListener('click', signOutOfMetaMask)
    setTimeout(() => {
        loginButton.addEventListener('click', loginWithMetaMask)
    }, 200)
}

async function getAccountBalance(accountAddress) {
    const contract = new web3.eth.Contract(nftJsData.contractData.abi, nftJsData.contractAddress)
    
    console.log(contract);
    contract.defaultAccount = accountAddress;

    const tokenBalance = await contract.methods.balanceOf(accountAddress).call();
    console.log(tokenBalance)
    let couponList = '';
    let cartSubTotal = parseInt(cartDetails.subTotal);
    for (let i = 0; i < tokenBalance; i++) {
        const tokenId = await contract.methods.tokenOfOwnerByIndex(accountAddress, i).call();

        // let tokenMetadataURI = await contract.methods.tokenURI(tokenId).call();
        // const tokenMetadata = await fetch(tokenMetadataURI).then(response => response.json());
        let tokenMetadata = await contract.methods.get(tokenId).call();

        if(cartSubTotal >= parseInt(tokenMetadata['minCartTotal'])) {
            couponList += '<div class="cc-nft-coupon-list"> <input id="'+ tokenMetadata['title'] + '" type="radio" name="ccNFTRadio" class="ccNFTRadio" value="'+ tokenMetadata['amount'] +'" /> <label for="'+ tokenMetadata['title'] + '"><b>'+ tokenMetadata['title'] + '</b> - Discount Amount :' + tokenMetadata['amount'] +'</label></div>';
        }
        else {
            couponList += '<div class="cc-nft-coupon-list" style="opacity: 0.5;"> <input id="'+ tokenMetadata['title'] + '" type="radio" class="ccNFTRadio" disabled /> <label for="'+ tokenMetadata['title'] + '"><b>'+ tokenMetadata['title'] + '</b> - Discount Amount :' + tokenMetadata['amount'] +'</label></div>';
        }

        console.log(tokenMetadata)
        
    }
    if(tokenBalance > 0) {
        document.getElementById('emptyCouponListSection').style.display = 'none';
        applyButton.style.display = 'initial';
        document.getElementById('cc-coupon-form').style.display = 'initial';
        couponListSection.innerHTML = couponList;
    }
    else {
        document.getElementById('cc-coupon-form').style.display = 'none';
        document.getElementById('cc_nft_coupon_success').style.display = 'none';
        document.getElementById('emptyCouponListSection').style.display = 'initial';
        applyButton.style.display = 'none';
        document.getElementById('emptyCouponListSection').innerHTML = '<div>Sorry !! there is no coupon to apply</div>';
    }
}

// create wp user
async function createUser(accountAddress) {
    var data = {
        action: 'create_user',
        account_address: accountAddress,
        wallet_login_success: true
    };
    jQuery.ajax({
        method: 'POST',
        contentType: 'application/x-www-form-urlencoded; charset=utf-8',
        url: ajaxObject.ajaxurl,
        data: data,
        success: function(response){
            console.log("Success Response: ", response);
        },
        error: function (request, status, error) {
            console.log("Error: ", request.responseText);
        }
    });
}

window.addEventListener('DOMContentLoaded', () => {
    toggleButton()
    if (!window.ethereum && ccNFTSection) {
        ccNFTSection.style.display = 'none';
    }
});
(function () {
    var ret = false;
    getPage = function () {
        var domain = location.hostname;
        if ((/\.?tmall\..*/.test(domain))||(/\.?95095\..*/.test(domain))) {
            return getTmallHkGoodsDetail();
        } else if (/\.?jd\..*/.test(domain)) {
            return getJDGoodsDetail(domain)
        } else if (/\.?amazon\..*/.test(domain)) {
            return getAmazonCNGoodsDetail(domain)
        } else if (/\.?ymatou\..*/.test(domain)) {
            return getYmaTouGoodsDetail();
        } else if (/\.?koudaitong\..*/.test(domain)) {
            return getKouDaiTongDetail();
        } else if (/\.?ebay\..*/.test(domain)) {
            return getEbayDetail();
        } else if (/\.?sasa\..*/.test(domain)) {
            return getSaSaDetail();
        } else if (/\.?bonjour.*/.test(domain)) {
            return getBonjourDetail();
        } else if (/\.?colourmix-.*/.test(domain)) {
            return getColourmixDetail();
        } else if (/\.?watsons\..*/.test(domain)) {
            return getWatsonsDetail();
        } else if (/\.?cosme\..*/.test(domain)) {
            return getCosmeDetail();
        } else if (/\.?gmarket\..*/.test(domain)) {
            return getGMarketDetail();
        } else if (/\.?lotteimall\..*/.test(domain)) {
            return getLotteiMallDetail();
        } else if (/\.?thebodyshop\..*/.test(domain)) {
            return getTheBodyShopDetail();
        }
    }
    $(function () {
        getPage();
        return false;
    });

    chrome.extension.onMessage.addListener(function (request) {
        window.close();
        return false;
    });
})();

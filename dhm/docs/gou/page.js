(function ()
{
    var profile = [],test=10;
    alert("插件正在运行");
    chrome.runtime.sendMessage({ method: "getProfile" ,number:1}, function (response)
    {//发起请求，获取基本配置
        console.log(response);
        if (response.data != undefined && response.data != "")
        {
            alert("插件收到消息");
        }
    });
})();

// Bind event:
chrome.runtime.onMessage.addListener(function(message, sender, sendResponse) {
    // Do something
});

// Send message:
chrome.runtime.sendMessage({greeting: 'hello'});
(function () {
    var data = {"type": "","to": "", data: null};
    var tabs = [];
//chrome.browserAction.onClicked.addListener(updateIcon);//添加监听事件
    chrome.runtime.onMessage.addListener(
        function (request, sender, sendResponse) {
            console.log(request);
            switch (request.type) {
                case "page"://淘宝好店
                    tabs[request.tab] = sender.tab.id;
                    break;
                case "finish"://接收数据完成
                    console.log("Finish!");
                    break;
                case "send"://发送海淘商品信息
                    data=request;
                    chrome.tabs.sendMessage(sender.tab.id, {"rev":"ok"});
                    break;
                default :
                    console.log(request.data)
                    break;
            }

            if (data.to != "" && tabs[data.to] != undefined) {
                chrome.tabs.sendMessage(tabs[data.to], data);
                chrome.tabs.update(tabs[data.to],{active:true})
                data = {"type": "","to": "", data: null};
            }
        }
    );

    chrome.browserAction.onClicked.addListener(function (tab) {
        chrome.tabs.executeScript(tab.id, {file: 'jquery-1.11.2.min.js'});
        chrome.tabs.executeScript(tab.id, {file: 'func.js'});
        chrome.tabs.executeScript(tab.id, {file: 'grab.js'});
    });
})();



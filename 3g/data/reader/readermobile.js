/**
    @fileOverview
    针对手机网站的阅读模式
 */




(function (window) {
    var page;
    var code;

    window.reader = {
        readerLaunch: readerLaunch,
        getTitle: getTitle,
        getContent: getContent,
        getNextUrl: getNextUrl
    };

    function getTitle() {
        return page.getArticleTitle();
    }

    function getContent() {
        return page.getArticleContent();
    }

    function getNextUrl() {
        return page.getNextPageUrl();
    }

    function readerLaunch(doc) {
	//window.readercheck.notifyInfo('stepped into maxthonReaderLaunch');
        code = isWebUrlList(window.location.host);
	//window.readercheck.notifyInfo('maxthonReaderLaunch code = ' + code);
	//alert(code);
        if (code > -1) {
            page = new PageConstructor(doc);
        }
        if (page && page.isRead()) {
            return true;
        }
        return false;
    }
    
    /**
        @description 页面构造分析
        @param document
        @return true代表可以启动阅读模式
     */
    function PageConstructor(doc) {
        this.articleTitle;
        this.articleContent;
        this.nextPageUrl;

        this.isRead = function () {
            this.getArticleTitle();
            this.getArticleContent();
            if (this.articleTitle && this.articleContent) {
                return true;
            }
            return false;
        };
        
        //返回文字title
        this.getArticleTitle = function () {
            var htmlTitle;
            if (this.articleTitle) {
                return this.articleTitle;
            }
            switch (code) {
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 begin
                //case 2: //新浪
                    //htmlTitle = doc.querySelector('.articleTitle').innerHTML;
                    //break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 end
                case 11: //红袖
                case 42://红袖子网站
                    //Gionee <weidong><2013-7-28> modify for CR00843589 begin
                    htmlTitle = doc.querySelectorAll('#scr a')[1].innerHTML;
                    //Gionee <weidong><2013-7-28> modify for CR00843589 end
                    break;
                case 20: //Business Insider
                    htmlTitle = doc.querySelector('.instapaper_title').innerHTML;
                    break;
                case 21: //Washington Post
                    htmlTitle = doc.querySelector('.intro > .headline').innerHTML;
                    break;
                case 23: //CNN The Marquee Blog
                    htmlTitle = doc.querySelector('.cnnm-title > a').innerHTML;
                    break;
                case 24: //Fox News
                    htmlTitle = doc.querySelector('.element > b').innerHTML;
                    break;
                case 25: //Huffington Post
                    htmlTitle = doc.querySelector('.entryTitle').innerHTML;
                    break;
                case 26: //BBC
                    htmlTitle = doc.querySelector('.story-body h1').innerHTML;
                    break;
                case 30: //Engadget
                    htmlTitle = doc.querySelector('.paddingTop h2').innerHTML;
                    break;
                case 32: //Mac Rumors
                    htmlTitle = doc.querySelector('.teaser_title').innerHTML;
                    break;
                //Gionee <weidong><2013-7-28> modify for CR00843589 begin
                case 37://宜搜小说
                    htmlTitle = doc.querySelector('title_tag').innerHTML;
                    break;
                /*
                case 39://书旗小说网
                    htmlTitle = doc.querySelector('#readTitle').innerHTML;
                    break;
                */
                //Gionee <weidong><2013-7-28> modify for CR00843589 end
                default :
                    htmlTitle = doc.querySelector('title').innerHTML;
            }
            this.articleTitle = htmlTitle ? htmlTitle : '';
            //alert(this.articleTitle);
        }

        //返回内容
        this.getArticleContent = function() {
            var htmlObj;
            var htmlStr;
            if (this.articleContent) {
                return this.articleContent;
            }
            switch (code) {
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 begin
                //case 0: //网易
                    //htmlObj = doc.querySelector('.text > .text1');
                    //htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    //break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 end
                case 1: //新浪读书
                    htmlObj = doc.querySelector('#zwbox > .blue_box');
                    if (!htmlObj) {
                        htmlObj = doc.querySelector('.wrap > *:nth-child(9)');
                    }
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 begin
                //case 2: //新浪
                    //htmlObj = doc.querySelector('.articleContent');
                    //htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    //break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 end
                case 3: //腾讯读书
                    htmlObj = doc.querySelector('.mqq-content > .mqq-p');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 begin
                //case 4: //腾讯
                    //htmlObj = doc.querySelector('#articlecontent');
                    //htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    //break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 end
                case 5: //凤凰读书
                    htmlObj = doc.querySelector('.listM > .content');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = /马上阅读/.test(htmlStr) ? '' : matchSectionToNul(htmlStr, '<form', 1);
                    htmlStr = matchTagToNull(htmlStr, 'div', 1);
                    htmlStr = matchTagToNull(htmlStr, 'a' ,0);
                    break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 begin
                //case 6: //凤凰
                    //htmlObj = doc.querySelector('#articlecontent');
                    //htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    //break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 end
                case 7: //3G门户
                    htmlObj = doc.querySelector('.content_text');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 8: //3G门户书城
                    htmlObj = doc.querySelector('#body0');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = matchSectionToNul(htmlStr, '<input', 1);
                    htmlStr = matchTagToNull(htmlStr, 'a' ,0);
                    break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 begin
                //case 9: //搜狐(旧版)
                    //htmlObj = doc.querySelectorAll('#newsimg');
                    //htmlStr = objToStr(htmlObj);
                    //htmlObj = doc.querySelector('#newscontet');
                    //htmlStr += htmlObj ? htmlObj.innerHTML : '';
                    //break;
                //case 10: //搜狐(新版)
                    //htmlObj = doc.querySelector('.finCnt .cnt');
                    //htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    //htmlStr = matchTagToNull(htmlStr, 'a' ,0);
                    //break;
                //Gionee <huxw><2013-3-8> modify for CRCR00770067 begin
                case 11: //红袖
                case 42://红袖子网站
                    htmlObj = doc.querySelector('#htmlContent');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = matchStrToNull(htmlStr, '&nbsp;');
                    break;
                case 12: //人民网
                    htmlObj = doc.querySelectorAll('font[class="c7"]');
                    htmlStr = objToStr(htmlObj);
                    break;
                case 13: //新华网
                    htmlObj = doc.querySelector('.content');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = matchTagToNull(htmlStr, 'a', 0);
                    break;
                case 14: //铁血网
                    htmlObj = doc.querySelector('.text_content');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 15: //环球网
                    htmlObj = doc.querySelector('#content');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 16: //起点
                    htmlObj = doc.querySelectorAll('.articleDes > h2');
                    htmlStr = objToStr(htmlObj);
                    htmlStr = /阅读设置/.test(htmlStr) ? matchSectionToNul(htmlStr, '阅读设置', 1) : '';
                    htmlStr = matchTagToNull(htmlStr, 'h1' ,0);
                    htmlStr = matchTagToNull(htmlStr, 'a' ,0);
                    break;
                case 17: //塔读网
                    htmlObj = doc.querySelector('.my_style');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = matchTagToNull(htmlStr, 'span' ,0);
                    break;
                case 18: //晋江原创网
                    htmlObj = doc.querySelector('ul');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 19: //Yahoo News
                    htmlObj = doc.querySelector('#content > div > .uic.center');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlObj = doc.querySelector('#content > div > .uic.description');
                    htmlStr += htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 20: //Business Insider
                    htmlObj = doc.querySelector('.instapaper_body');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = matchSectionToNul(htmlStr, '<div\\sclass="slide-controls', 1);
                    break;
                case 21: //Washington Post
                    htmlObj = doc.querySelector('.body');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 22: //CNN
                    htmlObj = doc.querySelector('.articleContent');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = matchTagToNull(htmlStr, 'span', 0, 'class="bold_text"');
                    htmlStr = matchTagToNull(htmlStr, 'div', 0);
                    htmlStr = matchTagToNull(htmlStr, 'script', 0);
                    break;
                case 23: //CNN The Marquee Blog
                    htmlObj = doc.querySelector('.cnnm-wideimg');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlObj = doc.querySelector('.cnnm-post-body');
                    htmlStr += htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 24: //Fox News
                    htmlObj = doc.querySelectorAll('div[class="element"] > p');
                    htmlStr = objToStr(htmlObj);
                    break;
                case 25: //Huffington Post
                    htmlObj = doc.querySelector('.entryVideo');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlObj = doc.querySelector('.entryImage');
                    htmlStr += htmlObj ? htmlObj.innerHTML : '';
                    htmlObj = doc.querySelector('.entryBody');
                    htmlStr += htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 26: //BBC
                    htmlObj = doc.querySelector('.story-body');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = matchTagToNull(htmlStr, 'div', 0, 'class="byline"');
                    htmlStr = matchTagToNull(htmlStr, 'h1', 0);
                    break;
                case 27: //BBC Sport
                    htmlObj = doc.querySelector('.storybody');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 28: //TC
                    htmlObj = doc.querySelector('.f_article > span');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 29: //GigaOM
                    htmlObj = doc.querySelector('.content > .post');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 30: //Engadget
                    htmlObj = doc.querySelector('.paddGen > span');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 31: //ESPN
                    htmlObj = doc.querySelector('#story-body');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 32: //Mac Rumors
                    htmlObj = doc.querySelector('.teaser_content');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 33: //NYT
                    htmlObj = doc.querySelector('.bodycontent');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                case 34: //新浪博客
                    htmlObj = doc.querySelector('.lS4');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                //Gionee <weidong><2013-7-28> modify for CR00843589 begin
                case 35: //移动阅读
                    htmlObj = doc.querySelector('.text');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = matchSectionToNul(htmlStr, '<a', 1);
                    if(htmlStr.length <= 5){
                        htmlStr = null;
                    }
                    break;
                case 36: //17K
                    htmlObj = doc.querySelectorAll('.content2');
                    htmlStr = objToStr(htmlObj);
                    htmlStr = matchSectionToNul(htmlStr, '<a', 1);
                    if(htmlStr.length <= 10){//为了过滤“下午好”
                        htmlStr = null;
                    }
                    break;
                case 37://宜搜小说
                    htmlStr = doc.querySelector('content_tag').innerHTML;
                    break;
                case 38://纵横小说
                    htmlObj = doc.querySelector('.yd');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    htmlStr = matchSectionToNul(htmlStr, '/span>', 0);
                    htmlStr = matchSectionToNul(htmlStr, '/a>', 0);
                    if(htmlStr.length <= 10){//为了过滤目录中的br
                        htmlStr = null;
                    }
                    break;
                case 39://看书网
                case 40://飞库小说
                    htmlStr = doc.querySelector('div[class="c21 dpad"]').innerHTML;
                    break;
                case 41://幻剑书盟小说
                    htmlStr = doc.querySelector('div[class="book_text pl_10"]').innerHTML;
                    break;
                /*
                case 39://书旗小说网
                    htmlObj = doc.querySelector('#readContent');
                    htmlStr = htmlObj ? htmlObj.innerHTML : '';
                    break;
                */
                //Gionee <weidong><2013-7-28> modify for CR00843589 end
            }
            this.articleContent = filterHtml(htmlStr);
            //alert(this.articleContent);
        }

        //获取下一页链接
        this.getNextPageUrl = function() {
            var urlHref;
	    //window.readercheck.notifyInfo('stepped into getNextPageUrl, code = ' + code);
            if (this.nextPageUrl) {
                return this.nextPageUrl;
            }
            switch (code) {
                case 2: //新浪
                    urlHref = findNextPageUrl(doc, '余下全文');
                    this.nextPageUrl = urlHref ? urlHref : findNextPageUrl(doc, '下一页|下页');
                    break;
                case 6: //凤凰
                    this.nextPageUrl = findNextPageUrl(doc, '下一页|下页');
                    break;
                case 13: //新华网
                    this.nextPageUrl = findNextPageUrl(doc, '^&gt;$');
                    break;
                case 14: //铁血网
                    this.nextPageUrl = null;
                    break;
                case 20: //Business Insider
                    this.nextPageUrl = findNextPageUrl(doc, '^Next\\s»');
                    break;
                case 21: //Washington Post
                    this.nextPageUrl = findNextPageUrl(doc, '^Next\\sPage');
                    break;
                case 22: //CNN
                    this.nextPageUrl = findNextPageUrlSpecial1();
                    break;
                case 24: //Fox News
                    this.nextPageUrl = findNextPageUrl(doc, '^Next\\sPage');
                    break;
                case 34: //新浪博客
                    urlHref = findNextPageUrl(doc, '余下全文');
                    this.nextPageUrl = urlHref ? urlHref : findNextPageUrl(doc, '下一页|下页');
                    break;
                //Gionee <weidong><2013-7-28> modify for CR00843589 begin
                case 35: //移动阅读
                    this.nextPageUrl = findNextPageUrl(doc, '下一页');
                    break;
                //Gionee <weidong><2013-7-28> modify for CR00843589 end
                default :
                    urlHref = findNextPageUrl(doc, '下一页|下页');
                    this.nextPageUrl = urlHref ? urlHref : findNextPageUrl(doc, '下一章|下章|下一节');
                }
            //alert(this.nextPageUrl);
            return this.nextPageUrl;
        }
    }

    //查找下一页链接
    function findNextPageUrl(doc, reg) {
        reg = new RegExp(reg, 'i');
		var regHref = new RegExp('^javascript');
		var pageTagA = doc.getElementsByTagName('a');
		var item;
		if(pageTagA && pageTagA.length>0) {
			for(var i=0; i<pageTagA.length; i++) {
				item = pageTagA[i];
				if( reg.test(item.innerHTML.toLowerCase()) && !regHref.test(item.href) ) {
					return item.href;
				}
			}
		}
	}

    //查找下一页链接针对CNN特殊处理
    function findNextPageUrlSpecial1() {
        var itemLink
        var allLink = document.querySelectorAll('.dotnav > a');
        for (var i = 0; i < allLink.length; i++) {
            itemLink = allLink[i];
            if (/on/i.test(itemLink.className) && (i + 1) < allLink.length) {
                return allLink[i + 1].href;
            }
        }
        return null;
    }

    // 判断webUrl是否是需要匹配兼容网站
    function isWebUrlList(url) {
        var reg;
        var urlList = [
            '3g\\.163\\.com',
            'book.*\\.sina\\.cn',
            '^(?!blog).*?\.sina\.cn',
            'ebook.*\\.3g\\.qq\\.com',
            '.+\\.3g\\.qq\\.com',
            'wap\\.book\\.ifeng\\.com',
            'i\\.ifeng\\.com',
            '.+\\.3g\\.cn',
            'read\\.3gycw\\.com',
            'wap\\.sohu\\.com',
            'm\\.sohu\\.com',
            '.+\\.hongxiu\\.com',
            'wap\\.people\\.com\\.cn',
            '(3g\\.xinhuanet\\.com|m\\.news\\.cn)',
            'm\\.tiexue\\.net',
            'm\\.huanqiu\\.com',
            'qidian\\.cn',
            'wap\\.tadu\\.com',
            '(wap\\.jjwxc\\.com|m\\.jjwxc\\.com)',
            'm\\.yahoo\\.com',
            'www\\.businessinsider\\.com',
            '(mobile\\.washingtonpost\\.com|m\\.washingtonpost\\.com)',
            'edition\\.cnn\\.com',
            'marquee\\.blogs\\.cnn\\.com',
            '.+\\.foxnews\\.mobi',
            'www\\.huffingtonpost\\.com',
            'm\\.bbc\\.co\\.uk',
            'www\\.bbc\\.co\\.uk',
            'm\\.techcrunch\\.com',
            'gigaom\\.com',
            'm\\.engadget\\.com',
            'm\\.espn\\.go\\.com',
            'www\\.macrumors\\.com',
            'mobile\\.nytimes\\.com',
            'blog\\.sina\\.cn',
            //Gionee <weidong><2013-7-28> modify for CR00843589 begin
            'wap\\.cmread\\.com',
            '3g\\.17k\\.com',
            '.+\\.easou\\.com',
            'm\\.zongheng\\.com',
            'wap\\.kanshu\\.com',
            'i\\.feiku\\.com',
            'wap\\.hjsm\\.tom\\.com',
            'm\\.huanxia\\.com'
            //'.+\\.pp\\.cn'
            //Gionee <weidong><2013-7-28> modify for CR00843589 end
        ];
        for (var i = 0; i < urlList.length; i++) {
            reg = new RegExp(urlList[i], 'i');
            if (reg.test(url)) {
                return i;
            }
        }
        return -1;
    }

    // 过滤掉style,class,onXXX事件代码
    function filterHtml(str) {
        //Gionee <weidong><2013-5-8> modify for CR00809288 begin
        if(!str){
            return "";
	     }
        //Gionee <weidong><2013-5-8> modify for CR00809288 end
        str = str.replace(/style\s*=\s*[\'\"][^\'\"]*[\'\"]/gi, '');
        str = str.replace(/class\s*=\s*[\'\"][^\'\"]*[\'\"]/gi, '');
        str = str.replace(/on[a-z]+\s*=\s*[\'\"][^\'\"]*[\'\"]/gi, '');
        str = str.replace(/(\s*$)/g, "");
        var idx = str.lastIndexOf('|');
        if(idx == str.length-1){
            str = str.substr(0,idx);
        }
        return str;
    }

    // 过滤掉script以后的代码(新浪)
    function filterHtmlScript(str) {
        //Gionee <weidong><2013-5-8> modify for CR00809288 begin
        if(!str){
            return "";
	     }
        //Gionee <weidong><2013-5-8> modify for CR00809288 end
        str = str.replace(/<script[^>]*>.*(?=<\/script>)<\/script>/gi, '#mx3_sign#');
        str = str.substring(0, str.indexOf('#mx3_sign#'));
        return str;
    }

    /**
        @description 过滤掉HTML代码特定字符串
        @param {string} str 要过滤的HTML代码
        @param {string} reg 匹配的字符串
    */
    function matchStrToNull(str, reg) {
        reg = new RegExp(reg, 'gi');
        str = str.replace(reg, '');
        return str;
    }

    /**
        @description 过滤掉HTML代码一类或某个tag (注意同类tag嵌套情况下及贪婪模式慎用!)
        @param {string} str 要过滤的HTML代码
        @param {string} reg 匹配的tag
        @param {number} isEnd 0 非贪婪 1 贪婪
        @param {string} attr 匹配的tag的属性
    */
    function matchTagToNull(str, tag, isEnd, attr) {
        attr = attr || '';
        if (isEnd == 0) {
            reg = '<(' + tag + ')\\s*' + attr + '[^>]*>[\\s\\S]*?<\\/\\1>'
        }
        else {
            reg = '<(' + tag + ')\\s*' + attr + '[^>]*>[\\s\\S]*<\\/\\1>';
        }
        reg = new RegExp(reg, 'gi');
        str = str.replace(reg, '');
        return str;
    }

    /**
        @description 过滤掉HTML代码的一部分
        @param {string} str 要过滤的HTML代码
        @param {string} reg 匹配的关键词
        @param {number} isEnd 0 去掉匹配位置之前的 1 去掉匹配位置之后的
    */
    function matchSectionToNul(str, reg, path) {
        if (path == 0) {
            reg = '^[\\s\\S]*' + reg;
        }
        else {
            reg += '[\\s\\S]*$';
        }
        reg = new RegExp(reg, 'i');
        return str.replace(reg, '');
    }

    // 把HTMLElement的类数组转换成字符串
    function objToStr(obj) {
        var str = '';
        for (var i = 0, l = obj.length; i < l; i++) {
            str += obj[i].innerHTML;
        }
        return str;
    }

})(window);




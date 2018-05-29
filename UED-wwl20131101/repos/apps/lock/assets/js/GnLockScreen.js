/**
 * 状态说明:
 * LOCKSCREEN_UNSTART = 0; （尚未下载）
 * LOCKSCREEN_DOWNLOADING = 1; （正在下载）
 * LOCKSCREEN_SUSPEND = 2; （暂停，包含网络断开，主动暂停）
 * LOCKSCREEN_FAIL = 3; （下载时文件发生损坏，返回此值，并删除文件）
 * LOCKSCREEN_DONE = 4; (下载完成)
 *
 *
 */

/**
 * 错误代码说明：
 * LOCKSCREEN_FAIL_DAMAGE = 1024; (文件损坏)
 * LOCKSCREEN_FAIL_SDCARD_NOT_EXIST = 2048; (SD卡不存在)
 * LOCKSCREEN_FAIL_WRITE_FILE_FAIL = 3072; （写文件失败）
 * LOCKSCREEN_FAIL_UNKOWN = 4096; （未知错误）
 *
 *
 *
 */

/**
 * 说明：
 * 参数传递方式为JSON
 * 以下msg，data均为参数。
 *
 * ex:
 * 	navigator.gnLockScreen.downloadStart(gnShowProcessRate, gnFail, 
 *  	{address : "http://192.168.1.1"});
 *
 * 在html中请包含cordova-2.0.0.js
 */

/**
 * start download the lockscreen.
 *
 * @param {Function} successCallback    The function to call when the download start is success
 *										data.progressRate 下载进度（在开始下载以后我们会定时返回此值，下载进度值在successCallback中读取）
 * @param {Function} errorCallback      The function to call when there is an error that can not start download. (OPTIONAL)
 *										msg.errorInfo 错误代码（如果出错误会调用errorCallback方法，同时会传递回错误代码）
 * @param {Options} options 			The options include download address (OPTIONAL)
 *										options.address 下载地址		
 * @return String                       
 */
downloadStart = function(successCallback, errorCallback, options) {
	successCallback();
}

/**
 * suspend download lockscreen.
 *
 * @param {Function} successCallback    The function to call when the download suspend is success
 * @param {Function} errorCallback      The function to call when there is an error that can not suspend download. (OPTIONAL)
 *										msg.errorInfo 错误代码（如果出错误会调用errorCallback方法，同时会传递回错误代码）
 * @param {Options}  options 			The options include the lockscreen id. (OPTIONAL)
 *										options.id 唯一标示符id
 * @return String                       
 */
downloadSuspend = function(successCallback, errorCallback, options) {
	successCallback();
}

/**
 * cancel download lockscreen.
 *
 * @param {Function} successCallback    The function to call when the download cancel is success
 * @param {Function} errorCallback      The function to call when there is an error that can not cancel download. (OPTIONAL)
 *										msg.errorInfo 错误代码（如果出错误会调用errorCallback方法，同时会传递回错误代码）
 * @param {Options}  options 			The options include the lockscreen id. (OPTIONAL)
 *										options.id 唯一标示符id
 * @return String                       
 */
downloadCancel = function(successCallback, errorCallback, options) {
	successCallback();
}

/**
 * Continue download lockscreen.
 *
 * @param {Function} successCallback    The function to call when the download continue is success
 *										data.progressRate 下载进度（在开始下载以后我们会定时返回此值，下载进度值在successCallback中读取）
 * @param {Function} errorCallback      The function to call when there is an error that can not continue download. (OPTIONAL)
 *										msg.errorInfo 错误代码（如果出错误会调用errorCallback方法，同时会传递回错误代码）
 * @param {Options}  options 			The options include the lockscreen id. (OPTIONAL)
 *										options.id 唯一标示符id
 * @return String                       
 */
downloadContinue = function(successCallback, errorCallback, options) {
	successCallback();
}

/**
 * watch download status.
 *
 * @param {Function} successCallback    The function to call when check lockscreen status

 * @param {Function} errorCallback      The function to call when there is an error that can not continue download. (OPTIONAL)
 *										msg.errorInfo 错误代码（如果出错误会调用errorCallback方法，同时会传递回错误代码）
 * @param {Options}  options 			The options include the lockscreen id. (OPTIONAL)
 *										options.id 唯一标示符id
 * @return String                       
 */
downloadWatch = function(successCallback, errorCallback, options) {
	successCallback();
}


/**
 * set lockscreen.
 *
 * @param {Function} successCallback    The function to call when the set lockscreen success
 * @param {Function} errorCallback      The function to call when there is an error that can not continue download. (OPTIONAL)
 *										msg.errorInfo 错误代码（如果出错误会调用errorCallback方法，同时会传递回错误代码）
 * @param {Options}  options 			The options include the lockscreen id. (OPTIONAL)
 *										options.id 唯一标示符id
 * @return String                       
 */
setLockScreen = function(successCallback, errorCallback, options) {
	successCallback();
}

/**
 * get lockscreen status.
 *
 * @param {Function} successCallback    The function to call when the set lockscreen success
 *										data.status 当前状态（返回后，在successCallback中读取）
 * @param {Function} errorCallback      The function to call when there is an error that can not continue download. (OPTIONAL)
 *										msg.errorInfo 错误代码（如果出错误会调用errorCallback方法，同时会传递回错误代码）
 * @param {Options}  options 			The options include the lockscreen id. (OPTIONAL)
 *										options.id 唯一标示符id
 * @return String                       
 */
getLockScreenStatus = function(successCallback, errorCallback, options) {
	successCallback();
}




/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-23
 * Time: 20:24:16
 * Contact: hankewins@gmail.com
 */

var mongoose = require('mongoose');
var config = require('../../config').config;

mongoose.connect(config.db, function (err) {
	if (err) {
		console.error('connect to %s error: ', config.db, err.message);
		process.exit(1);
	} 

	console.log('connect to %s success.',config.db)
});

//加载其他各Models
require('./user');
require('./task');

//对外接口
exports.User = mongoose.model('User');

exports.Task = mongoose.model('Task');

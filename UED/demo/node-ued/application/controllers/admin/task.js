/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-31
 * Time: 10:18:19
 * Contact: hankewins@gmail.com
 */

exports.list = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = '';
	res.render('admin/task/list', { 
		title: '管理后台',
		breadcrumb:[
			{'link':'#','name':'个人中心'},
			{'link':'/admin/calendar','name':'任务列表'}
		]
	});
};
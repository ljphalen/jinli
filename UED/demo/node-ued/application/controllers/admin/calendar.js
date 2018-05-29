/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-28
 * Time: 14:30:22
 * Contact: hankewins@gmail.com
 */

exports.index = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = '';
	res.render('admin/com/calendar', { 
		title: '管理后台',
		breadcrumb:[
			{'link':'#','name':'个人中心'},
			{'link':'/admin/calendar','name':'日历'}
		]
	});
};
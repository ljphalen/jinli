/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-25
 * Time: 14:37:54
 * Contact: hankewins@gmail.com
 */

var check = require('validator').check,
	sanitize = require('validator').sanitize;

var crypto = require('crypto');

var User = require('../../proxy').User;

exports.profile = function (req, res, next) {
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'list';
	res.render('admin/com/profile', { 
		title: '管理后台',
		breadcrumb:[
			{'link':'#','name':'个人中心'},
			{'link':'javascript:void(0);','name':'设置'}
		]
	});
}

exports.showSetting = function (req, res, next) {
	if (!req.session.user) {
		res.redirect('admin/login');
		return;
	}

	User.getUserById(req.session.user._id, function(err, data){
		if (err) {
			return next(err);	
		}

		if (req.query.save === 'success') {
			data.success = '保存成功！';
		}

		data.error = null;

		data.title = '管理后台';
		data.breadcrumb = [{'link':'#','name':'个人中心'},{'link':'javascript:void(0);','name':'设置'}];

		res.render('admin/user/setting', data);
	});

}

exports.setting = function (req, res, next) {

	function getInput(str) {
		return sanitize(req.body[str]).trim().toLowerCase();
	}

	var action = getInput('action');

	//setting
	if (action === 'change_setting') {
		var username  = req.session.user.username;
		var email     = req.session.user.email;
		var realname  = sanitize(req.body.realname).trim().toLowerCase();
		var thumbnail = sanitize(req.body.thumbnail).trim().toLowerCase();
		var sites     = sanitize(req.body.sites).trim().toLowerCase();
		var location  = sanitize(req.body.location).trim().toLowerCase();
		var signature = sanitize(req.body.signature).trim().toLowerCase();
		var profile   = sanitize(req.body.profile).trim().toLowerCase();
		var weibo     = sanitize(req.body.weibo).trim().toLowerCase();


		User.getUserById(req.session.user._id, function(err, data){
			//console.log(err.code);
			if (err) {
				req.session.error = "错误代码：" + "(" + err.code + ")";
			}

			data.realname  = realname;
			data.username  = username;
			data.email     = email;
			data.thumbnail = thumbnail;
			data.sites     = sites;
			data.location  = location;
			data.signature = signature;
			data.profile   = profile;
			data.weibo     = weibo;

			data.save(function(err){
				if (err) {
					return next(err);
				}
				res.redirect('admin/user/setting?save=success');
			});
		});	
	}

	//change password
	if (action === 'change_password') {
		var old_pass = sanitize(req.body.old_pass).trim();
		var new_pass = sanitize(req.body.new_pass).trim();
		var rep_pass = sanitize(req.body.rep_pass).trim();

		User.getUserById(req.session.user._id, function(err, data){
			if (err) {
				return next(err);
			}

			if (data.password !== md5(old_pass)) {
				res.render('admin/user/setting', {
					error: '当前密码不正确!',
					username: data.username,
					realname: data.realname,
					email: data.email,
					sites: data.sites,
					thumbnail: data.thumbnail,
					location: data.location,
					profile: data.profile,
					signature: data.signature,
					weibo: data.weibo,
					title: '管理后台',
					breadcrumb: [{'link':'#','name':'个人中心'},{'link':'javascript:void(0);','name':'设置'}]
				});
				return;
			}

			if (md5(new_pass) === md5(rep_pass) && new_pass != '') {
				data.password = md5(new_pass);

				data.save(function (err){
					if (err) {
						return next(err);
					}

					res.render('admin/user/setting', {
						success: '密码修改成功!',
						username: data.username,
						realname: data.realname,
						email: data.email,
						sites: data.sites,
						thumbnail: data.thumbnail,
						location: data.location,
						profile: data.profile,
						signature: data.signature,
						weibo: data.weibo,
						title: '管理后台',
						breadcrumb: [{'link':'#','name':'个人中心'},{'link':'javascript:void(0);','name':'设置'}]
					});
					return;
				});
			} else {
				res.render('admin/user/setting', {
					error: '密码修改失败!',
					username: data.username,
					realname: data.realname,
					email: data.email,
					sites: data.sites,
					thumbnail: data.thumbnail,
					location: data.location,
					profile: data.profile,
					signature: data.signature,
					weibo: data.weibo,
					title: '管理后台',
					breadcrumb: [{'link':'#','name':'个人中心'},{'link':'javascript:void(0);','name':'设置'}]
				});
			}

		});
	}
}

exports.do_userAdd = function (req, res, next) {

}

function md5(str) {
    var md5sum = crypto.createHash('md5');
    md5sum.update(str);
    str = md5sum.digest('hex');
    return str;
}
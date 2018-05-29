/*
 * GET home page.
 */

var check = require('validator').check,
	sanitize = require('validator').sanitize;

var crypto = require('crypto');
var config = require('../../../config').config;

var User = require('../../proxy').User;

exports.index = function(req, res){
	res.locals.menuLevel = 'login';
	res.locals.menuIndex = 'login';
	res.render('admin/login', { title: 'Hankewins的工作站' });
};

exports.index2 = function(req, res){
	res.locals.menuLevel = 'dashboard';
	res.locals.menuIndex = 'index2';
	res.render('admin/index_2', { title: 'Hankewins的工作站' });
};

/**
 * Show user login page.
 * @param  {HttpRequest} req
 * @param  {HttpResponse} res
 */
exports.showLogin = function (req, res) {
    var status = sanitize(req.param('account')).trim().toLowerCase();

    req.session._loginReferer = req.headers.referer;

    if (status) {
        req.session.account = 'other';
        res.render('admin/com/login', { title: '管理后台', status: 'other' });
    } else if (!status && req.session.user) {
        return res.redirect('admin/list');
    } else {
        res.render('admin/com/login', { title: '管理后台', status: '' });
    }

	

	
}

/**
 * Handle user login.
 *
 * @param {HttpRequest} req
 * @param {HttpResponse} res
 * @param {Function} next
 */
exports.login = function (req, res, next) {
    var username = sanitize(req.body.username).trim().toLowerCase();
    var password = sanitize(req.body.password).trim();
    var status = sanitize(req.body.status).trim().toLowerCase();

    var jumpUrl = [
        "admin/login",
        "admin/login?account=other"
    ];

    if (username == '') {
        req.session.error = "请输入用户名！";
        return res.redirect(status ? jumpUrl[1] : jumpUrl[0]);
    }

    if (password == '') {
        req.session.error = "请输入密码！";
        return res.redirect(status ? jumpUrl[1] : jumpUrl[0]);
    }

    User.getUserByUserName(username, function (err, user) {
        if (err) {
            return next(err);
        }
        console.log(user);
        if (!user) {
            req.session.error = "用户不存在！";
            return res.redirect(status ? jumpUrl[1] : jumpUrl[0]);
        }

        password = md5(password);
        if (password !== user.password) {
            req.session.error = "密码错误！";
            return res.redirect(status ? jumpUrl[1] : jumpUrl[0]);
        }

        // if (!user.active) {
        //     // 从新发送激活邮件
        //     mail.sendActiveMail(user.email, md5(user.email + config.session_secret), user.name, user.email);
        //     return res.render('/admin/com/login', { error: '此帐号还没有被激活，激活链接已发送到 ' + user.email + ' 邮箱，请查收。' });
        // }
        // 

        req.session.user = user;
        req.session.isLocked = false;
        req.session.status = null;
        req.session.account = null;

        res.redirect('admin/list');
    }); 
};

exports.logout = function (req, res, next) {
    req.session = null;
    res.redirect('admin/login');
};

exports.auth = function(req, res, next) {
    res.locals.menuLevel = '';
    res.locals.menuIndex = '';
    res.locals.skins = req.cookies.skins ? req.cookies.skins : 'default';
    res.locals.message = '';
    res.locals.user = '';
    res.locals.user = req.session.user;
    res.locals.isLogin = req.session.user ? true : false;
    var err = req.session.error;
    delete req.session.error;
    if (err) 
        res.locals.message = '<div class="alert alert-error"><button class="close" data-dismiss="alert">×</button>' + err + '</div>';

    next();
};

function md5(str) {
    var md5sum = crypto.createHash('md5');
    md5sum.update(str);
    str = md5sum.digest('hex');
    return str;
}

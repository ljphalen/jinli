/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-24
 * Time: 19:33:23
 * Contact: hankewins@gmail.com
 */

var check = require('validator').check, sanitize = require('validator').sanitize;
var crypto = require('crypto');

exports.locked = function (req, res, next) {
    //console.log('aaa');
    req.session.isLocked = true;
    res.locals.menuLevel = 'com';
    res.locals.menuIndex = 'lock';
    res.render('admin/com/lock', { title: '管理后台' });
}

exports.unlock = function (req, res, next) {
    var password = md5(sanitize(req.body.password).trim());

    console.log(res.locals.user,password);

    if (res.locals.user.password == password) {
        req.session.isLocked = false;
        res.redirect('admin/list');
    } else {
        //req.session.error = "cookie已过期，请重新登录！";
        req.session.error = "密码输入错误，还有4次机会！";
        res.redirect('admin/lockscreen');
    }
}

function md5(str) {
    var md5sum = crypto.createHash('md5');
    md5sum.update(str);
    str = md5sum.digest('hex');
    return str;
}

/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-23
 * Time: 19:13:47
 * Contact: hankewins@gmail.com
 */

var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var config = require('../../config').config;

// -- TableName admin_users 后台用户表
// -- Created By hankewins@gmail.com@2013-10-23 
// -- Fields uid          用户ID 
// -- Fields username     用户名
// -- Fields realname     用户真实姓名
// -- Fields password     用户密码
// -- Fields email        邮箱地址
// -- Fields sites        个人站点
// -- Fields thumbnail    个人图像URL
// -- Fields location     所在地点
// -- Fields signature    个性签名
// -- Fields profile      个人简介
// -- Fields weibo        个人微博
// -- Fields avatar       avatar图像
// -- Fields actived      激活状态
// -- Fields created      注册时间
// -- Fields updated      更新时间

var UserSchema = new Schema({
    //uid: { type: String, index: true },
    username: { type: String, unique: true },
    realname: { type: String, index: true},
    password: { type: String },
    email: { type: String, unique: true },
    sites: { type: String },
    thumbnail: {type: String},
    location: { type: String },
    signature: { type: String },
    profile: { type: String },
    weibo: { type: String },
    avatar: { type: String },
    active: { type: Boolean, default: true },
    created: { type: Date, default: Date.now },
    updated: { type: Date, default: Date.now }
});

/**
 * 创建一个虚拟的字段 avatar_url
 * @return {[type]} [description]
 */
UserSchema.virtual('avatar_url').get(function () {
    var url = this.thumbnail || this.avatar || "https://1.gravatar.com/avatar/3c727687ac031937714cadcce7c9418d?s=29" || config.site_static_host + '/images/user_icon&48.png';
    //return url.replace('http://www.gravatar.com/', 'http://gravatar.qiniudn.com/');
    return url;
});

mongoose.model('User', UserSchema);
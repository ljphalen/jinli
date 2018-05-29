/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-23
 * Time: 20:29:55
 * Contact: hankewins@gmail.com
 */

// 引入对应的Model
var models = require('../models');
var User = models.User;

/**
 * 根据指定用户查找对应用户信息
 * Callback:
 * - err, 数据库异常
 * - users, 用户列表
 * @param {Array} names 用户名列表
 * @param {Function} callback 回调函数
 */
exports.getAllByUserName = function (names, callback) {
    if (names.length === 0) {
        return callback(null, []);
    }

    User.find({ name: { $in: names } }, callback);
};

/**
 * 根据用户名查找用户
 * Callback:
 * - err, 数据库异常
 * - user, 用户
 * @param {String} username 用户名
 * @param {Function} callback 回调函数
 */
exports.getUserByUserName = function (username, callback) {
    User.findOne({'username': username}, callback);
};

/**
 * 根据用户ID，查找用户
 * Callback:
 * - err, 数据库异常
 * - user, 用户
 * @param {String} id 用户ID
 * @param {Function} callback 回调函数
 */
exports.getUserById = function (id, callback) {
    User.findOne({_id: id}, callback);
};

/**
 * 根据用户真实姓名，查找用户
 * Callback:
 * - err, 数据库异常
 * - user, 用户
 * @param {String} name 真实姓名
 * @param {Function} callback 回调函数
 */
exports.getUserByRealName = function (name, callback) {
    User.findOne({realname: name}, callback);
};

/**
 * 根据邮箱，查找用户
 * Callback:
 * - err, 数据库异常
 * - user, 用户
 * @param {String} email 邮箱地址
 * @param {Function} callback 回调函数
 */
exports.getUserByEmail = function (email, callback) {
    User.findOne({email: email}, callback);
};

/**
 * 根据用户ID列表，获取一组用户
 * Callback:
 * - err, 数据库异常
 * - users, 用户列表
 * @param {Array} ids 用户ID列表
 * @param {Function} callback 回调函数
 */
exports.getUsersByIds = function (ids, callback) {
    User.find({'_id': {'$in': ids}}, callback);
};

/**
 * 根据关键字，获取一组用户
 * Callback:
 * - err, 数据库异常
 * - users, 用户列表
 * @param {String} query 关键字
 * @param {Object} opt 选项
 * @param {Function} callback 回调函数
 */
exports.getUsersByQuery = function (query, opt, callback) {
    User.find(query, [], opt, callback);
};

/**
 * 根据查询条件，获取一个用户
 * Callback:
 * - err, 数据库异常
 * - user, 用户
 * @param {String} name 用户名
 * @param {String} key 激活码
 * @param {Function} callback 回调函数
 */
exports.getUserByQuery = function (name, key, callback) {
    User.findOne({name: name, retrieve_key: key}, callback);
};

/**
 * 添加用户和更新用户
 * Callback:
 * - err, 数据库异常
 * - user, 用户
 * @param  {Object}   options  
 * @param  {Function} callback [description]
 */
exports.newAndSave = function (options, callback) {
    var user = new User();
    user.username  = options.username;
    user.realname  = options.realname || '';
    user.password  = options.password;
    user.email     = options.email;
    user.thumbnail = options.thumbnail || '';
    user.location  = options.location || '';
    user.signature = options.signature || '';
    user.profile   = options.profile || '';
    user.weibo     = options.weibo || '';
    user.sites     = options.sites || '';
    user.avatar    = options.avatar || '';
    user.active    = options.active || true;
    user.created   = Date.now();
    user.updated   = Date.now();

    user.save(callback);
}

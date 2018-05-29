/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-30
 * Time: 15:04:17
 * Contact: hankewins@gmail.com
 */

var mongoose = require('mongoose');
var Schema = mongoose.Schema;
var config = require('../../config').config;

// -- TableName admin_tasks 后台任务表
// -- Created By hankewins@gmail.com@2013-10-30 
// -- Fields title      任务标题 
// -- Fields author     任务发布者
// -- Fields content    任务内容
// -- Fields auditor    任务审核者
// -- Fields status     任务状态
// -- Fields created    创建日期
// -- Fields updated    更新日期

var TaskSchema = new Schema({
    title: String,
    author: String,
    content: String,
    auditor: String,
    status: Boolean,
    created: { type: Date, default: Date.now },
    updated: { type: Date, default: Date.now },

});

mongoose.model('Task', TaskSchema);
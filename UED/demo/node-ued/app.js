
/**
 * Module dependencies.
 */
var config = require('./config').config;
var express = require('express');
var http = require('http');
var path = require('path');

var app = express();


var maxAge = 3600000 * 24 * 30;
// all environments
app.set('port', process.env.PORT || 3000);
app.set('views', __dirname + '/application/views');
app.set('view engine', 'html');
app.engine('html', require('ejs').renderFile);
app.use(express.favicon(__dirname + '/favicon.ico', { maxAge: maxAge }));
app.use(express.logger('dev'));
app.use(express.bodyParser());
app.use(express.methodOverride());
//cookie
app.use(express.cookieParser());
//session
app.use(express.cookieSession({
	secret: config.session_secret
}));

//定义中间件用于用户认证
app.use(require('./application/controllers/admin/login').auth);


app.use(require('stylus').middleware(__dirname + '/public'));
//配置上传文件目录
//app.use('/upload/', express.static(config.upload_dir, { maxAge: maxAge }));
//配置静态文件
//var staticDir = path.join(__dirname, 'public');
app.use(express.static(path.join(__dirname, 'public')));

app.use(app.router);

// development only
if ('development' == app.get('env')) {
  app.use(express.errorHandler());
}

//require(__dirname + '/application/controllers/front/routes')(app); // Front routes
require(__dirname + '/application/controllers/admin/routes')(app); // Backend routes

http.createServer(app).listen(app.get('port'), function(){
  console.log('Express server listening on port ' + app.get('port'));
});


module.exports = app;
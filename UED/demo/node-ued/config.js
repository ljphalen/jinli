/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-23
 * Time: 18:58:39
 * Contact: hankewins@gmail.com
 */

var path = require('path');

exports.config = {
	debug: true,
	name: 'UED',
	description: 'UED dev',
	version: '0.0.1',

	//site setting
	site_headers: [
		'<meta name="author" content="hankewins@gmail.com" />'
	],
	host: 'dev.node.gionee.com',
	site_logo: '', //default is 'name'
	site_navs: [
		// [ path, title, [target=''] ]
		[ '/about', '关于']
	],

	site_static_host: '',

	upload_dir: path.join(__dirname, 'public', 'uploads', 'images'),

	db: 'mongodb://localhost/node_ued_dev',
	session_secret: 'node_ued',
	auth_cookie_name: 'node_ued',
	port: 3000,

	site_links: [
		{
			'text': 'Node 官方网站',
			'url': 'http://nodejs.org/'
		}
	],

	//MAIL SMTP
	mail_opts: {
		host: 'smtp@126.com',
		port: 25,
		auth: {
			user: 'hankewins@126.com',
			pass: 'xp1234567'
		}
	}
};
/*
 * Created with Sublime Text 2.
 * User: Hankewins
 * Date: 2013-10-22
 * Time: 20:05:24
 * Contact: hankewins@gmail.com
 */

exports.list = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'list';
	res.render('admin/com/list', { 
		title: '管理后台',
		breadcrumb:[
			{'link':'#','name':'后台公共组件'},
			{'link':'/admin/list','name':'列表'}
		]
	});
};

exports.editable = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'editable';
	res.render('admin/com/editable', { 
		title: '管理后台',
		breadcrumb:[
			{'link':'#','name':'后台公共组件'},
			{'link':'/admin/editable','name':' 可编辑的列表'}
		]
	});
};

exports.pagination = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'pagination';
	res.render('admin/com/pagination', { 
		title: '管理后台',
		breadcrumb:[
			{'link':'#','name':'分页'},
			{'link':'/admin/form','name':'基础分页'}
		]
	});
};

exports.form = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'form';
	res.render('admin/com/form', { 
		title: '管理后台',
		breadcrumb:[
			{'link':'#','name':'Form表单'},
			{'link':'/admin/form','name':'基础样式'}
		]
	});
};

exports.component = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'component';
	res.render('admin/com/form_component', { 
		title: 'Form Components',
		breadcrumb:[
			{'link':'#','name':'Form表单'},
			{'link':'/admin/form_component','name':'表单组件'}
		]
	});
};

exports.validation = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'validation';
	res.render('admin/com/form_validation', { 
		title: 'Form validation',
		breadcrumb:[
			{'link':'#','name':'Form表单'},
			{'link':'/admin/form_component','name':'表单验证'}
		]
	});
};

exports.formadd = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'formadd';
	res.render('admin/com/form_add', { 
		title: '管理后台',
		breadcrumb:[
			{'link':'#','name':'Form表单'},
			{'link':'/admin/form/add','name':'表单添加'}
		]
	});
};

exports.login = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'login';
	res.render('admin/com/login', { title: '管理后台' });
};

/**
 * [logout description]
 * @param  {[type]} req [description]
 * @param  {[type]} res [description]
 * @return {[type]}     [description]
 */
exports.logout = function(req, res){
	//res.locals.menuLevel = 'com';
	//res.locals.menuIndex = 'login';
	res.render('admin/com/login', { title: '管理后台' });
};

/**
 * [tips description]
 * @param  {[type]} req [description]
 * @param  {[type]} res [description]
 * @return {[type]}     [description]
 */
exports.tips = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'tips';
	res.render('admin/com/tips', { 
		title: '管理后台',
		breadcrumb:[]
	});
};

/**
 * [lock description]
 * @param  {[type]} req [description]
 * @param  {[type]} res [description]
 * @return {[type]}     [description]
 */
exports.lock = function(req, res){
	res.locals.menuLevel = 'com';
	res.locals.menuIndex = 'lock';
	res.render('admin/com/lock', { title: '管理后台' });
};

exports.error404 = function (req, res) {
	res.render('admin/com/404', { title: '404 错误页面' });
}
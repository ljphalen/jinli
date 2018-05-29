var dashboard  = require('./dashboard');
var ui         = require('./ui');
var login      = require('./login');
var components = require('./components');
var tables     = require('./tables');
var icons      = require('./icons');
var portlets   = require('./portlets');
var extras     = require('./extras');
var forms      = require('./forms');
var lockscreen = require('./lockscreen');
var com        = require('./com');
var user       = require('./user');
var calendar   = require('./calendar');
var task       = require('./task');

module.exports = function(app){
	app.get('/admin', authentication);
	app.get('/admin', com.list);
	app.get('/admin/index', dashboard.index);
	app.get('/admin/index2', dashboard.index2);

	//UI Routes
	app.get('/admin/ui/general', ui.general);
	app.get('/admin/ui/buttons', ui.buttons);
	app.get('/admin/ui/jquery', ui.jquery);
	app.get('/admin/ui/tabs', ui.tabs);
	app.get('/admin/ui/typography', ui.typography);
	app.get('/admin/ui/tree', ui.tree);
	app.get('/admin/ui/nestable', ui.nestable);

	//Components
	
	app.get('/admin/components/calendar', components.calendar);
	app.get('/admin/components/grids', components.grids);
	app.get('/admin/components/charts', components.charts);
	app.get('/admin/components/messengers', components.messengers);
	app.get('/admin/components/gallery', components.gallery);

	//Forms
	app.get('/admin/forms/layout', forms.layout);
	app.get('/admin/forms/component', forms.component);
	app.get('/admin/forms/validation', forms.validation);
	app.get('/admin/forms/wizard', forms.wizard);
	app.get('/admin/forms/upload', forms.upload);

	//Tables
	app.get('/admin/tables/basic', tables.basic);
	app.get('/admin/tables/manage', tables.manage);
	app.get('/admin/tables/edit', tables.edit);

	//Portlets
	app.get('/admin/portlets/general', portlets.general);
	app.get('/admin/portlets/draggable', portlets.draggable);

	//ICONS
	app.get('/admin/icons/awesome', icons.awesome);
	app.get('/admin/icons/glyphicons', icons.glyphicons);

	//Extras
	app.get('/admin/extras/lock', extras.lock);
	app.get('/admin/extras/profile', extras.profile);
	app.get('/admin/extras/invoice', extras.invoice);
	app.get('/admin/extras/pricing', extras.pricing);
	app.get('/admin/extras/inbox', extras.inbox);
	app.get('/admin/extras/search', extras.search);
	app.get('/admin/extras/faq', extras.faq);
	app.get('/admin/extras/404', extras.err404);
	app.get('/admin/extras/500', extras.err500);

	//Login
	//app.get('/admin/login', login.index);
	app.get('/admin/login/index', login.index);

	//com
	app.get('/admin/list', authentication);
	app.get('/admin/list', com.list);

	app.get('/admin/edit-table', authentication);
	app.get('/admin/edit-table', com.editable);

	app.get('/admin/tips', authentication);
	app.get('/admin/tips', com.tips);

	app.get('/admin/lockscreen', authentication);
	app.get('/admin/lockscreen', lockscreen.locked);

	app.post('/admin/do/lockscreen',lockscreen.unlock);

	//app.get('/admin/login', notAuthentication);
	app.get('/admin/login', login.showLogin);
	app.post('/admin/login', login.login);

	app.get('/admin/logout', login.logout);

	app.get('/admin/form', authentication);
	app.get('/admin/form', com.form);

	app.get('/admin/pagination', authentication);
	app.get('/admin/pagination', com.pagination);

	app.get('/admin/form/component', authentication);
	app.get('/admin/form/component', com.component);

	app.get('/admin/form/validation', authentication);
	app.get('/admin/form/validation', com.validation);

	app.get('/admin/form/add', authentication);
	app.get('/admin/form/add', com.formadd);


	// user
	app.get('/admin/user/profile', authentication);
	app.get('/admin/user/profile', user.profile);

	app.get('/admin/user/setting', authentication);
	app.get('/admin/user/setting', user.showSetting);
	app.post('/admin/user/setting', user.setting);

	app.get('/admin/user/add', authentication);
	//app.get('/admin/user/add', user.showAdd);
	//app.post('/admin/user/do/add', user.add);


	app.get('/admin/task', task.list);	
	app.get('/admin/task/list', task.list);	

	app.get('/admin/calendar', authentication);
	app.get('/admin/calendar', calendar.index);





	app.get('/admin/404', com.error404);

	//404
	app.get('*', function (req, res) {
		res.locals.menuLevel = 'extras';
		res.locals.menuIndex = 'err404';
		res.redirect('admin/404');
		//res.render('admin/com/404', { title: 'Hankewins的工作站' });
	});

};

function authentication(req, res, next) {
	if (req.session.account === 'other') {
		if (req.path == '/admin/login'){ //防止出现重复循环
			//return res.redirect('admin/lockscreen');
			next();
		}
	}

	if (!req.session.user) {
		req.session.error = '请先登陆';
		return res.redirect('admin/login');
	}

	if (req.session.isLocked === true) {
		//req.session.error = '请先登陆';
		if (req.path != '/admin/lockscreen'){ //防止出现重复循环
			return res.redirect('admin/lockscreen');
		}
	}

	next();
}

function notAuthentication(req, res, next) {
	if(req.session.user) {
		return res.redirect('admin/list');
	}
	next();
}


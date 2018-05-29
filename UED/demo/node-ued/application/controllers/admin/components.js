/*
 * GET home page.
 */

exports.calendar = function(req, res){
	res.locals.menuLevel = 'components';
	res.locals.menuIndex = 'calendar';
	res.render('admin/calendar', { title: 'Hankewins的工作站' });
};

exports.grids = function(req, res){
	res.locals.menuLevel = 'components';
	res.locals.menuIndex = 'grids';
	res.render('admin/grids', { title: 'Hankewins的工作站' });
};

exports.charts = function(req, res){
	res.locals.menuLevel = 'components';
	res.locals.menuIndex = 'charts';
	res.render('admin/charts', { title: 'Hankewins的工作站' });
};

exports.messengers = function(req, res){
	res.locals.menuLevel = 'components';
	res.locals.menuIndex = 'messengers';
	res.render('admin/messengers', { title: 'Hankewins的工作站' });
};

exports.gallery = function(req, res){
	res.locals.menuLevel = 'components';
	res.locals.menuIndex = 'gallery';
	res.render('admin/gallery', { title: 'Hankewins的工作站' });
};
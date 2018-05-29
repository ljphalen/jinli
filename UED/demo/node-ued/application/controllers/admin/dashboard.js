/*
 * GET home page.
 */

exports.index = function(req, res){
	res.locals.menuLevel = 'dashboard';
	res.locals.menuIndex = 'index';
	res.render('admin/index', { title: 'Hankewins的工作站' });
};

exports.index2 = function(req, res){
	res.locals.menuLevel = 'dashboard';
	res.locals.menuIndex = 'index2';
	res.render('admin/index_2', { title: 'Hankewins的工作站' });
};
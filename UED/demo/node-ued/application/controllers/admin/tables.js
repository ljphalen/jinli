/*
 * GET home page.
 */

exports.basic = function(req, res){
	res.locals.menuLevel = 'tables';
	res.locals.menuIndex = 'basic';
	res.render('admin/basic_table', { title: 'Hankewins的工作站' });
};

exports.manage = function(req, res){
	res.locals.menuLevel = 'tables';
	res.locals.menuIndex = 'manage';
	res.render('admin/managed_table', { title: 'Hankewins的工作站' });
};

exports.edit = function(req, res){
	res.locals.menuLevel = 'tables';
	res.locals.menuIndex = 'edit';
	res.render('admin/editable_table', { title: 'Hankewins的工作站' });
};

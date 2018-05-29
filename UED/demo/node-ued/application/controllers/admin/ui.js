/*
 * GET home page.
 */

exports.general = function(req, res){
	res.locals.menuLevel = 'ui';
	res.locals.menuIndex = 'general';
	res.render('admin/ui_elements_general', { title: 'Hankewins的工作站' });
};

exports.buttons = function(req, res){
	res.locals.menuLevel = 'ui';
	res.locals.menuIndex = 'buttons';
	res.render('admin/ui_elements_buttons', { title: 'Hankewins的工作站' });
};

exports.jquery = function(req, res){
	res.locals.menuLevel = 'ui';
	res.locals.menuIndex = 'jquery';
	res.render('admin/jquery_ui', { title: 'Hankewins的工作站' });
};

exports.tabs = function(req, res){
	res.locals.menuLevel = 'ui';
	res.locals.menuIndex = 'tabs';
	res.render('admin/ui_elements_tabs_accordions', { title: 'Hankewins的工作站' });
};

exports.typography = function(req, res){
	res.locals.menuLevel = 'ui';
	res.locals.menuIndex = 'typography';
	res.render('admin/ui_elements_typography', { title: 'Hankewins的工作站' });
};

exports.tree = function(req, res){
	res.locals.menuLevel = 'ui';
	res.locals.menuIndex = 'tree';
	res.render('admin/tree_view', { title: 'Hankewins的工作站' });
};

exports.nestable = function(req, res){
	res.locals.menuLevel = 'ui';
	res.locals.menuIndex = 'nestable';
	res.render('admin/nestable', { title: 'Hankewins的工作站' });
};
/*
 * GET home page.
 */

exports.general = function(req, res){
	res.locals.menuLevel = 'portlets';
	res.locals.menuIndex = 'general';
	res.render('admin/general_portlet', { title: 'Hankewins的工作站' });
};

exports.draggable = function(req, res){
	res.locals.menuLevel = 'portlets';
	res.locals.menuIndex = 'draggable';
	res.render('admin/draggable_portlet', { title: 'Hankewins的工作站' });
};
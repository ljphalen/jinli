/*
 * GET home page.
 */

exports.layout = function(req, res){
	res.locals.menuLevel = 'forms';
	res.locals.menuIndex = 'layout';
	res.render('admin/form_layout', { title: 'Hankewins的工作站' });
};

exports.component = function(req, res){
	res.locals.menuLevel = 'forms';
	res.locals.menuIndex = 'component';
	res.render('admin/form_component', { title: 'Hankewins的工作站' });
};

exports.validation = function(req, res){
	res.locals.menuLevel = 'forms';
	res.locals.menuIndex = 'validation';
	res.render('admin/form_validation', { title: 'Hankewins的工作站' });
};

exports.wizard = function(req, res){
	res.locals.menuLevel = 'forms';
	res.locals.menuIndex = 'wizard';
	res.render('admin/form_wizard', { title: 'Hankewins的工作站' });
};

exports.upload = function(req, res){
	res.locals.menuLevel = 'forms';
	res.locals.menuIndex = 'upload';
	res.render('admin/dropzone', { title: 'Hankewins的工作站' });
};

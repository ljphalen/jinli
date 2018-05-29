/*
 * GET home page.
 */

exports.lock = function(req, res){
	res.locals.menuLevel = 'extras';
	res.locals.menuIndex = 'lock';
	res.render('admin/lock', { title: 'Hankewins的工作站' });
};

exports.profile = function(req, res){
	res.locals.menuLevel = 'extras';
	res.locals.menuIndex = 'profile';
	res.render('admin/profile', { title: 'Hankewins的工作站' });
};

exports.invoice = function(req, res){
	res.locals.menuLevel = 'extras';
	res.locals.menuIndex = 'invoice';
	res.render('admin/invoice', { title: 'Hankewins的工作站' });
};

exports.pricing = function(req, res){
	res.locals.menuLevel = 'extras';
	res.locals.menuIndex = 'pricing';
	res.render('admin/pricing', { title: 'Hankewins的工作站' });
};

exports.inbox = function(req, res){
	res.locals.menuLevel = 'extras';
	res.locals.menuIndex = 'inbox';
	res.render('admin/inbox', { title: 'Hankewins的工作站' });
};

exports.search = function(req, res){
	res.locals.menuLevel = 'extras';
	res.locals.menuIndex = 'search';
	res.render('admin/search_result', { title: 'Hankewins的工作站' });
};

exports.faq = function(req, res){
	res.locals.menuLevel = 'extras';
	res.locals.menuIndex = 'faq';
	res.render('admin/faq', { title: 'Hankewins的工作站' });
};

exports.err404 = function(req, res){
	res.locals.menuLevel = 'extras';
	res.locals.menuIndex = 'err404';
	res.render('admin/404', { title: 'Hankewins的工作站' });
};


exports.err500 = function(req, res){
	res.locals.menuLevel = 'extras';
	res.locals.menuIndex = 'err500';
	res.render('admin/500', { title: 'Hankewins的工作站' });
};
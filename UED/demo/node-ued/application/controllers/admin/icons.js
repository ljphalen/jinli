/*
 * GET home page.
 */

exports.awesome = function(req, res){
	res.locals.menuLevel = 'icons';
	res.locals.menuIndex = 'awesome';
	res.render('admin/font_awesome', { title: 'Hankewins的工作站' });
};

exports.glyphicons = function(req, res){
	res.locals.menuLevel = 'icons';
	res.locals.menuIndex = 'glyphicons';
	res.render('admin/glyphicons', { title: 'Hankewins的工作站' });
};

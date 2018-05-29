-- 修复产权证无scyn的问题

update apks set app_cert =1 where id in (
	select apk_id from app_cert
);
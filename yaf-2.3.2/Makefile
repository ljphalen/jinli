srcdir = /home/ljp/www/motion/trunk/yaf-2.3.2
builddir = /home/ljp/www/motion/trunk/yaf-2.3.2
top_srcdir = /home/ljp/www/motion/trunk/yaf-2.3.2
top_builddir = /home/ljp/www/motion/trunk/yaf-2.3.2
EGREP = /bin/grep -E
SED = /bin/sed
CONFIGURE_COMMAND = './configure' '--with-php-config=/usr/local/php56/bin/php-config'
CONFIGURE_OPTIONS = '--with-php-config=/usr/local/php56/bin/php-config'
SHLIB_SUFFIX_NAME = so
SHLIB_DL_SUFFIX_NAME = so
ZEND_EXT_TYPE = zend_extension
RE2C = exit 0;
AWK = gawk
shared_objects_yaf = yaf.lo yaf_application.lo yaf_bootstrap.lo yaf_dispatcher.lo yaf_exception.lo yaf_config.lo configs/yaf_config_ini.lo configs/yaf_config_simple.lo yaf_request.lo requests/yaf_request_http.lo requests/yaf_request_simple.lo yaf_response.lo responses/yaf_response_http.lo responses/yaf_response_cli.lo yaf_view.lo views/yaf_view_interface.lo views/yaf_view_simple.lo yaf_controller.lo yaf_action.lo yaf_router.lo routes/yaf_route_interface.lo routes/yaf_route_static.lo routes/yaf_route_simple.lo routes/yaf_route_supervar.lo routes/yaf_route_regex.lo routes/yaf_route_rewrite.lo routes/yaf_route_map.lo yaf_loader.lo yaf_registry.lo yaf_plugin.lo yaf_session.lo
PHP_PECL_EXTENSION = yaf
PHP_MODULES = $(phplibdir)/yaf.la
PHP_ZEND_EX =
all_targets = $(PHP_MODULES) $(PHP_ZEND_EX)
install_targets = install-modules install-headers
prefix = /usr/local/php56
exec_prefix = $(prefix)
libdir = ${exec_prefix}/lib
prefix = /usr/local/php56
phplibdir = /home/ljp/www/motion/trunk/yaf-2.3.2/modules
phpincludedir = /usr/local/php56/include/php
CC = cc
CFLAGS = -g -O2
CFLAGS_CLEAN = $(CFLAGS)
CPP = cc -E
CPPFLAGS = -DHAVE_CONFIG_H
CXX =
CXXFLAGS =
CXXFLAGS_CLEAN = $(CXXFLAGS)
EXTENSION_DIR = /usr/local/php56/lib/php/extensions/no-debug-non-zts-20131226
PHP_EXECUTABLE = /usr/local/php56/bin/php
EXTRA_LDFLAGS =
EXTRA_LIBS =
INCLUDES = -I/usr/local/php56/include/php -I/usr/local/php56/include/php/main -I/usr/local/php56/include/php/TSRM -I/usr/local/php56/include/php/Zend -I/usr/local/php56/include/php/ext -I/usr/local/php56/include/php/ext/date/lib
LFLAGS =
LDFLAGS =
SHARED_LIBTOOL =
LIBTOOL = $(SHELL) $(top_builddir)/libtool
SHELL = /bin/sh
INSTALL_HEADERS =
mkinstalldirs = $(top_srcdir)/build/shtool mkdir -p
INSTALL = $(top_srcdir)/build/shtool install -c
INSTALL_DATA = $(INSTALL) -m 644

DEFS = -DPHP_ATOM_INC -I$(top_builddir)/include -I$(top_builddir)/main -I$(top_srcdir)
COMMON_FLAGS = $(DEFS) $(INCLUDES) $(EXTRA_INCLUDES) $(CPPFLAGS) $(PHP_FRAMEWORKPATH)

all: $(all_targets) 
	@echo
	@echo "Build complete."
	@echo "Don't forget to run 'make test'."
	@echo

build-modules: $(PHP_MODULES) $(PHP_ZEND_EX)

build-binaries: $(PHP_BINARIES)

libphp$(PHP_MAJOR_VERSION).la: $(PHP_GLOBAL_OBJS) $(PHP_SAPI_OBJS)
	$(LIBTOOL) --mode=link $(CC) $(CFLAGS) $(EXTRA_CFLAGS) -rpath $(phptempdir) $(EXTRA_LDFLAGS) $(LDFLAGS) $(PHP_RPATHS) $(PHP_GLOBAL_OBJS) $(PHP_SAPI_OBJS) $(EXTRA_LIBS) $(ZEND_EXTRA_LIBS) -o $@
	-@$(LIBTOOL) --silent --mode=install cp $@ $(phptempdir)/$@ >/dev/null 2>&1

libs/libphp$(PHP_MAJOR_VERSION).bundle: $(PHP_GLOBAL_OBJS) $(PHP_SAPI_OBJS)
	$(CC) $(MH_BUNDLE_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS) $(LDFLAGS) $(EXTRA_LDFLAGS) $(PHP_GLOBAL_OBJS:.lo=.o) $(PHP_SAPI_OBJS:.lo=.o) $(PHP_FRAMEWORKS) $(EXTRA_LIBS) $(ZEND_EXTRA_LIBS) -o $@ && cp $@ libs/libphp$(PHP_MAJOR_VERSION).so

install: $(all_targets) $(install_targets)

install-sapi: $(OVERALL_TARGET)
	@echo "Installing PHP SAPI module:       $(PHP_SAPI)"
	-@$(mkinstalldirs) $(INSTALL_ROOT)$(bindir)
	-@if test ! -r $(phptempdir)/libphp$(PHP_MAJOR_VERSION).$(SHLIB_DL_SUFFIX_NAME); then \
		for i in 0.0.0 0.0 0; do \
			if test -r $(phptempdir)/libphp$(PHP_MAJOR_VERSION).$(SHLIB_DL_SUFFIX_NAME).$$i; then \
				$(LN_S) $(phptempdir)/libphp$(PHP_MAJOR_VERSION).$(SHLIB_DL_SUFFIX_NAME).$$i $(phptempdir)/libphp$(PHP_MAJOR_VERSION).$(SHLIB_DL_SUFFIX_NAME); \
				break; \
			fi; \
		done; \
	fi
	@$(INSTALL_IT)

install-binaries: build-binaries $(install_binary_targets)

install-modules: build-modules
	@test -d modules && \
	$(mkinstalldirs) $(INSTALL_ROOT)$(EXTENSION_DIR)
	@echo "Installing shared extensions:     $(INSTALL_ROOT)$(EXTENSION_DIR)/"
	@rm -f modules/*.la >/dev/null 2>&1
	@$(INSTALL) modules/* $(INSTALL_ROOT)$(EXTENSION_DIR)

install-headers:
	-@if test "$(INSTALL_HEADERS)"; then \
		for i in `echo $(INSTALL_HEADERS)`; do \
			i=`$(top_srcdir)/build/shtool path -d $$i`; \
			paths="$$paths $(INSTALL_ROOT)$(phpincludedir)/$$i"; \
		done; \
		$(mkinstalldirs) $$paths && \
		echo "Installing header files:          $(INSTALL_ROOT)$(phpincludedir)/" && \
		for i in `echo $(INSTALL_HEADERS)`; do \
			if test "$(PHP_PECL_EXTENSION)"; then \
				src=`echo $$i | $(SED) -e "s#ext/$(PHP_PECL_EXTENSION)/##g"`; \
			else \
				src=$$i; \
			fi; \
			if test -f "$(top_srcdir)/$$src"; then \
				$(INSTALL_DATA) $(top_srcdir)/$$src $(INSTALL_ROOT)$(phpincludedir)/$$i; \
			elif test -f "$(top_builddir)/$$src"; then \
				$(INSTALL_DATA) $(top_builddir)/$$src $(INSTALL_ROOT)$(phpincludedir)/$$i; \
			else \
				(cd $(top_srcdir)/$$src && $(INSTALL_DATA) *.h $(INSTALL_ROOT)$(phpincludedir)/$$i; \
				cd $(top_builddir)/$$src && $(INSTALL_DATA) *.h $(INSTALL_ROOT)$(phpincludedir)/$$i) 2>/dev/null || true; \
			fi \
		done; \
	fi

PHP_TEST_SETTINGS = -d 'open_basedir=' -d 'output_buffering=0' -d 'memory_limit=-1'
PHP_TEST_SHARED_EXTENSIONS =  ` \
	if test "x$(PHP_MODULES)" != "x"; then \
		for i in $(PHP_MODULES)""; do \
			. $$i; $(top_srcdir)/build/shtool echo -n -- " -d extension=$$dlname"; \
		done; \
	fi; \
	if test "x$(PHP_ZEND_EX)" != "x"; then \
		for i in $(PHP_ZEND_EX)""; do \
			. $$i; $(top_srcdir)/build/shtool echo -n -- " -d $(ZEND_EXT_TYPE)=$(top_builddir)/modules/$$dlname"; \
		done; \
	fi`
PHP_DEPRECATED_DIRECTIVES_REGEX = '^(magic_quotes_(gpc|runtime|sybase)?|(zend_)?extension(_debug)?(_ts)?)[\t\ ]*='

test: all
	@if test ! -z "$(PHP_EXECUTABLE)" && test -x "$(PHP_EXECUTABLE)"; then \
		INI_FILE=`$(PHP_EXECUTABLE) -d 'display_errors=stderr' -r 'echo php_ini_loaded_file();' 2> /dev/null`; \
		if test "$$INI_FILE"; then \
			$(EGREP) -h -v $(PHP_DEPRECATED_DIRECTIVES_REGEX) "$$INI_FILE" > $(top_builddir)/tmp-php.ini; \
		else \
			echo > $(top_builddir)/tmp-php.ini; \
		fi; \
		INI_SCANNED_PATH=`$(PHP_EXECUTABLE) -d 'display_errors=stderr' -r '$$a = explode(",\n", trim(php_ini_scanned_files())); echo $$a[0];' 2> /dev/null`; \
		if test "$$INI_SCANNED_PATH"; then \
			INI_SCANNED_PATH=`$(top_srcdir)/build/shtool path -d $$INI_SCANNED_PATH`; \
			$(EGREP) -h -v $(PHP_DEPRECATED_DIRECTIVES_REGEX) "$$INI_SCANNED_PATH"/*.ini >> $(top_builddir)/tmp-php.ini; \
		fi; \
		TEST_PHP_EXECUTABLE=$(PHP_EXECUTABLE) \
		TEST_PHP_SRCDIR=$(top_srcdir) \
		CC="$(CC)" \
			$(PHP_EXECUTABLE) -n -c $(top_builddir)/tmp-php.ini $(PHP_TEST_SETTINGS) $(top_srcdir)/run-tests.php -n -c $(top_builddir)/tmp-php.ini -d extension_dir=$(top_builddir)/modules/ $(PHP_TEST_SHARED_EXTENSIONS) $(TESTS); \
		TEST_RESULT_EXIT_CODE=$$?; \
		rm $(top_builddir)/tmp-php.ini; \
		exit $$TEST_RESULT_EXIT_CODE; \
	else \
		echo "ERROR: Cannot run tests without CLI sapi."; \
	fi

clean:
	find . -name \*.gcno -o -name \*.gcda | xargs rm -f
	find . -name \*.lo -o -name \*.o | xargs rm -f
	find . -name \*.la -o -name \*.a | xargs rm -f 
	find . -name \*.so | xargs rm -f
	find . -name .libs -a -type d|xargs rm -rf
	rm -f libphp$(PHP_MAJOR_VERSION).la $(SAPI_CLI_PATH) $(SAPI_CGI_PATH) $(SAPI_MILTER_PATH) $(SAPI_LITESPEED_PATH) $(SAPI_FPM_PATH) $(OVERALL_TARGET) modules/* libs/*

distclean: clean
	rm -f Makefile config.cache config.log config.status Makefile.objects Makefile.fragments libtool main/php_config.h main/internal_functions_cli.c main/internal_functions.c stamp-h sapi/apache/libphp$(PHP_MAJOR_VERSION).module sapi/apache_hooks/libphp$(PHP_MAJOR_VERSION).module buildmk.stamp Zend/zend_dtrace_gen.h Zend/zend_dtrace_gen.h.bak Zend/zend_config.h TSRM/tsrm_config.h
	rm -f php5.spec main/build-defs.h scripts/phpize
	rm -f ext/date/lib/timelib_config.h ext/mbstring/oniguruma/config.h ext/mbstring/libmbfl/config.h ext/oci8/oci8_dtrace_gen.h ext/oci8/oci8_dtrace_gen.h.bak
	rm -f scripts/man1/phpize.1 scripts/php-config scripts/man1/php-config.1 sapi/cli/php.1 sapi/cgi/php-cgi.1 ext/phar/phar.1 ext/phar/phar.phar.1
	rm -f sapi/fpm/php-fpm.conf sapi/fpm/init.d.php-fpm sapi/fpm/php-fpm.service sapi/fpm/php-fpm.8 sapi/fpm/status.html
	rm -f ext/iconv/php_have_bsd_iconv.h ext/iconv/php_have_glibc_iconv.h ext/iconv/php_have_ibm_iconv.h ext/iconv/php_have_iconv.h ext/iconv/php_have_libiconv.h ext/iconv/php_iconv_aliased_libiconv.h ext/iconv/php_iconv_supports_errno.h ext/iconv/php_php_iconv_h_path.h ext/iconv/php_php_iconv_impl.h
	rm -f ext/phar/phar.phar ext/phar/phar.php
	if test "$(srcdir)" != "$(builddir)"; then \
	  rm -f ext/phar/phar/phar.inc; \
	fi
	$(EGREP) define'.*include/php' $(top_srcdir)/configure | $(SED) 's/.*>//'|xargs rm -f

.PHONY: all clean install distclean test
.NOEXPORT:
yaf.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf.c -o yaf.lo 
yaf_application.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_application.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_application.c -o yaf_application.lo 
yaf_bootstrap.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_bootstrap.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_bootstrap.c -o yaf_bootstrap.lo 
yaf_dispatcher.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_dispatcher.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_dispatcher.c -o yaf_dispatcher.lo 
yaf_exception.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_exception.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_exception.c -o yaf_exception.lo 
yaf_config.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_config.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_config.c -o yaf_config.lo 
configs/yaf_config_ini.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/configs/yaf_config_ini.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/configs/yaf_config_ini.c -o configs/yaf_config_ini.lo 
configs/yaf_config_simple.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/configs/yaf_config_simple.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/configs/yaf_config_simple.c -o configs/yaf_config_simple.lo 
yaf_request.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_request.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_request.c -o yaf_request.lo 
requests/yaf_request_http.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/requests/yaf_request_http.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/requests/yaf_request_http.c -o requests/yaf_request_http.lo 
requests/yaf_request_simple.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/requests/yaf_request_simple.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/requests/yaf_request_simple.c -o requests/yaf_request_simple.lo 
yaf_response.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_response.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_response.c -o yaf_response.lo 
responses/yaf_response_http.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/responses/yaf_response_http.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/responses/yaf_response_http.c -o responses/yaf_response_http.lo 
responses/yaf_response_cli.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/responses/yaf_response_cli.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/responses/yaf_response_cli.c -o responses/yaf_response_cli.lo 
yaf_view.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_view.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_view.c -o yaf_view.lo 
views/yaf_view_interface.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/views/yaf_view_interface.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/views/yaf_view_interface.c -o views/yaf_view_interface.lo 
views/yaf_view_simple.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/views/yaf_view_simple.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/views/yaf_view_simple.c -o views/yaf_view_simple.lo 
yaf_controller.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_controller.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_controller.c -o yaf_controller.lo 
yaf_action.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_action.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_action.c -o yaf_action.lo 
yaf_router.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_router.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_router.c -o yaf_router.lo 
routes/yaf_route_interface.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_interface.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_interface.c -o routes/yaf_route_interface.lo 
routes/yaf_route_static.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_static.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_static.c -o routes/yaf_route_static.lo 
routes/yaf_route_simple.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_simple.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_simple.c -o routes/yaf_route_simple.lo 
routes/yaf_route_supervar.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_supervar.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_supervar.c -o routes/yaf_route_supervar.lo 
routes/yaf_route_regex.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_regex.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_regex.c -o routes/yaf_route_regex.lo 
routes/yaf_route_rewrite.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_rewrite.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_rewrite.c -o routes/yaf_route_rewrite.lo 
routes/yaf_route_map.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_map.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/routes/yaf_route_map.c -o routes/yaf_route_map.lo 
yaf_loader.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_loader.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_loader.c -o yaf_loader.lo 
yaf_registry.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_registry.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_registry.c -o yaf_registry.lo 
yaf_plugin.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_plugin.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_plugin.c -o yaf_plugin.lo 
yaf_session.lo: /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_session.c
	$(LIBTOOL) --mode=compile $(CC)  -I. -I/home/ljp/www/motion/trunk/yaf-2.3.2 $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS)  -c /home/ljp/www/motion/trunk/yaf-2.3.2/yaf_session.c -o yaf_session.lo 
$(phplibdir)/yaf.la: ./yaf.la
	$(LIBTOOL) --mode=install cp ./yaf.la $(phplibdir)

./yaf.la: $(shared_objects_yaf) $(YAF_SHARED_DEPENDENCIES)
	$(LIBTOOL) --mode=link $(CC) $(COMMON_FLAGS) $(CFLAGS_CLEAN) $(EXTRA_CFLAGS) $(LDFLAGS) -o $@ -export-dynamic -avoid-version -prefer-pic -module -rpath $(phplibdir) $(EXTRA_LDFLAGS) $(shared_objects_yaf) $(YAF_SHARED_LIBADD)


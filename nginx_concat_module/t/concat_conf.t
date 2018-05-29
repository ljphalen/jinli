#!/usr/bin/perl
# Concat_configuration_Unit_Test
###############################################################################

use warnings;
use strict;

use File::Copy;
use File::Basename;
use Test::More;

BEGIN { use FindBin; chdir($FindBin::Bin); }

use lib 'lib';
use Test::Nginx qw/ :DEFAULT :gzip /;

###############################################################################

select STDERR; $| = 1;
select STDOUT; $| = 1;

my $t = Test::Nginx->new()->has(qw/http/)->plan(44);

my $d = $t->testdir();

mkdir("$d/concatFile");
$t->write_file('concatFile/index.html', 'index');
$t->write_file('concatFile/tindex.html', 'tIndex');
$t->write_file('concatFile/hello.x', 'hello.x');
$t->write_file('concatFile/world.x', 'world.x');
$t->write_file('concatFile/hello.js', 'hello.js');
$t->write_file('concatFile/world.js', 'world.js');
$t->write_file('concatFile/jack.js', 'jack.js');
$t->write_file('concatFile/hello.css', 'hello.css');
$t->write_file('concatFile/world.css', 'world.css');
$t->write_file('concatFile/jack.css', 'jack.css');
$t->write_file('concatFile/hello.html', 'hello.html');
$t->write_file('concatFile/world.html', 'world.html');
$t->write_file('concatFile/jack.html', 'jack.html');
$t->write_file('concatFile/world.htm', 'world.htm');
$t->write_file('concatFile/jack.shtml', 'jack.shtml');
$t->write_file('concatFile/hello.jpeg', 'hello.jpeg');
$t->write_file('concatFile/world.jpeg', 'world.jpeg');
$t->write_file('concatFile/jack.jpeg', 'jack.jpeg');
$t->write_file('concatFile/hello', 'hello');
$t->write_file('concatFile/world', 'world');
$t->write_file('concatFile/jack', 'jack');
$t->write_file('concatFile/t1.js', '1');
$t->write_file('concatFile/t2.js', '2');
$t->write_file('concatFile/t3.js', '3');
$t->write_file('concatFile/t4.js', '4');
$t->write_file('concatFile/t5.js', '5');
$t->write_file('concatFile/t6.js', '6');
$t->write_file('concatFile/t7.js', '7');
$t->write_file('concatFile/t8.js', '8');
$t->write_file('concatFile/t9.js', '9');
$t->write_file('concatFile/t10.js', '10');
$t->write_file('concatFile/t11.js', '11');
$t->write_file('concatFile/t12.js', '12');
$t->write_file('concatFile/t13.js', '13');
$t->write_file('concatFile/t14.js', '14');
$t->write_file('concatFile/t15.js', '15');

###############################################################################
#Test1
#concat on test
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
my $m;
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message1 = qr/hello.jsworld.js/s;

like(http_get('/concatFile/??hello.js,world.js'), $concat_message1, 'concat - concat on test');

$t->stop();
###############################################################################
###############################################################################
#Test2
#concat off test
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  off;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message2 = qr/index/s;

like(http_get('/concatFile/??hello.js,world.js'), $concat_message2, 'concat - concat off test');

$t->stop();
###############################################################################
###############################################################################
#Test3
#concat off and concat files 0 test
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  off;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message3 = qr/index/s;

like(http_get('/concatFile/'), $concat_message3, 'concat - concat off and concat files 0 test');

$t->stop();
###############################################################################
###############################################################################
#Test4
#concat on and concat files 0 test
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message4 = qr/index/s;

like(http_get('/concatFile/'), $concat_message4, 'concat - concat on and concat files 0 test');

$t->stop();
###############################################################################
###############################################################################
#Test5
#concat_unique on test -- not the same type
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message5 = qr/400/s;

like(http_get('/concatFile/??hello.js,world.css,jack.js'), $concat_message5, 'concat - concat_unique on test -- not the same type');

$t->stop();
###############################################################################
###############################################################################
#Test6
#concat_unique on test -- the same type
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message6 = qr/hello.jsworld.jsjack.js/s;

like(http_get('/concatFile/??hello.js,world.js,jack.js'), $concat_message6, 'concat - concat_unique on test -- the same type');

$t->stop();
###############################################################################
###############################################################################
#Test7
#concat_unique off test -- not the same type
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique off;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message7 = qr/hello.jsworld.cssjack.js/s;

like(http_get('/concatFile/??hello.js,world.css,jack.js'), $concat_message7, 'concat - concat_unique on test -- not the same type');

$t->stop();
###############################################################################
###############################################################################
#Test8
#concat_unique off test -- the same type
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique off;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message8 = qr/hello.jsworld.jsjack.js/s;

like(http_get('/concatFile/??hello.js,world.js,jack.js'), $concat_message8, 'concat - concat_unique on test -- the same type');

$t->stop();
###############################################################################
###############################################################################
#Test9
#concat_max_files 10 test -- 1 file
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message9 = qr/hello.js/s;

like(http_get('/concatFile/??hello.js'), $concat_message9, 'concat - concat_max_files 10 test -- 1 file');

$t->stop();
###############################################################################
###############################################################################
#Test10
#concat_max_files 10 test -- 5 file
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message10 = qr/hello.jsworld.jsjack.js12/s;

like(http_get('/concatFile/??hello.js,world.js,jack.js,t1.js,t2.js'), $concat_message10, 'concat - concat_max_files 10 test -- 5 file');

$t->stop();
###############################################################################
###############################################################################
#Test11
#concat_max_files 10 test -- 10 file
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message11 = qr/hello.jsworld.jsjack.js1234567/s;

like(http_get('/concatFile/??hello.js,world.js,jack.js,t1.js,t2.js,t3.js,t4.js,t5.js,t6.js,t7.js'), $concat_message11, 'concat - concat_max_files 10 test -- 10 file');

$t->stop();
###############################################################################
###############################################################################
#Test12
#concat_max_files 10 test -- 11 file
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

##########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message12 = qr/400/s;

like(http_get('/concatFile/??hello.js,world.js,jack.js,t1.js,t2.js,t3.js,t4.js,t5.js,t6.js,t7.js,t8.js'), $concat_message12, 'concat - concat_max_files 10 test -- 11 file');

$t->stop();
###############################################################################
###############################################################################
#Test13
#concat_max_files 10 test -- 0 file
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message13 = qr/index/s;

like(http_get('/concatFile/??'), $concat_message13, 'concat - concat_max_files 10 test -- 0 file');

$t->stop();
###############################################################################
###############################################################################
#Test14
#concat_types text/css, text/html test -- concat_unique on(fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
            concat_types text/css text/html;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message14 = qr/hello.htmlworld.htmljack.html/s;

like(http_get('/concatFile/??hello.html,world.html,jack.html'), $concat_message14, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique on(fix)');

$t->stop();
###############################################################################
###############################################################################
#Test15
#concat_types application/x-javascript, text/css, text/html test -- concat_unique on(not fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
            concat_types text/css text/html;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message15 = qr/400/s;

like(http_get('/concatFile/??hello.jpeg,world.jpeg,jack.jpeg'), $concat_message15, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique on(not fix)');

$t->stop();
###############################################################################
###############################################################################
#Test16
#concat_types application/x-javascript, text/css, text/html test -- concat_unique on(fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
            concat_types text/css text/html;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message16 = qr/hello.htmlworld.htmjack.shtml/s;

like(http_get('/concatFile/??hello.html,world.htm,jack.shtml'), $concat_message16, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique on(fix)');

$t->stop();
###############################################################################
###############################################################################
#Test17
#concat_types text/css, text/html test -- concat_unique on(fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
            concat_types text/css text/html;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message17 = qr/hello.cssworld.cssjack.css/s;

like(http_get('/concatFile/??hello.css,world.css,jack.css'), $concat_message17, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique on(fix)');

$t->stop();
###############################################################################
###############################################################################
#Test18
#concat_types text/css, text/html test -- concat_unique on(fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
            concat_types text/css text/html;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message18 = qr/hello.jsworld.jsjack.js/s;

like(http_get('/concatFile/??hello.js,world.js,jack.js'), $concat_message18, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique on(fix)');

$t->stop();
###############################################################################
###############################################################################
#Test19
#concat_types text/css, text/html test -- concat_unique off(not fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
            concat_types text/css text/html;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message19 = qr/400/s;

like(http_get('/concatFile/??hello.js,world.html,jack.css,hello.jpeg'), $concat_message19, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique off(not fix)');

$t->stop();
###############################################################################
###############################################################################
#Test20
#concat_types application/x-javascript, text/css, text/html test -- concat_unique off(fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
            concat_types text/css text/html;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message20 = qr/hello.htmlworld.htmljack.html/s;

like(http_get('/concatFile/??hello.html,world.html,jack.html'), $concat_message20, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique off(fix)');

$t->stop();
###############################################################################
###############################################################################
#Test21
#concat_types text/css, text/html test -- concat_unique off(not fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
            concat_types text/css text/html;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message21 = qr/400/s;

like(http_get('/concatFile/??hello.jpeg,world.jpeg,jack.jpeg'), $concat_message21, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique off(fix)');

$t->stop();
###############################################################################
###############################################################################
#Test22
#concat_types text/css, text/html test -- concat_unique off(not fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
            concat_types text/css text/html;
        }
    }
}

EOF

##########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message22 = qr/400/s;

like(http_get('/concatFile/??hello,world,jack'), $concat_message22, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique off(fix)');

$t->stop();
###############################################################################
###############################################################################
#Test23
#concat_types text/css, text/html test -- concat_unique off(fix)
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique off;
            concat_types text/css text/html;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message23 = qr/hello.jsworld.htmljack.css/s;

like(http_get('/concatFile/??hello.js,world.html,jack.css'), $concat_message23, 'concat - concat_types application/x-javascript, text/css, text/html test -- concat_unique off(fix)');

$t->stop();
###############################################################################
###############################################################################
#Test24
#concat on normal request index.html not combin
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message24 = qr/index/s;

like(http_get('/concatFile/index.html'), $concat_message24, 'concat - concat on normal request index.html not combin');

$t->stop();
###############################################################################
###############################################################################
#Test25
#concat on normal request tindex.html not combin
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message25 = qr/tIndex/s;

like(http_get('/concatFile/tindex.html'), $concat_message25, 'concat - concat on normal request tindex.html not combin');

$t->stop();
###############################################################################
###############################################################################
#Test26
#concat off normal request index.html not combin
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  off;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message26 = qr/index/s;

like(http_get('/concatFile/index.html'), $concat_message26, 'concat - concat off normal request index.html not combin');

$t->stop();
###############################################################################
###############################################################################
#Test27
#concat off normal request tindex.html not combin
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  off;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message27 = qr/tIndex/s;

like(http_get('/concatFile/tindex.html'), $concat_message27, 'concat - concat off normal request tindex.html not combin');

$t->stop();
###############################################################################
###############################################################################
#Test28
#concat_unique on normal request index.html not combin
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message28 = qr/index/s;

like(http_get('/concatFile/index.html'), $concat_message28, 'concat - concat_unique on normal request index.html not combin');

$t->stop();
###############################################################################
###############################################################################
#Test29
#concat_unique on normal request tindex.html not combin
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message29 = qr/tIndex/s;

like(http_get('/concatFile/tindex.html'), $concat_message29, 'concat - concat_unique on normal request tindex.html not combin');

$t->stop();
###############################################################################
###############################################################################
#Test30
#concat_unique off normal request index.html not combin
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique off;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message30 = qr/index/s;

like(http_get('/concatFile/index.html'), $concat_message30, 'concat - concat_unique off normal request index.html not combin');

$t->stop();
###############################################################################
###############################################################################
#Test31
#concat_unique off normal request tindex.html not combin
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_unique off;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message31 = qr/tIndex/s;

like(http_get('/concatFile/tindex.html'), $concat_message31, 'concat - concat_unique off normal request tindex.html not combin');

$t->stop();
###############################################################################
###############################################################################
#Test32
#concat_max_files: 1 out of the scale
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_max_files 1;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message32 = qr/400/s;

like(http_get('/concatFile/??hello.js,world.js'), $concat_message32, 'concat - concat_max_files: 1 out of the scale');

$t->stop();
###############################################################################
###############################################################################
#Test33
#concat_max_files: 1 in the scale
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_max_files 1;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message33 = qr/hello.js/s;

like(http_get('/concatFile/??hello.js'), $concat_message33, 'concat - concat_max_files: 1 in the scale');

$t->stop();
###############################################################################
###############################################################################
#Test34
#concat_max_files: 17 ; one file
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_max_files 17;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message34 = qr/hello.js/s;

like(http_get('/concatFile/??hello.js'), $concat_message34, 'concat - concat_max_files: 17 ; one file');

$t->stop();
###############################################################################
###############################################################################
#Test35
#concat_max_files: 17 ; 9 files
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_max_files 17;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message35 = qr/hello.jsworld.jsjack.js123456/s;

like(http_get('/concatFile/??hello.js,world.js,jack.js,t1.js,t2.js,t3.js,t4.js,t5.js,t6.js'), $concat_message35, 'concat - concat_max_files: 17 ; 9 files');

$t->stop();
###############################################################################
###############################################################################
#Test36
#concat_max_files: 17 ; 17 files
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_max_files 17;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message36 = qr/hello.jsworld.jsjack.js1234567891011121314/s;

like(http_get('/concatFile/??hello.js,world.js,jack.js,t1.js,t2.js,t3.js,t4.js,t5.js,t6.js,t7.js,t8.js,t9.js,t10.js,t11.js,t12.js,t13.js,t14.js'), $concat_message36, 'concat - concat_max_files: 17 ; 17 files');

$t->stop();
###############################################################################
###############################################################################
#Test37
#concat_max_files: 17 ; 18 files
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_max_files 17;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message37 = qr/400/s;

like(http_get('/concatFile/??hello.js,world.js,jack.js,t1.js,t2.js,t3.js,t4.js,t5.js,t6.js,t7.js,t8.js,t9.js,t10.js,t11.js,t12.js,t13.js,t14.js,t15.js'), $concat_message37, 'concat - concat_max_files: 17 ; 18 files');

$t->stop();
###############################################################################
###############################################################################
#Test38
#concat_max_files: 17 ; 0 files
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_max_files 17;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $concat_message38 = qr/index/s;

like(http_get('/concatFile/??'), $concat_message38, 'concat - concat_max_files: 17 ; 0 files');

$t->stop();
###############################################################################
###############################################################################
#Test39
#concat_max_files: 100 ; 100 files
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_max_files 1000;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $i;
my $url="/concatFile/??";
foreach $i(1..100){
	$url=$url."hello.js,";
}
my $tmp;
foreach $i(1..100){
	$tmp=$tmp."hello.js"
}


my $concat_message39 = qr/$tmp/s;

like(http_get($url), $concat_message39, 'concat - concat_max_files: 100 ; 100 files');

$t->stop();
###############################################################################
###############################################################################
#Test40
#concat_max_files: 100 ; 101 files
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
            concat_max_files 1000;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################
my $i2;
my $url2="/concatFile/??";
foreach $i2(1..101){
	$url2=$url2."hello.js,";
}
my $tmp2;
foreach $i2(1..101){
	$tmp2=$tmp2."hello.js"
}


my $concat_message40 = qr/$tmp2/s;

like(http_get($url2), $concat_message40, 'concat - concat_max_files: 100 ; 101 files');

$t->stop();
###############################################################################
###############################################################################
#Test41
#concat_types not inside the mime.types 
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################

my $concat_message41 = qr/400/s;

like(http_get('/concatFile/??hello.x,world.x'), $concat_message41, 'concat - concat_types not inside the mime.types');

$t->stop();
###############################################################################
###############################################################################
#Test42
#concat_unique as default test; same type input 
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################

my $concat_message42 = qr/hello.jsworld.js/s;

like(http_get('/concatFile/??hello.js,world.js'), $concat_message42, 'concat - concat_unique as default test; same type input ');

$t->stop();
###############################################################################
###############################################################################
#Test43
#concat_unique as default test; not the same type input 
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################

my $concat_message43 = qr/400/s;

like(http_get('/concatFile/??hello.js,world.css'), $concat_message43, 'concat - concat_unique as default test; not the same type input ');

$t->stop();
###############################################################################
###############################################################################
#Test44
#concat_unique as default test; not the same type input 
$t->write_file_expand('nginx.conf', <<'EOF');

%%TEST_GLOBALS%%

master_process off;
daemon         off;

events {
}

http {
    %%TEST_GLOBALS_HTTP%%
    
    include mime.types;
    default_type application/octet-stream;

    server {
        listen      127.0.0.1:8080;
        server_name localhost;

        location /concatFile/ {
            concat  on;
        }
    }
}

EOF

###########################################################
$m = dirname(dirname($ENV{TEST_NGINX_BINARY})) . '/conf/mime.types';
copy($m, $t->testdir()) or die 'copy mime.types failed: $!';

$t->run();
###############################################################################

my $concat_message44 = qr/hello.cssworld.css/s;

like(http_get('/concatFile/??hello.css,world.css'), $concat_message44, 'concat - concat_unique as default test; same type input ');

$t->stop();

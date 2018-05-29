#!/usr/bin/perl
# Concat_url_Unit_Test
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

my $t = Test::Nginx->new()->has(qw/http/)->plan(50);

my $d = $t->testdir();

mkdir("$d/concatFile");
$t->write_file('concatFile/hello.js', 'hello.js');
$t->write_file('concatFile/world.js', 'world.js');
$t->write_file('concatFile/jack.js', 'jack.js');

mkdir("$d/concatFile/dir1");
$t->write_file('concatFile/dir1/hello.js', 'hello.js_dir1');
$t->write_file('concatFile/dir1/world.js', 'world.js_dir1');
$t->write_file('concatFile/dir1/jack.js', 'jack.js_dir1');

###############################################################################
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
my $concat_message1 = qr/404/s;
like(http_get('/concatFile/hello.js,world.js'), $concat_message1, 'concat - concat_url test -- no question mark');

my $concat_message2 = qr/403/s;
like(http_get('/concatFile/?hello.js,world.js'), $concat_message2, 'concat - concat_url test -- one question mark front');

my $concat_message3 = qr/hello.js/s;
like(http_get('/concatFile/hello.js?world.js'), $concat_message3, 'concat - concat_url test -- one question mark middle');

my $concat_message4 = qr/404/s;
like(http_get('/concatFile/hello.js,world.js?'), $concat_message4, 'concat - concat_url test -- one question mark back');

my $concat_message5 = qr/hello.js/s;
like(http_get('/concatFile/hello.js??world.js'), $concat_message5, 'concat - concat_url test -- two question mark middle');

my $concat_message6 = qr/404/s;
like(http_get('/concatFile/hello.js,world.js??'), $concat_message6, 'concat - concat_url test -- two question mark back');

my $concat_message7 = qr/hello.js/s;
like(http_get('/concatFile/hello.js?world.js?'), $concat_message7, 'concat - concat_url test -- two question mark seprate');

my $concat_message8 = qr/403/s;
like(http_get('/concatFile/?hello.js?world.js'), $concat_message8, 'concat - concat_url test -- two question mark seprate');

my $concat_message9 = qr/403/s;
like(http_get('/concatFile/?hello.js,world.js?'), $concat_message9, 'concat - concat_url test -- two question mark seprate');

my $concat_message10 = qr/400/s;
like(http_get('/concatFile/???hello.js,world.js'), $concat_message10, 'concat - concat_url test -- three question mark front');

my $concat_message11 = qr/404/s;
like(http_get('/concatFile/hello.js,world.js???'), $concat_message11, 'concat - concat_url test -- three question mark back');

my $concat_message12 = qr/hello.js/s;
like(http_get('/concatFile/hello.js???world.js'), $concat_message12, 'concat - concat_url test -- three question mark middle');

my $concat_message13 = qr/403/s;
like(http_get('/concatFile/?hello.js??world.js'), $concat_message13, 'concat - concat_url test -- three question mark seprate middle');

my $concat_message14 = qr/403/s;
like(http_get('/concatFile/?hello.js?world.js?'), $concat_message14, 'concat - concat_url test -- three question mark seprate');

my $concat_message15 = qr/hello.js/s;
like(http_get('/concatFile/??hello.js?world.js'), $concat_message15, 'concat - concat_url test -- three question mark seprate');

my $concat_message16 = qr/hello.jsworld.js/s;
like(http_get('/concatFile/??hello.js,world.js?'), $concat_message16, 'concat - concat_url test -- three question mark seprate');

my $concat_message17 = qr/hello.js/s;
like(http_get('/concatFile/hello.js??world.js?'), $concat_message17, 'concat - concat_url test -- three question mark seprate');

my $concat_message18 = qr/403/s;
like(http_get('/concatFile/?hello.js,world.js??'), $concat_message18, 'concat - concat_url test -- three question mark seprate');

my $concat_message19 = qr/hello.js/s;
like(http_get('/concatFile/hello.js?world.js??'), $concat_message19, 'concat - concat_url test -- three question mark seprate');

my $concat_message20 = qr/hello.js/s;
like(http_get('/concatFile/??hello.js??world.js'), $concat_message20, 'concat - concat_url test -- four question mark');

my $concat_message21 = qr/hello.js/s;
like(http_get('/concatFile/hello.js??world.js??'), $concat_message21, 'concat - concat_url test -- four question mark');

my $concat_message22 = qr/hello.jsworld.js/s;
like(http_get('/concatFile/??hello.js,world.js??'), $concat_message22, 'concat - concat_url test -- four question mark');

my $concat_message23 = qr/400/s;
like(http_get('/concatFile/??,hello.js,world.js'), $concat_message23, 'concat - concat_url test -- one more comma front');

my $concat_message24 = qr/400/s;
like(http_get('/concatFile/??,,hello.js,world.js'), $concat_message24, 'concat - concat_url test -- two more commas front');

my $concat_message25 = qr/hello.jsworld.js/s;
like(http_get('/concatFile/??hello.js,world.js,'), $concat_message25, 'concat - concat_url test -- one more comma back');

my $concat_message26 = qr/400/s;
like(http_get('/concatFile/??hello.js,world.js,,'), $concat_message26, 'concat - concat_url test -- two more commas back');

my $concat_message27 = qr/400/s;
like(http_get('/concatFile/??hello.js,world.js,,,'), $concat_message27, 'concat - concat_url test -- three more commas back');

my $concat_message28 = qr/400/s;
like(http_get('/concatFile/??hello.js,,world.js'), $concat_message28, 'concat - concat_url test -- one more comma middle');

my $concat_message29 = qr/400/s;
like(http_get('/concatFile/??hello.js,,,world.js'), $concat_message29, 'concat - concat_url test -- two more commas middle');

my $concat_message30 = qr/hello.jsworld.js_dir1jack.js/s;
like(http_get('/concatFile/??hello.js,dir1/world.js,jack.js'), $concat_message30, 'concat - concat_url test -- with one directory middle');

my $concat_message31 = qr/hello.js_dir1world.jsjack.js/s;
like(http_get('/concatFile/??dir1/hello.js,world.js,jack.js'), $concat_message31, 'concat - concat_url test -- with one directory front');

my $concat_message32 = qr/hello.jsworld.jsjack.js_dir1/s;
like(http_get('/concatFile/??hello.js,world.js,dir1/jack.js'), $concat_message32, 'concat - concat_url test -- with one directory back');

my $concat_message33 = qr/hello.js_dir1world.js_dir1jack.js/s;
like(http_get('/concatFile/??dir1/hello.js,dir1/world.js,jack.js'), $concat_message33, 'concat - concat_url test -- with two directory AAB');

my $concat_message34 = qr/hello.js_dir1world.jsjack.js_dir1/s;
like(http_get('/concatFile/??dir1/hello.js,world.js,dir1/jack.js'), $concat_message34, 'concat - concat_url test -- with two directory ABA');

my $concat_message35 = qr/hello.jsworld.js_dir1jack.js_dir1/s;
like(http_get('/concatFile/??hello.js,dir1/world.js,dir1/jack.js'), $concat_message35, 'concat - concat_url test -- with two directory BAA');

my $concat_message36 = qr/hello.jsworld.js_dir1jack.js/s;
like(http_get('/concatFile/??hello.js,/dir1/world.js,jack.js'), $concat_message36, 'concat - concat_url test -- with one directory strarts with slash middle');

my $concat_message37 = qr/hello.js_dir1world.jsjack.js/s;
like(http_get('/concatFile/??/dir1/hello.js,world.js,jack.js'), $concat_message37, 'concat - concat_url test -- with one directory strarts with slash front');

my $concat_message38 = qr/hello.jsworld.jsjack.js_dir1/s;
like(http_get('/concatFile/??hello.js,world.js,/dir1/jack.js'), $concat_message38, 'concat - concat_url test -- with one directory strarts with slash back');

my $concat_message39 = qr/hello.js_dir1world.js_dir1jack.js/s;
like(http_get('/concatFile/??/dir1/hello.js,/dir1/world.js,jack.js'), $concat_message39, 'concat - concat_url test -- with two directory strarts with slash AAB');

my $concat_message40 = qr/hello.js_dir1world.jsjack.js_dir1/s;
like(http_get('/concatFile/??/dir1/hello.js,world.js,/dir1/jack.js'), $concat_message40, 'concat - concat_url test -- with two directory strarts with slash ABA');

my $concat_message41 = qr/hello.jsworld.js_dir1jack.js_dir1/s;
like(http_get('/concatFile/??hello.js,/dir1/world.js,/dir1/jack.js'), $concat_message41, 'concat - concat_url test -- with two directory strarts with slash BAA');

my $concat_message42 = qr/hello.js_dir1world.js_dir1jack.js/s;
like(http_get('/concatFile/??/dir1/hello.js,dir1/world.js,jack.js'), $concat_message42, 'concat - concat_url test -- with two directory and one of it strarts with slash ABC');

my $concat_message43 = qr/hello.js_dir1world.jsjack.js_dir1/s;
like(http_get('/concatFile/??/dir1/hello.js,world.js,dir1/jack.js'), $concat_message43, 'concat - concat_url test -- with two directory and one of it strarts with slash ACB');

my $concat_message44 = qr/hello.jsworld.js_dir1jack.js_dir1/s;
like(http_get('/concatFile/??hello.js,dir1/world.js,/dir1/jack.js'), $concat_message44, 'concat - concat_url test -- with two directory and one of it strarts with slash CBA');

my $concat_message45 = qr/400/s;
like(http_get('/concatFile/??hello.js,../world.js,jack.js'), $concat_message45, 'concat - concat_url test -- bad request(../)');

my $concat_message46 = qr/hello.jsworld.jsjack.js/s;
like(http_get('/concatFile/??hello.js,./world.js,jack.js'), $concat_message46, 'concat - concat_url test -- dot slash(./)');

my $concat_message47 = qr/400/s;
like(http_get('/concatFile/??hello.js,./../world.js,jack.js'), $concat_message47, 'concat - concat_url test -- bad request(./../)');

my $concat_message48 = qr/400/s;
like(http_get('/concatFile/hello./??js,world.js,jack.js'), $concat_message48, 'concat - two question marks ./??js');

my $concat_message49 = qr/400/s;
like(http_get('/concatFile//??hello.js,world.js,jack.js,?'), $concat_message49, 'concat - in the end have a commas and question mark ,?');

my $concat_message50 = qr/404/s;
like(http_get('/concatFile//??hello.js,world.js,jack.js,/'), $concat_message50, 'concat - in the end have a commas and / ,/');



$t->stop();
###############################################################################

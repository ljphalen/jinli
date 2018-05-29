#!/usr/bin/perl
# Concat_result_Unit_Test
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

my $t = Test::Nginx->new()->has(qw/http/)->plan(6);

my $d = $t->testdir();

mkdir("$d/concatFile");
$t->write_file('concatFile/hello.js', 'hello.js');
$t->write_file('concatFile/world.js', 'world.js');
$t->write_file('concatFile/jack.js', 'jack.js');

$t->write_file('concatFile/empty.js', '');
$t->write_file('concatFile/chinese.js', 'ÄãºÃÂð£¿');

my $i;
my $largeFile="a";

foreach $i(1..102400){
	$largeFile = $largeFile."a";
}

$t->write_file('concatFile/LargeFile.js', $largeFile);

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
my $concat_message1 = qr/hello.jsworld.js/s;
like(http_get('/concatFile/??hello.js,world.js,empty.js'), $concat_message1, 'concat - concat result test -- empty file');

my $concat_message2 = qr/404/s;
like(http_get('/concatFile/??hello.js,helloworld.js,world.js'), $concat_message2, 'concat - concat result test -- no eixt file middle');

my $concat_message3 = qr/404/s;
like(http_get('/concatFile/??helloworld.js,hello.js,world.js'), $concat_message3, 'concat - concat result test -- no eixt file front');

my $concat_message4 = qr/404/s;
like(http_get('/concatFile/??hello.js,world.js,helloworld.js'), $concat_message4, 'concat - concat result test -- no eixt file back');

my $concat_message5 = qr/hello.jsworld.jsÄãºÃÂð£¿/s;
like(http_get('/concatFile/??hello.js,world.js,chinese.js'), $concat_message5, 'concat - concat result test -- include Chinese words');

my $tmp = "hello.jsworld.js".$largeFile;
my $concat_message6 = qr/$tmp/s;
like(http_get('/concatFile/??hello.js,world.js,LargeFile.js'), $concat_message6, 'concat - concat result test -- include 100k file');


$t->stop();
###############################################################################









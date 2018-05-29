#!/bin/sh

#keytool依赖
type keytool >/dev/null 2>&1 || { echo >&2 "keytool required.  Aborting."; exit 1; }

randnum=$(date +%sN);

if [ -z "$1" ];then
    echo "Usage: `basename $0` file.apk"
    exit 0
elif [ -e "$1" ];then
    result=`unzip -o $1 "META-INF/*.*SA" -d $randnum | grep inflating | grep SA | awk -F ": " '{print $2}'`
else
    echo "failed:$1 not exists"
    exit 1
fi

#未找到签名文件
if [ ! -n "$result" ]; then
    rm -rf $randnum
    echo "$1 failed::::unsigned"
    exit 1
fi

for x in $result
do
    #获取指纹
    if [ -e $x ];then
        md5=`keytool -printcert -file $x | grep "MD5\|SHA1"`
        echo "$1::::$x::::success:\n$md5"
    else
        echo "$1::::$x::::failed:RSA not exists"
    fi
done

rm -rf $randnum
exit 0
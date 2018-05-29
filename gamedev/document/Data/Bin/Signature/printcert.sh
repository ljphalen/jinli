#!/bin/sh

#keytool依赖
type keytool >/dev/null 2>&1 || { echo >&2 "keytool required.  Aborting."; exit 1; }

randnum=$(date +%s);

if [ -z "$1" ];then
    echo "Usage: `basename $0` file.apk"
    exit 0
elif [ -e "$1" ];then
    result=`unzip -o $1 "META-INF/*.RSA" -d $randnum | grep inflating | grep RSA | awk -F ": " '{print $2}'`
else
    echo "failed:$1 not exists"
    exit 1
fi

#未发现签名文件，文件未经过签名，或签名机制不规范，老版本的包可能有不同的签名方式
if [ ! $result ]; then
	rm -rf $randnum
	echo "failed:unsigned"
	exit 1
fi

#获取指纹
echo $result;
if [ -e $result ];then 
	result=`keytool -printcert -file $result | grep "MD5\|SHA1"`
	echo "success:\n$result"
else
	echo "failed:RSA not exists"
fi

#删除临时文件
rm -rf $randnum

exit 0
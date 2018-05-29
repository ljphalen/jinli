FILE=$1
P=${FILE/./\/}

rm -rf *.tar.gz

if test -z $P;then
	echo "Usage: sh package.sh dirname\n"
	exit 1
fi

SRC=http://19.9.0.130/svn/motion/online/UED/repos/apps/$P
DIST=$PWD/assets/$FILE
PS=$PWD/rsync.ps
EX=$PWD/rsync.exclude

svn info $SRC
error=$?

if [ $error -ne 0 ]; then
        echo "\nCould not find svn path : "$SRC" \n";
        exit 1
fi

echo "确认打包游戏项目二级子目录的静态文件已经合并完成? (y/n)"
read RESP
if [ "$RESP" = "y" ]; then
    echo ""
else
    echo "请配合前端开发合并完成后再打包.\n"
    exit 1
fi


mkdir -p $PWD/assets
rm -rf $PWD/assets/*

echo "start export files...."
sleep 1 
svn export $SRC $DIST --force

echo "start tar files...."
sleep 1 

cd $PWD/assets
tar -czvf assets.$FILE.tar.gz $FILE/
mv *.tar.gz ../

cd ../ 
chmod 755 *.tar.gz
exit 1


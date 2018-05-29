P=$1

rm -rf *.tar.gz

if test -z $P;then
	echo "Usage: sh package.sh dirname\n"
	exit 1
fi

SRC=http://19.9.0.130/svn/motion/online/$P
DIST=$PWD/packages/$P
PS=$PWD/rsync.ps
EX=$PWD/rsync.exclude

svn info $SRC
error=$?

if [ $error -ne 0 ]; then
        echo "\nCould not find svn path : "$SRC" \n";
        exit 1
fi

echo "\033[;33;33mQ1. 确认静态文件已经合并完成? (y/n)\033[0m"
read RESP
if [ "$RESP" = "y" ]; then
    echo ""
else
    echo "\033[0;33;33m请配合前端开发合并完成后再打包.\033[0m\n"
    exit 1
fi


echo "\033[;33;33mQ2. 确认代码合完成且已经commit到online? (y/n)\033[0m"
read RESP
if [ "$RESP" = "y" ]; then
    echo ""
else
    echo "\033[0;33;33m请使用svn st 检查代码合并是否完成且已经commit到online\033[0m\n"
    exit 1
fi

mkdir -p $PWD/packages
rm -rf $PWD/packages/*

echo "start export files...."
sleep 1 
svn export $SRC $DIST --force
svn export http://19.9.0.130/svn/motion/online/UED/repos/sys $PWD/packages/sys --force
svn export http://19.9.0.130/svn/motion/online/UED/repos/apps/admin $PWD/packages/admin --force
svn export http://19.9.0.130/svn/motion/online/UED/repos/apps/$P $PWD/packages/assets.$P --force 
svn export http://19.9.0.130/svn/motion/online/UED/repos/apps/admin $PWD/packages/assets.admin --force

echo "start tar files...."
sleep 1 

cd $PWD/packages
rm -rf $P/data/attachs/*
tar -czvf $P.tar.gz $P/
tar -czvf assets.sys.tar.gz sys/
tar -czvf assets.admin.tar.gz admin/
tar -czvf assets.$P.tar.gz assets.$P/
tar -czvf assets.admin.tar.gz assets.admin/
mv *.tar.gz ../

cd ../ 
chmod 755 *.tar.gz
exit 1


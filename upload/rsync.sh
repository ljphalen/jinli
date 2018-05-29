P=$1

chmod 600 $PWD/rsync.ps

if test -z $P;then
	echo "Usage: sh rsync.sh [u3g|gou|3g|game|theme|lock|miigou|dhm|...]\n"
	exit 1
fi

if [ "$P" == "u3g" ];then
    SRC=http://19.9.0.130/svn/motion/branches/UED20140505/repos/apps/3g
    DIST=$PWD/files/UED/repos/apps/3g
elif [ "$P" == "udemo" ]
then
    SRC=http://19.9.0.130/svn/motion/branches/UED20140505/demo/3g
    DIST=$PWD/files/UED/demo/3g
else
    SRC=http://19.9.0.130/svn/motion/trunk/$P
    DIST=$PWD/files/$P
fi
PS=$PWD/rsync.ps
EX=$PWD/rsync.exclude

svn info $SRC
error=$?

if [ $error -ne 0 ]; then
	echo "\nCould not find svn path : "$SRC" \n";
	exit 1
fi

mkdir -p $PWD/files
if [ -d $DIST ]; then
	svn cleanup $DIST 
fi

echo "start update files...."
sleep 1 
svn co $SRC $DIST --force
#find $DIST/public -name *.php |xargs sed -i "s/develop/test/g"
echo "start rsync files...."
sleep 1
if [ "$P" == "u3g" -o "$P" == "udemo" ];then
    DIST=$PWD/files/UED
    rsync -avz --password-file=$PS --exclude-from=$EX $DIST gou3g@42.121.237.23::www
else 
    rsync -avz --password-file=$PS --exclude-from=$EX $DIST gou3g@42.121.237.23::www
fi
exit 1

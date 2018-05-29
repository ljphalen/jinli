P=$1


if test -z $P;then
        echo "Usage: sh rsync.sh [gou|3g|game|theme|lock...]\n"
        exit 1
fi


SRC=http://192.168.110.97/svn/motion/online/$P
DIST=$PWD/stable/$P
PS=$PWD/stable.ps
EX=$PWD/rsync.exclude

chmod 600 $PS 

echo $PS

svn info $SRC
error=$?

if [ $error -ne 0 ]; then
        echo "\nCould not find svn path : "$SRC" \n";
        exit 1
fi

mkdir -p $PWD/stable
if [ -d $PWD/stable/$P ]; then
        svn cleanup $PWD/stable/$P
fi

echo "start update files...."
sleep 1
svn co $SRC $DIST --force
#find $DIST/public -name *.php |xargs sed -i "s/develop/test/g"
echo "start rsync files...."
sleep 1

rsync -avz --password-file=$PS --exclude-from=$EX $DIST root@42.121.56.101::www
exit 1

#!/bin/bash


initialize() {
    PROJECT=$1
    if [ -n "$2" ]; then
        SRC_REPO=$2
    else
        echo "Usage: ./rsync <project_name> <svn_repo_url>"
    	echo "<project_name>:    项目名称, 如 mobgi_backend, mobgi_api, 对应测试站的项目目录名"
   	echo "<svn_repo_url>:  可选参数, 指定需要同步的代码svn仓库全路径"
   	echo "例如:"
    	echo "./rsync mobgi_api http://svn.ids111.com:81/dgc/mobgi_backend/branches/mobgi_backend_rock"
    	exit 1
    fi
    DIST_PATH=$PWD/files/$PROJECT
    PASSWORD=$PWD/rsync.pw
    EXCLUDE_FILES=$PWD/rsync.exclude

    chmod 600 $PWD/rsync.pw
}

checkEnv() {
    svn info $SRC_REPO
    error=$?

    if [ $error -ne 0 ]; then
        echo "Could not find svn path : "$SRC_REPO"";
        exit 1
    fi

    mkdir -p $PWD/files
    rm -rf $DIST_PATH
}

checkoutSrc() {
    echo "start update files...."
    sleep 1
    svn co $SRC_REPO $DIST_PATH --force
}

rsyncFiles() {
    echo "start rsync files...."
    sleep 1
    rsync -avz --password-file=$PASSWORD --exclude-from=$EXCLUDE_FILES $DIST_PATH ljp@192.168.119.63::rsync
    exit 1
}





#------------------------------------------------------------
#------------------------- Main Entry ------------------------
#------------------------------------------------------------
if [ -n "$1" ]; then
    initialize $1 $2
    checkEnv
    checkoutSrc
    rsyncFiles
else
    echo "Usage: ./rsync <project_name> <svn_repo_url>"
    echo "<project_name>:    项目名称, 如 mobgi_backend, mobgi_api, 对应测试站的项目目录名"
    echo "<svn_repo_url>:  可选参数, 指定需要同步的代码svn仓库全路径"
    echo "例如:"
    echo "./rsync mobgi_api http://svn.ids111.com:81/dgc/mobgi_backend/branches/mobgi_backend_rock"
    exit 1
fi

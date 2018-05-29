#!/bin/bash

TAR_FILE_NAME=
SRC_REPO=
DIST_PATH=

initialize() {
    srcPath=$1
    TAR_FILE_NAME=${srcPath//\//.}
    SRC_REPO=http://19.9.0.130/svn/motion/online/UED/repos/apps/$srcPath
    DIST_PATH=$PWD/assets/$srcPath
}

checkEnv() {
    svn info $SRC_REPO
    error=$?

    if [ $error -ne 0 ]; then
        echo "Could not find svn path : $SRC_REPO";
        exit 1
    fi
}

checkProcess() {
    echo -e "\033[01;33mQ1. 确认打包游戏项目二级子目录的静态文件已经合并完成?  (y/n)\033[00m"
    read RESP
    if [ "$RESP" != "y" ]; then
        echo -e "\033[01;33m请配合前端开发合并完成后再打包\033[00m"
        exit 1
    fi
}

checkoutSrc() {
    mkdir -p $PWD/assets
    rm -rf $PWD/assets/*

    echo "start export files...."
    sleep 1
    svn export $SRC_REPO $DIST_PATH --force
}

tarPackages() {
    echo "start tar files...."
    sleep 1

    rm -rf *.tar.gz
    cd $PWD/assets
    tar -czvf assets.$TAR_FILE_NAME.tar.gz $DIST_PATH/
    mv *.tar.gz ../

    cd ../
    chmod 755 *.tar.gz
    exit 1
}

#------------------------------------------------------------
#------------------------- Main Entry ------------------------
#------------------------------------------------------------
if [ -n "$1" ]; then
    initialize $1
    checkEnv
    checkProcess
    checkoutSrc
    tarPackages
else
    echo "Usage: 打包指定路径下的静态资源 ./gameassets.sh <asset_path>"
    echo "<asset_path>:    path of asset resource, such as game/web"
    exit 1
fi

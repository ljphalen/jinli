#!/bin/bash

PROJECT=
SRC_REPO=
DIST_PATH=

initialize() {
    PROJECT=$1
    if [ -n "$2" ]; then
        SRC_REPO=$2
    else
    	SRC_REPO=http://19.9.0.130/svn/motion/online/gamehall/$PROJECT
    fi
    DIST_PATH=$PWD/packages/$PROJECT
}

checkEnv() {
    svn info $SRC_REPO
    error=$?

    if [ $error -ne 0 ]; then
        echo "Could not find svn path : "$SRC_REPO"";
        exit 1
    fi

}

checkProcess() {
    echo -e "\033[01;33mQ1. 确认静态文件已经合并完成? (y/n)\033[00m"
    read RESP
    if [ "$RESP" != "y" ]; then
        echo -e "\033[01;33m请配合前端开发合并完成后再打包.\033[00m"
        exit 1
    fi

    echo -e "\033[01;33mQ2. 确认代码合完成且已经commit到online? (y/n)\033[00m"
    read RESP
    if [ "$RESP" != "y" ]; then
        echo -e "\033[01;33m请使用svn st 检查代码合并是否完成且已经commit到online\033[00m"
        exit 1
    fi
}

checkoutSrc() {
    echo "start export files...."
    sleep 1
    mkdir -p $PWD/packages
    rm -rf $PWD/packages/*
    svn export $SRC_REPO $DIST_PATH --force
    svn export http://19.9.0.130/svn/motion/online/UED/repos/sys $PWD/packages/sys --force
    svn export http://19.9.0.130/svn/motion/online/UED/repos/apps/admin $PWD/packages/admin --force
    svn export http://19.9.0.130/svn/motion/online/UED/repos/apps/$PROJECT $PWD/packages/assets.$PROJECT --force
}

tarPackages() {
    echo "start tar files...."
    sleep 1

    rm -rf *.tar.gz
    cd $PWD/packages
    rm -rf $PROJECT/data/attachs/*
    rm -rf $PROJECT/docs/*
    tar -czvf $PROJECT.tar.gz $PROJECT/
    tar -czvf assets.sys.tar.gz sys/
    tar -czvf assets.admin.tar.gz admin/
    tar -czvf assets.$PROJECT.tar.gz assets.$PROJECT/
    mv *.tar.gz ../

    cd ../
    chmod 755 *.tar.gz
    exit 1
}

#------------------------------------------------------------
#------------------------- Main Entry ------------------------
#------------------------------------------------------------
if [ -n "$1" ]; then
    initialize $1 $2
    checkEnv
    checkProcess
    checkoutSrc
    tarPackages
else
    echo "Usage: ./package.sh <project_name> [<svn_repo_url>]"
    echo "<project_name>:    项目名称, 如 game, gameweixin"
    echo "[<svn_repo_url>]:  可选参数, 指定需要打包的代码svn仓库全路径"
    echo "例如:"
    echo "./package.sh game"
    echo "./package.sh game http://19.9.0.130/svn/motion/branches/game_v1.5.7_release/"
    exit 1
fi

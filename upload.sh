#!/bin/sh
DIR=$(dirname $(readlink -f $0))

TARGET=$DIR/../UploadVeryGrabber_tmp

mkdir $TARGET

cd $TARGET

git clone -b master --single-branch --depth 1 file://$DIR

tar czf verygrabber.tar.gz VeryGrabber

scp verygrabber.tar.gz idealo@85.199.141.230:
ssh idealo@85.199.141.230 'tar xzf verygrabber.tar.gz'

rm -rf $TARGET

#!/usr/bin/env bash

# Builds the zip file that can be uploaded to Opencart

BASEDIR=$(dirname "$0")
STARTDIR=$(pwd)
ZIPNAME="opencart-3.x-paysera-1.6.ocmod.zip"
TEMPDIR="tmp"
UPLOADDIR="upload"

cd "$BASEDIR"
rm -rf $ZIPNAME
mkdir -p $TEMPDIR/$UPLOADDIR
cp -R src/* $TEMPDIR/$UPLOADDIR
cp -R README.md $TEMPDIR
cp -R Readme.txt $TEMPDIR
mkdir -p $TEMPDIR/$UPLOADDIR/system/library
cp vendor/webtopay/libwebtopay/WebToPay.php $TEMPDIR/$UPLOADDIR/system/library
rm -rf $TEMPDIR/$UPLOADDIR/vendor
cd "$BASEDIR/$TEMPDIR"
zip -q -r "../$ZIPNAME" ./*
cd "$BASEDIR"
rm -rf "$STARTDIR/$TEMPDIR"
cd "$STARTDIR"
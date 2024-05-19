#!/bin/sh

set -e

export MWD="${PWD}"
export BOOTSTRAP="https://github.com/twbs/bootstrap/releases/download/v5.3.3/bootstrap-5.3.3-dist.zip"
export TEMPD="$(mktemp -d)"

if [ ! -d "${MWD}/yardimcilar" ] ; then
	mkdir -p "${MWD}/yardimcilar" "${MWD}/yardimcilar/css" "${MWD}/yardimcilar/js"
fi

(
	cd "${TEMPD}"

	export status="true" BSZIP="${BOOTSTRAP##*/}"
	export CWD="${PWD}"

	wget "${BOOTSTRAP}" && {
		mkdir -p "${MWD}/yardimcilar/css" "${MWD}/yardimcilar/js"
		unzip "${BSZIP}" # Sıkıştırılmış haldeki Bootstrap dosyasını bulunduğumuz dizine çıkar.
		cp -v "${BSZIP%.*}/css/bootstrap.min.css" "${MWD}/yardimcilar/css"	# Bootstrap css dosyasını al.
		cp -v "${BSZIP%.*}/js/bootstrap.min.js" "${MWD}/yardimcilar/js"		# Bootstrap js dosyasını al.
	} || {
		export status="false"
	}

	rm -rvf "${CWD}"

	if ! "${status}" ; then
		exit 1
	fi
)

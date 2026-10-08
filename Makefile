SHELL := /bin/bash
APPNAME := ureport-client
VERSION := $(shell cat VERSION | tr -d "[:space:]")
COMMIT := $(shell git rev-parse --short HEAD)

REQS := sassc msgfmt
K := $(foreach r, ${REQS}, $(if $(shell command -v ${r} 2> /dev/null), '', $(error "${r} not installed")))

LANGUAGES := $(wildcard language/*/LC_MESSAGES)
JAVASCRIPT := $(shell find public -name '*.js' ! -name '*-*.js')

default: test clean compile package

clean:
	rm -Rf build/${APPNAME}*
	for f in $(shell find public/css -name '*-*.css*'); do rm $$f; done
	for f in $(shell find public/js  -name '*-*.js'  ); do rm $$f; done

compile:
	cd public/css && sassc -t compact -m screen.scss screen-${VERSION}.css
	for f in $(LANGUAGES); do \
		msgfmt -cv $$f/errors.po -o $$f/errors.mo; \
		msgfmt -cv $$f/labels.po -o $$f/labels.mo; \
		msgfmt -cv $$f/messages.po -o $$f/messages.mo; \
	done
	for f in ${JAVASCRIPT}; do cp $$f $${f%.js}-${VERSION}.js; done


test:
	vendor/bin/phpstan analyse -l 5

package:
	[[ -d build ]] || mkdir build
	rsync -rl --exclude-from=buildignore . build/${APPNAME}
	cd build && tar czf ${APPNAME}-${VERSION}.tar.gz ${APPNAME}

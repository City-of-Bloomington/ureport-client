include make.conf
# Variables from make.conf:
#
# DOCKER_REPO
SHELL := /bin/bash
APPNAME := ureport-client
VERSION := $(shell cat VERSION | tr -d "[:space:]")
COMMIT := $(shell git rev-parse --short HEAD)

REQS := sassc msgfmt
K := $(foreach r, ${REQS}, $(if $(shell command -v ${r} 2> /dev/null), '', $(error "${r} not installed")))

LANGUAGES := $(wildcard language/*/LC_MESSAGES)
JAVASCRIPT := $(shell find public -name '*.js' ! -name '*-*.js')
SASS := $(shell find -name screen.scss -not -path '*/build/*')
CSS := $(patsubst %.scss, %-$(VERSION).css, $(SASS))


default: clean compile package

clean:
	rm -Rf build/${APPNAME}*
	for f in $(shell find public/css -name '*-*.css'   ); do rm $$f; done

compile: $(CSS)
	cd $(LANGUAGES) && msgfmt -cv *.po
	for f in ${JAVASCRIPT}; do cp $$f $${f%.js}-${VERSION}.js; done

$(CSS): $(SASS)
	cd $(@D) && sassc -t compact -m screen.scss screen-${VERSION}.css

test:
	vendor/phpunit/phpunit/phpunit -c src/Test/Unit.xml

package:
	[[ -d build ]] || mkdir build
	rsync -rl --exclude-from=buildignore . build/${APPNAME}
	cd build && tar czf ${APPNAME}-${VERSION}.tar.gz ${APPNAME}

dockerfile:
	docker build build/blossom -t ${DOCKER_REPO}/${APPNAME}:${VERSION}-${COMMIT}
	docker push ${DOCKER_REPO}/${APPNAME}:${VERSION}-${COMMIT}

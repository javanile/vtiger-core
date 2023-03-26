
.PHONY: build

build:
	bash build.sh

update:
	bash contrib/update.sh $(tag)


## =====
## Tests
## =====

test-blank-branch:
	@bash tests/blank-branch-test.sh
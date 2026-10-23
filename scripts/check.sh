#!/usr/bin/env sh
set -eu
python -m json.tool project-map.json >/dev/null
python -m compileall -q ecosystem tests
python -m unittest discover -s tests -v

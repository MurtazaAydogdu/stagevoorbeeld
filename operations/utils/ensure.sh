#!/usr/bin/env bash

# Ensure a command is available

function ensure {
	# for each argument, check it is available
	# example: `ops.ensure node git which`
	# will output nothing if the command is available, or exits non-zero if a command is missing
  local missing=0
	# check each argument
	for bin in "$@"
	do
		if [ ! $(command -v $bin) ]; then
			# echo "${ERROR} $bin is not installed on this system" >&2
			echo_error "$bin is not installed on this system"
			missing=$((missing+1))
		fi
	done
	if [ $missing -gt 0 ]; then
	   echo "missing"
	else
	   echo ""
	fi
}

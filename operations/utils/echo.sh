#!/usr/bin/env bash

# echo utilities
# NOTE: these constants are implicitly exported so they can be used in external scripts as well.
bold=$(tput bold)
normal=$(tput sgr0)

SKIP="${bold}$(tput setaf 6)SKIP:  ${normal}"
INFO="${bold}$(tput setaf 2)INFO:  ${normal}"
WARN="${bold}$(tput setaf 5)WARN:  ${normal}"
ERROR="${bold}$(tput setaf 1)ERROR: ${normal}"
DEBUG="${bold}$(tput setaf 3)DEBUG: ${normal}"

function echo_debug() {
    if [ ${OPS_DEBUG+x} ]; then echo "${DEBUG} $*"; fi
}

function echo_skip() {
    echo "${SKIP} $*"
}

function echo_info() {
    # (${CXP_DEBUG} || ${CXP_SKIP} || ${CXP_INFO}) && echo "${INFO} $*"
    echo "${INFO} $*"
}

function echo_error() {
    echo "${ERROR} $*" >&2
}

function echo_warn() {
    echo "${WARN} $*" >&2
}

function banner() {
    local MESSAGE=${1:-""}

    if [ -z ${OPS_VERBOSE+x} ]; then
    echo
    echo "${normal}${bold}Genifer${normal}"
    echo ${normal}
    fi
    echo $MESSAGE
    echo
}

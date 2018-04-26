#!/usr/bin/env bash

# useage:
# ./operations/utils/generateDockerComposeJobs.sh src/docker-compose.jobs.in.yml src/jobs.in >> docker-compose.yml

templateIn=${1}
jobsIn=${2}

current_dir=$(cd $(dirname $0) && pwd)
root_dir=${current_dir}/../..
src_dir=${current_dir}/../../src

cat ${root_dir}/${jobsIn} | \
xargs -Inew-name sed -e 's/wait-for-it/new-name/g' ${root_dir}/${templateIn} | \
python -c '
import sys
def to_camel_case(snake_str):
  components = snake_str.split("-")
  # We capitalize the first letter of each component except the first one
  # with the "title" method and join them together.
  return components[0] + "".join(x.title() for x in components[1:])
p = "--queue="
pl = len(p)
for l in sys.stdin:
  i = l.find(p)
  if ( i != -1 ): # found queue line
    j = l.find(" ",i,)
    w = l[i+pl:j]
    wc = to_camel_case(w)
    # print "#",i,j,w,wc
    l = l.replace(w,wc) # replace with camelcase
  sys.stdout.write(l)
  sys.stdout.flush()
'


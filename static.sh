#!/bin/sh

do_wget ( ) {
	wget -O${2} "http://stats-${USER}.distributed.net/${1}"
	return
}

get_project ( ) {
    do_wget pc_index.php?project_id=$1 cache/index_$1.inc
    do_wget misc/pc_countries.php?project_id=$1\&source=o cache/countries_o_$1.inc
    do_wget misc/pc_countries.php?project_id=$1\&source=y cache/countries_y_$1.inc
    if [ $1 != 3 -a $1 != 5 ]; then
        do_wget misc/pc_money.php?project_id=$1\&source=y cache/money_$1.inc
    fi
}

if [ x$1 = x ]; then
    args="5 8 24 25"
    echo "No projects specified, I assume you want all of them"
else
    args=$@
fi

echo "Updating static pages for projects $args"
echo

for project in $args; do
    get_project $project
done

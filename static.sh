#!/bin/sh

do_wget ( ) {
	wget -O${2} "http://stats-${USER}.distributed.net/${1}"
	return
}

do_wget pc_index.php?project_id=24 cache/index_24.inc
do_wget pc_index.php?project_id=25 cache/index_25.inc
do_wget pc_index.php?project_id=5 cache/index_5.inc
do_wget pc_index.php?project_id=8 cache/index_8.inc

do_wget misc/pc_countries.php?project_id=24\&source=o cache/countries_o_24.inc
do_wget misc/pc_countries.php?project_id=25\&source=o cache/countries_o_25.inc
do_wget misc/pc_countries.php?project_id=5\&source=o cache/countries_o_5.inc 
do_wget misc/pc_countries.php?project_id=8\&source=o cache/countries_o_8.inc 
do_wget misc/pc_countries.php?project_id=24\&source=y cache/countries_y_24.inc
do_wget misc/pc_countries.php?project_id=25\&source=y cache/countries_y_25.inc
do_wget misc/pc_countries.php?project_id=5\&source=y cache/countries_y_5.inc
do_wget misc/pc_countries.php?project_id=8\&source=y cache/countries_y_8.inc

#do_wget pc_index.php index.html
#do_wget pc_countries.php countries.html
#do_wget pc_cpu.php cpu.html
#do_wget pc_money.php money.html

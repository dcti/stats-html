#!/bin/sh

do_wget ( ) {
	wget -O${2} "http://stats-${USER}.distributed.net/ogr-25/${1}"
	return
}

do_wget pc_index.php3 index.html
do_wget pc_countries.php3 countries.html
#do_wget pc_cpu.php3 cpu.html
#do_wget pc_money.php3 money.html

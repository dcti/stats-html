#!/bin/sh

do_wget ( ) {
	wget -O${2} "http://stats-${USER}.distributed.net/generic/${1}"
	return
}

do_wget pc_index.php index.html
do_wget pc_countries.php countries.html
#do_wget pc_cpu.php cpu.html
#do_wget pc_money.php money.html

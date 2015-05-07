#!/bin/sh
for i in 100000 1000000 2000000 5000000 10000000 
do
	for j in 10 30 60 100 
	do
		for k in 0 1 2 3 
		do
		rm -fr mutilprocess.pid;
		php mutilprocess.php start c=$i n=$j i=$k;
		sleep 2m;
		done
	done	
done	



SELECT CONCAT(  `forename` ,  `surname` ) AS uname, COUNT( * ) ,  `membnum` 
FROM  `members` 
WHERE  `u3ayear` =  '2015-2016'
AND STATUS =  'Active'
GROUP BY CONCAT(  `forename` ,  `surname` ) 
HAVING COUNT( * ) >1
LIMIT 0 , 30
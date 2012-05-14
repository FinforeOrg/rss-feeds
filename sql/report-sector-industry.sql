SELECT 
	si1.name AS 'Industry'
	,si2.name AS 'Supersector'
	,si3.name AS 'Sector'
	,si4.name AS 'Subsector'
	,si4.definition AS 'Definition'
FROM sector_industry si1
INNER JOIN sector_industry si2 ON si1.id = si2.parent_id AND si2.level = 2
INNER JOIN sector_industry si3 ON si2.id = si3.parent_id AND si3.level = 3
INNER JOIN sector_industry si4 ON si3.id = si4.parent_id AND si4.level = 4
WHERE	si1.level = 1
;
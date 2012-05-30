SELECT * FROM country_region
WHERE NAME = 'Europe';

SELECT GROUP_CONCAT(DISTINCT id) INTO @country_region_id FROM country_region WHERE `name` IN ('Europe', 'asia');
SELECT GROUP_CONCAT(DISTINCT id) INTO @country_region_id FROM country_region WHERE `name` IN ('World');
SELECT @country_region_id;

SELECT
@country_region_id,
cr4.id AS 'Country (UN)'
	,cr4.name AS 'Country (UN)'
	,cr1.name AS 'World (UN)'
	,cr2.name AS 'Continent (UN)'
	,cr3.name AS 'Region (UN)'
	,cr4.iso_country_code AS 'Country Code (ISO)'

# World
FROM country_region cr1
# Continent
INNER JOIN country_region cr2 ON cr1.id = cr2.parent_region_id AND cr2.other_region = 0
# Region
INNER JOIN country_region cr3 ON cr2.id = cr3.parent_region_id AND cr3.other_region = 0
# Country
INNER JOIN country_region cr4 ON cr3.id = cr4.parent_region_id AND cr4.other_region = 0

WHERE
cr4.id IN (@country_region_id)
OR cr4.parent_region_id IN (@country_region_id)
OR cr3.parent_region_id IN (@country_region_id)
OR cr2.parent_region_id IN (@country_region_id)
OR cr1.parent_region_id IN (@country_region_id)


;
SELECT 
	cr4.name AS 'Country (UN)'
	,cr1.name AS 'World (UN)'
	,cr2.name AS 'Continent (UN)'
	,cr3.name AS 'Region (UN)'
	,cr3_other.name AS 'Other Region (UN)'
	,cr4.iso_country_code AS 'Country Code (ISO)'

	
	,cr1.code AS 'World Code'
	,cr2.code AS 'Continent Code'
	,cr3.code AS 'Region Code'
	,cr3_other.code AS 'Other Region Code (UN)'
	,cr4.code AS 'Country Code'
	
	,COALESCE(cr1.developed_developing_region, cr2.developed_developing_region, cr3.developed_developing_region, cr4.developed_developing_region) AS 'Developed or Developing'

	,IF(cr4.least_developed_country, 'Yes', 'No') AS 'Least developed country'
	,IF(cr4.landlocked_developing_country, 'Yes', 'No') AS 'Landlocked developing country'
	,IF(cr4.small_island_developing_state, 'Yes', 'No') AS 'Small island developing state'
	,IF(cr4.transition_country, 'Yes', 'No') AS 'Transition country'
	
# World
FROM country_region cr1
# Continent
INNER JOIN country_region cr2 ON cr1.id = cr2.parent_region_id AND cr2.other_region = 0
# Region
INNER JOIN country_region cr3 ON cr2.id = cr3.parent_region_id AND cr3.other_region = 0
# Country
INNER JOIN country_region cr4 ON cr3.id = cr4.parent_region_id AND cr4.other_region = 0
# Other Region
LEFT JOIN country_region cr3_other ON cr3.name = cr3_other.name AND cr3_other.other_region = 1

ORDER BY
	cr4.name;
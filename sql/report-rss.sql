SELECT 
	su.id
	,COALESCE(mc.name,'') AS 'Source Category' 
	,GROUP_CONCAT(DISTINCT COALESCE(mc.name,'')) AS source_categories
	,GROUP_CONCAT(DISTINCT mc.tag) AS 'Tags'	
	
	, mu.domain AS 'Source Domain' 
	, mu.url AS 'Source URL' 
	
	, GROUP_CONCAT(DISTINCT sc.name) AS 'Feed Types'
	, su.url AS 'Feed URL'
	, su.title AS 'Feed Title'
	
	, sut.twitter_id AS 'Twitter ID'
	, sut.name AS 'Twitter Name'
	, sut.screen_name AS 'Twitter screen_name'
	, sut.location AS 'Twitter location'
	, sut.description AS 'Twitter description'
	, sut.profile_image_url AS 'Twitter profile_image_url'
	, sut.profile_image_url_https AS 'Twitter profile_image_url_https'
	, sut.url AS 'Twitter url'
	, sut.protected AS 'Twitter protected'
	, sut.followers_count AS 'Twitter followers_count'
	, sut.friends_count AS 'Twitter friends_count'
	, sut.created_at AS 'Twitter created_at'
	, sut.favourites_count AS 'Twitter favourites_count'
	, sut.utc_offset AS 'Twitter utc_offset'
	, sut.time_zone AS 'Twitter time_zone'
	, sut.notifications AS 'Twitter notifications'
	, sut.geo_enabled AS 'Twitter geo_enabled'
	, sut.verified AS 'Twitter verified'
	, sut.following AS 'Twitter following'
	, sut.statuses_count AS 'Twitter statuses_count'
	, sut.lang AS 'Twitter lang'
	, sut.listed_count AS 'Twitter listed_count'
	, sut.saved_at AS 'Twitter saved_at'
FROM scrape_url su
INNER JOIN scrape_url_category suc ON suc.scrape_url_id = su.id
INNER JOIN scrape_category sc ON sc.id = suc.scrape_category_id
LEFT JOIN main_url mu ON mu.id = su.url_id
LEFT JOIN main_category mc ON mc.id = suc.main_category_id
LEFT JOIN scrape_url_twitter sut ON sut.scrape_url_id = su.id
GROUP BY su.id
ORDER BY 
	IF(ISNULL(mc.name),1,0)
	, mc.name
	, mu.id;
	

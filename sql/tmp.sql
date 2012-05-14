SELECT COUNT(*) FROM scrape_url_category WHERE main_category_id != 0;

DROP TEMPORARY TABLE IF EXISTS temp_ids_to_delete;
CREATE TEMPORARY TABLE temp_ids_to_delete
SELECT t2.id
FROM scrape_url_category t1
INNER JOIN scrape_url_category t2 USING(scrape_url_id,scrape_category_id)
WHERE
t1.main_category_id != 0
AND t2.main_category_id = 0
;

DELETE t1 FROM scrape_url_category t1
INNER JOIN temp_ids_to_delete t2
WHERE t1.id = t2.id
;

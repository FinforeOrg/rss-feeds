/*
SQLyog Community v9.61 
MySQL - 5.5.8 : Database - rss_feeds
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `sector_industry` */

DROP TABLE IF EXISTS `sector_industry`;

CREATE TABLE `sector_industry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL DEFAULT '',
  `code` int(11) NOT NULL DEFAULT '0',
  `definition` text,
  `parent_id` int(11) NOT NULL DEFAULT '0',
  `level` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=InnoDB AUTO_INCREMENT=185 DEFAULT CHARSET=utf8;

/*Data for the table `sector_industry` */

insert  into `sector_industry`(`id`,`name`,`code`,`definition`,`parent_id`,`level`) values (1,'Oil & Gas',1,'',0,1),(2,'Oil & Gas',500,'',1,2),(3,'Oil & Gas Producers',530,'',2,3),(4,'Exploration & Production',533,'Companies engaged in the exploration for and drilling, production, refining and supply of oil and gas products.',3,4),(5,'Integrated Oil & Gas',537,'Integrated oil and gas companies engaged in the exploration for and drilling, production, refining, distribution and retail sales of oil and gas products.',3,4),(6,'Oil Equipment, Services & Distribution',570,'',2,3),(7,'Oil Equipment & Services',573,'Suppliers of equipment and services to oil fields and offshore platforms, such as drilling, exploration, seismic-information services and platform construction.',6,4),(8,'Pipelines',577,'Operators of pipelines carrying oil, gas or other forms of fuel. Excludes pipeline operators that derive the majority of their revenues from direct sales to end users, which are classified under Gas Distribution.',6,4),(9,'Alternative Energy',580,'',2,3),(10,'Renewable Energy Equipment',583,'Companies that develop or manufacture renewable energy equipment utilizing sources such as solar, wind, tidal, geothermal, hydro and waves.',9,4),(11,'Alternative Fuels',587,'Companies that produce alternative fuels such as ethanol, methanol, hydrogen and bio-fuels that are mainly used to power vehicles, and companies that are involved in the production of vehicle fuel cells and/or the development of alternative fuelling infrastructure.',9,4),(12,'Basic Materials',1000,'',0,1),(13,'Chemicals',1300,'',12,2),(14,'Chemicals',1350,'',13,3),(15,'Commodity Chemicals',1353,'Producers and distributors of simple chemical products that are primarily used to formulate more complex chemicals or products, including plastics and rubber in their raw form, fibreglass and synthetic fibres.',14,4),(16,'Specialty Chemicals',1357,'Producers and distributors of finished chemicals for industries or end users, including dyes, cellular polymers, coatings, special plastics and other chemicals for specialized applications. Includes makers of colourings, flavours and fragrances, fertilizers, pesticides, chemicals used to make drugs, paint in its pigment form and glass in its unfinished form. Excludes producers of paint and glass products used for construction, which are classified under Building Materials & Fixtures.',14,4),(17,'Basic Resources',1700,'',12,2),(18,'Forestry & Paper',1730,'',17,3),(19,'Forestry',1733,'Owners and operators of timber tracts, forest tree nurseries and sawmills. Excludes providers of finished wood products such as wooden beams, which are classified under Building Materials & Fixtures.',18,4),(20,'Paper',1737,'Producers, converters, merchants and distributors of all grades of paper. Excludes makers of printed forms, which are classified under Business Support Services, and manufacturers of paper items such as cups and napkins, which are classified under Nondurable Household Products.',18,4),(21,'Industrial Metals & Mining',1750,'',17,3),(22,'Aluminium',1753,'Companies that mine or process bauxite or manufacture and distribute aluminium bars, rods and other products for use by other industries. Excludes manufacturers of finished aluminium products, such as siding, which are categorized according to the type of end product.',21,4),(23,'Nonferrous Metals',1755,'Producers and traders of metals and primary metal products other than iron, aluminium and steel. Excludes companies that make finished products, which are categorized according to the type of end product.',21,4),(24,'Iron & Steel',1757,'Manufacturers and stockholders of primary iron and steel products such as pipes, wires, sheets and bars, encompassing all processes from smelting in blast furnaces to rolling mills and foundries. Includes companies that primarily mine iron ores.',21,4),(25,'Mining',1770,'',17,3),(26,'Coal',1771,'Companies engaged in the exploration for or mining of coal.',25,4),(27,'Diamonds & Gemstones',1773,'Companies engaged in the exploration for and production of diamonds and other gemstones.',25,4),(28,'General Mining',1775,'Companies engaged in the exploration, extraction or refining of minerals not defined elsewhere within the Mining sector.',25,4),(29,'Gold Mining',1777,'Prospectors for and extractors or refiners of gold-bearing ores.',25,4),(30,'Platinum & Precious Metals',1779,'Companies engaged in the exploration for and production of platinum, silver and other precious metals not defined elsewhere.',25,4),(31,'Industrials',2000,'',0,1),(32,'Construction & Materials',2300,'',31,2),(33,'Construction & Materials',2350,'',32,3),(34,'Building Materials & Fixtures',2353,'Producers of materials used in the construction and refurbishment of buildings and structures, including cement and other aggregates, wooden beams and frames, paint, glass, roofing and flooring materials other than carpets. Includes producers of bathroom and kitchen fixtures, plumbing supplies and central air-conditioning and heating equipment. Excludes producers of raw lumber, which are classified under Forestry.',33,4),(35,'Heavy Construction',2357,'Companies engaged in the construction of commercial buildings, infrastructure such as roads and bridges, residential apartment buildings, and providers of services to construction companies, such as architects, masons, plumbers and electrical contractors.',33,4),(36,'Industrial Goods & Services',2700,'',31,2),(37,'Aerospace & Defense',2710,'',36,3),(38,'Aerospace',2713,'Manufacturers, assemblers and distributors of aircraft and aircraft parts primarily used in commercial or private air transport. Excludes manufacturers of communications satellites, which are classified under Telecommunications Equipment.',37,4),(39,'Defense',2717,'Producers of components and equipment for the defense industry, including military aircraft, radar equipment and weapons.',37,4),(40,'General Industrials',2720,'',36,3),(41,'Containers & Packaging',2723,'Makers and distributors of cardboard, bags, boxes, cans, drums, bottles and jars and glass used for packaging.',40,4),(42,'Diversified Industrials',2727,'Industrial companies engaged in three or more classes of business within the Industrial industry that differ substantially from each other.',40,4),(43,'Electronic & Electrical Equipment',2730,'',36,3),(44,'Electrical Components & Equipment',2733,'Makers and distributors of electrical parts for finished products, such as printed circuit boards for radios, televisions and other consumer electronics. Includes makers of cables, wires, ceramics, transistors, electric adapters and security cameras.',43,4),(45,'Electronic Equipment',2737,'Manufacturers and distributors of electronic products used in different industries. Includes makers of lasers, smart cards, bar scanners, fingerprinting equipment and other electronic factory equipment.',43,4),(46,'Industrial Engineering',2750,'',36,3),(47,'Commercial Vehicles & Trucks',2753,'Manufacturers and distributors of commercial vehicles and heavy agricultural and construction machinery, including rail cars, tractors, bulldozers, cranes, buses and industrial lawn mowers. Includes non-military shipbuilders, such as builders of cruise ships and ferries.',46,4),(48,'Industrial Machinery',2757,'Designers, manufacturers, distributors and installers of industrial machinery and factory equipment, such as machine tools, lathes, presses and assembly line equipment. Includes makers of pollution control equipment, castings, pressings, welded shapes, structural steelwork, compressors, pumps, bearings, elevators and escalators.',46,4),(49,'Industrial Transportation',2770,'',36,3),(50,'Delivery Services',2771,'Operators of mail and package delivery services for commercial and consumer use. Includes courier and logistic services primarily involving air transportation.',49,4),(51,'Marine Transportation',2773,'Providers of on-water transportation for commercial markets, such as container shipping. Excludes ports, which are classified under Transportation Services, and shipbuilders, which are classified under Commercial Vehicles & Trucks.',49,4),(52,'Railroads',2775,'Providers of industrial railway transportation and railway lines. Excludes passenger railway companies, which are classified under Travel & Tourism, and manufacturers of rail cars, which are classified under Commercial Vehicles & Trucks.',49,4),(53,'Transportation Services',2777,'Companies providing services to the Industrial Transportation sector, including companies that manage airports, train depots, roads, bridges, tunnels, ports, and providers of logistic services to shippers of goods. Includes companies that provide aircraft and vehicle maintenance services.',49,4),(54,'Trucking',2779,'Companies that provide commercial trucking services. Excludes road and tunnel operators, which are classified under Transportation Services, and vehicle rental and taxi companies, which are classified under Travel & Tourism.',49,4),(55,'Support Services',2790,'',36,3),(56,'Business Support Services',2791,'Providers of nonfinancial services to a wide range of industrial enterprises and governments. Includes providers of printing services, management consultants, office cleaning services, and companies that install, service and monitor alarm and security systems.',55,4),(57,'Business Training & Employment Agencies',2793,'Providers of business or management training courses and employment services.',55,4),(58,'Financial Administration',2795,'Providers of computerized transaction processing, data communication and information services, including payroll, bill payment and employee benefit services.',55,4),(59,'Industrial Suppliers',2797,'Distributors and wholesalers of diversified products and equipment primarily used in the commercial and industrial sectors. Includes builders merchants.',55,4),(60,'Waste & Disposal Services',2799,'Providers of pollution control and environmental services for the management, recovery and disposal of solid and hazardous waste materials, such as landfills and recycling centres. Excludes manufacturers of industrial air and water filtration equipment, which are classified under Industrial Machinery.',55,4),(61,'Consumer Goods',3000,'',0,1),(62,'Automobiles & Parts',3300,'',61,2),(63,'Automobiles & Parts',3350,'',62,3),(64,'Automobiles',3353,'Makers of motorcycles and passenger vehicles, including cars, sport utility vehicles (SUVs) and light trucks. Excludes makers of heavy trucks, which are classified under Commercial Vehicles & Trucks, and makers of recreational vehicles (RVs and ATVs), which are classified under Recreational Products.',63,4),(65,'Auto Parts',3355,'Manufacturers and distributors of new and replacement parts for motorcycles and automobiles, such as engines, carburettors and batteries. Excludes producers of tires, which are classified under Tires.',63,4),(66,'Tires',3357,'Manufacturers, distributors and retreaders of automobile, truck and motorcycle tires.',63,4),(67,'Food & Beverage',3500,'',61,2),(68,'Beverages',3530,'',67,3),(69,'Brewers',3533,'Manufacturers and shippers of cider or malt products such as beer, ale and stout.',68,4),(70,'Distillers & Vintners',3535,'Producers, distillers, vintners, blenders and shippers of wine and spirits such as whisky, brandy, rum, gin or liqueurs.',68,4),(71,'Soft Drinks',3537,'Manufacturers, bottlers and distributors of non-alcoholic beverages, such as soda, fruit juices, tea, coffee and bottled water.',68,4),(72,'Food Producers',3570,'',67,3),(73,'Farming & Fishing',3573,'Companies that grow crops or raise livestock, operate fisheries or own nontobacco plantations. Includes manufacturers of livestock feeds and seeds and other agricultural products but excludes manufacturers of fertilizers or pesticides, which are classified under Specialty Chemicals.',72,4),(74,'Food Products',3577,'Food producers, including meatpacking, snacks, fruits, vegetables, dairy products and frozen seafood. Includes producers of pet food and manufacturers of dietary supplements, vitamins and related items. Excludes producers of fruit juices, tea, coffee, bottled water and other non-alcoholic beverages, which are classified under Soft Drinks.',72,4),(75,'Personal & Household Goods',3700,'',61,2),(76,'Household Goods & Home Construction',3720,'',75,3),(77,'Durable Household Products',3722,'Manufacturers and distributors of domestic appliances, lighting, hand tools and power tools, hardware, cutlery, tableware, garden equipment, luggage, towels and linens.',76,4),(78,'Nondurable Household Products',3724,'Producers and distributors of pens, paper goods, batteries, light bulbs, tissues, toilet paper and cleaning products such as soaps and polishes.',76,4),(79,'Furnishings',3726,'Manufacturers and distributors of furniture, including chairs, tables, desks, carpeting, wallpaper and office furniture.',76,4),(80,'Home Construction',3728,'Constructors of residential homes, including manufacturers of mobile and prefabricated homes intended for use in one place.',76,4),(81,'Leisure Goods',3740,'',75,3),(82,'Consumer Electronics',3743,'Manufacturers and distributors of consumer electronics, such as TVs, VCRs, DVD players, audio equipment, cable boxes, calculators and camcorders.',81,4),(83,'Recreational Products',3745,'Manufacturers and distributors of recreational equipment. Includes musical instruments, photographic equipment and supplies, RVs, ATVs and marine recreational vehicles such as yachts, dinghies and speedboats.',81,4),(84,'Toys',3747,'Manufacturers and distributors of toys and video/computer games, including such toys and games as playing cards, board games, stuffed animals and dolls.',81,4),(85,'Personal Goods',3760,'',75,3),(86,'Clothing & Accessories',3763,'Manufacturers and distributors of all types of clothing, jewellery, watches or textiles. Includes sportswear, sunglasses, eyeglass frames, leather clothing and goods, and processors of hides and skins.',85,4),(87,'Footwear',3765,'Manufacturers and distributors of shoes, boots, sandals, sneakers and other types of footwear.',85,4),(88,'Personal Products',3767,'Makers and distributors of cosmetics, toiletries and personal-care and hygiene products, including deodorants, soaps, toothpaste, perfumes, diapers, shampoos, razors and feminine-hygiene products. Includes makers of contraceptives other than oral contraceptives, which are classified under Pharmaceuticals.',85,4),(89,'Tobacco',3780,'',75,3),(90,'Tobacco',3785,'Manufacturers and distributors of cigarettes, cigars and other tobacco products. Includes tobacco plantations.',89,4),(91,'Health Care',4000,'',0,1),(92,'Health Care',4500,'',91,2),(93,'Health Care Equipment & Services',4530,'',92,3),(94,'Health Care Providers',4533,'Owners and operators of health maintenance organizations, hospitals, clinics, dentists, opticians, nursing homes, rehabilitation and retirement centres. Excludes veterinary services, which are classified under Specialized Consumer Services.',93,4),(95,'Medical Equipment',4535,'Manufacturers and distributors of medical devices such as MRI scanners, prosthetics, pacemakers, X-ray machines and other non-disposable medical devices.',93,4),(96,'Medical Supplies',4537,'Manufacturers and distributors of medical supplies used by health care providers and the general public. Includes makers of contact lenses, eyeglass lenses, bandages and other disposable medical supplies.',93,4),(97,'Pharmaceuticals & Biotechnology',4570,'',92,3),(98,'Biotechnology',4573,'Companies engaged in research into and development of biological substances for the purposes of drug discovery and diagnostic development, and which derive the majority of their revenue from either the sale or licensing of these drugs and diagnostic tools.',97,4),(99,'Pharmaceuticals',4577,'Manufacturers of prescription or over-the-counter drugs, such as aspirin, cold remedies and birth control pills. Includes vaccine producers but excludes vitamin producers, which are classified under Food Products.',97,4),(100,'Consumer Services',5000,'',0,1),(101,'Retail',5300,'',100,2),(102,'Food & Drug Retailers',5330,'',101,3),(103,'Drug Retailers',5333,'Operators of pharmacies, including wholesalers and distributors catering to these businesses.',102,4),(104,'Food Retailers & Wholesalers',5337,'Supermarkets, food-oriented convenience stores and other food retailers and distributors. Includes retailers of dietary supplements and vitamins.',102,4),(105,'General Retailers',5370,'',101,3),(106,'Apparel Retailers',5371,'Retailers and wholesalers specializing mainly in clothing, shoes, jewellery, sunglasses and other accessories.',105,4),(107,'Broadline Retailers',5373,'Retail outlets and wholesalers offering a wide variety of products including both hard goods and soft goods.',105,4),(108,'Home Improvement Retailers',5375,'Retailers and wholesalers concentrating on the sale of home improvement products, including garden equipment, carpets, wallpaper, paint, home furniture, blinds and curtains, and building materials.',105,4),(109,'Specialized Consumer Services',5377,'Providers of consumer services such as auction houses, day-care centres, dry cleaners, schools, consumer rental companies, veterinary clinics, hair salons and providers of funeral, lawn-maintenance, consumer-storage, heating and cooling installation and plumbing services.',105,4),(110,'Specialty Retailers',5379,'Retailers and wholesalers concentrating on a single class of goods, such as electronics, books, automotive parts or closeouts. Includes automobile dealerships, video rental stores, dollar stores, duty-free shops and automotive fuel stations not owned by oil companies.',105,4),(111,'Media',5500,'',100,2),(112,'Media',5550,'',111,3),(113,'Broadcasting & Entertainment',5553,'Producers, operators and broadcasters of radio, television, music and filmed entertainment. Excludes movie theatres, which are classified under Recreational Services.',112,4),(114,'Media Agencies',5555,'Companies providing advertising, public relations and marketing services. Includes billboard providers and telemarketers.',112,4),(115,'Publishing',5557,'Publishers of information via printed or electronic media.',112,4),(116,'Travel & Leisure',5700,'',100,2),(117,'Travel & Leisure',5750,'',116,3),(118,'Airlines',5751,'Companies providing primarily passenger air transport. Excludes airports, which are classified under Transportation Services.',117,4),(119,'Gambling',5752,'Providers of gambling and casino facilities. Includes online casinos, racetracks and the manufacturers of pachinko machines and casino and lottery equipment.',117,4),(120,'Hotels',5753,'Operators and managers of hotels, motels, lodges, resorts, spas and campgrounds.',117,4),(121,'Recreational Services',5755,'Providers of leisure facilities and services, including fitness centres, cruise lines, movie theatres and sports teams.',117,4),(122,'Restaurants & Bars',5757,'Operators of restaurants, fast-food facilities, coffee shops and bars. Includes integrated brewery companies and catering companies.',117,4),(123,'Travel & Tourism',5759,'Companies providing travel and tourism related services, including travel agents, online travel reservation services, automobile rental firms and companies that primarily provide passenger transportation, such as buses, taxis, passenger rail and ferry companies.',117,4),(124,'Telecommunications',6000,'',0,1),(125,'Telecommunications',6500,'',124,2),(126,'Fixed Line Telecommunications',6530,'',125,3),(127,'Fixed Line Telecommunications',6535,'Providers of fixed-line telephone services, including regional and long-distance. Includes companies that primarily provides telephone services through the internet. Excludes companies whose primary business is Internet access, which are classified under Internet.',126,4),(128,'Mobile Telecommunications',6570,'',125,3),(129,'Mobile Telecommunications',6575,'Providers of mobile telephone services, including cellular, satellite and paging services. Includes wireless tower companies that own, operate and lease mobile site towers to multiple wireless service providers.',128,4),(130,'Utilities',7000,'',0,1),(131,'Utilities',7500,'',130,2),(132,'Electricity',7530,'',131,3),(133,'Conventional Electricity',7535,'Companies generating and distributing electricity through the burning of fossil fuels such as coal, petroleum and natural gas, and through nuclear energy.',132,4),(134,'Alternative Electricity',7537,'Companies generating and distributing electricity from a renewable source. Includes companies that produce solar, water, wind and geothermal electricity.',132,4),(135,'Gas, Water & Multi-utilities',7570,'',131,3),(136,'Gas Distribution',7573,'Distributors of gas to end users. Excludes providers of natural gas as a commodity, which are classified under the Oil & Gas industry.',135,4),(137,'Multi-utilities',7575,'Utility companies with significant presence in more than one utility.',135,4),(138,'Water',7577,'Companies providing water to end users, including water treatment plants.',135,4),(139,'Financials',8000,'',0,1),(140,'Banks',8300,'',139,2),(141,'Banks',8350,'',140,3),(142,'Banks',8355,'Banks providing a broad range of financial services, including retail banking, loans and money transmissions.',141,4),(143,'Insurance',8500,'',139,2),(144,'Nonlife Insurance',8530,'',143,3),(145,'Full Line Insurance',8532,'Insurance companies with life, health, property & casualty and reinsurance interests, no one of which predominates.',144,4),(146,'Insurance Brokers',8534,'Insurance brokers and agencies.',144,4),(147,'Property & Casualty Insurance',8536,'Companies engaged principally in accident, fire, automotive, marine, malpractice and other classes of nonlife insurance.',144,4),(148,'Reinsurance',8538,'Companies engaged principally in reinsurance.',144,4),(149,'Life Insurance',8570,'',143,3),(150,'Life Insurance',8575,'Companies engaged principally in life and health insurance.',149,4),(151,'Real Estate',8600,'',139,2),(152,'Real Estate Investment & Services',8630,'',151,3),(153,'Real Estate Holding & Development',8633,'Companies that invest directly or indirectly in real estate through development, investment or ownership. Excludes real estate investment trusts and similar entities, which are classified as Real Estate Investment Trusts.',152,4),(154,'Real Estate Services',8637,'Companies that provide services to real estate companies but do not own the properties themselves. Includes agencies, brokers, leasing companies, management companies and advisory services. Excludes real estate investment trusts and similar entities, which are classified as Real Estate Investment Trusts.',152,4),(155,'Real Estate Investment Trusts',8670,'',151,3),(156,'Industrial & Office REITs',8671,'Real estate investment trusts or corporations (REITs) or listed property trusts (LPTs) that primarily invest in office, industrial and flex properties.',155,4),(157,'Retail REITs',8672,'Real estate investment trusts or corporations (REITs) or listed property trusts (LPTs) that primarily invest in retail properties. Includes malls, shopping centres, strip centres and factory outlets.',155,4),(158,'Residential REITs',8673,'Real estate investment trusts or corporations (REITs) or listed property trusts (LPTs) that primarily invest in residential home properties. Includes apartment buildings and residential communities.',155,4),(159,'Diversified REITs',8674,'Real estate investment trusts or corporations (REITs) or listed property trusts (LPTs) that invest in a variety of property types without a concentration on any single type.',155,4),(160,'Specialty REITs',8675,'Real estate investment trusts or corporations (REITs) or listed property trusts (LPTs) that invest in self storage properties, properties in the health care industry such as hospitals, assisted living facilities and health care laboratories, and other specialized properties such as auto dealership facilities, timber properties and net lease properties.',155,4),(161,'Mortgage REITs',8676,'Real estate investment trusts or corporations (REITs) or listed property trusts (LPTs) that are directly involved in lending money to real estate owners and operators or indirectly through the purchase of mortgages or mortgage backed securities.',155,4),(162,'Hotel & Lodging REITs',8677,'Real estate investment trusts or corporations (REITs) or listed property trusts (LPTs) that primarily invest in hotels or lodging properties.',155,4),(163,'Financial Services',8700,'',139,2),(164,'Financial Services',8770,'',163,3),(165,'Asset Managers',8771,'Companies that provide custodial, trustee and other related fiduciary services. Includes mutual fund management companies.',164,4),(166,'Consumer Finance',8773,'Credit card companies and providers of personal finance services such as personal loans and check cashing companies.',164,4),(167,'Specialty Finance',8775,'Companies engaged in financial activities not specified elsewhere. Includes companies not classified under Equity Investment Instruments or Nonequity Investment Instruments engaged primarily in owning stakes in a diversified range of companies.',164,4),(168,'Investment Services',8777,'Companies providing a range of specialized financial services, including securities brokers and dealers, online brokers and security or commodity exchanges.',164,4),(169,'Mortgage Finance',8779,'Companies that provide mortgages, mortgage insurance and other related services.',164,4),(170,'Equity Investment Instruments',8980,'',163,3),(171,'Equity Investment Instruments',8985,'Corporate closed-ended investment entities identified under distinguishing legislation, such as investment trusts and venture capital trusts.',170,4),(172,'Nonequity Investment Instruments',8990,'',163,3),(173,'Nonequity Investment Instruments',8995,'Noncorporate, open-ended investment instruments such as open-ended investment companies and funds, unit trusts, ETFs and currency funds and split capital trusts.',172,4),(174,'Technology',9000,'',0,1),(175,'Technology',9500,'',174,2),(176,'Software & Computer Services',9530,'',175,3),(177,'Computer Services',9533,'Companies that provide consulting services to other businesses relating to information technology. Includes providers of computer-system design, systems integration, network and systems operations, data management and storage, repair services and technical support.',176,4),(178,'Internet',9535,'Companies providing Internet-related services, such as Internet access providers and search engines and providers of Web site design, Web hosting, domain-name registration and e-mail services.',176,4),(179,'Software',9537,'Publishers and distributors of computer software for home or corporate use. Excludes computer game producers, which are classified under Toys.',176,4),(180,'Technology Hardware & Equipment',9570,'',175,3),(181,'Computer Hardware',9572,'Manufacturers and distributors of computers, servers, mainframes, workstations and other computer hardware and subsystems, such as mass-storage drives, mice, keyboards and printers.',180,4),(182,'Electronic Office Equipment',9574,'Manufacturers and distributors of electronic office equipment, including photocopiers and fax machines.',180,4),(183,'Semiconductors',9576,'Producers and distributors of semiconductors and other integrated chips, including other products related to the semiconductor industry, such as semiconductor capital equipment and motherboards. Excludes makers of printed circuit boards, which are classified under Electrical Components & Equipment.',180,4),(184,'Telecommunications Equipment',9578,'Makers and distributors of high-technology communication products, including satellites, mobile telephones, fibres optics, switching devices, local and wide-area networks, teleconferencing equipment and connectivity devices for computers, including hubs and routers.',180,4);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

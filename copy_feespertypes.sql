DROP TABLE IF EXISTS `feespertypes`;
CREATE TABLE IF NOT EXISTS `feespertypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `membtype` varchar(4) DEFAULT NULL,
  `feetopay` decimal(10,0) DEFAULT NULL,
  `firstyearfee` decimal(10,0) DEFAULT '0',
  `acyear` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `index_feespertypes_on_membtype` (`membtype`),
  KEY `index_feespertypes_on_acyear` (`acyear`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `feespertypes`
--

INSERT INTO `feespertypes` (`id`, `membtype`, `feetopay`, `firstyearfee`, `acyear`, `created_at`, `updated_at`) VALUES
(2, 'CM', 0, 0, '2014-2015', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(3, 'CMS', 0, 0, '2014-2015', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(4, 'GL', 0, 0, '2014-2015', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(5, 'GLS', 0, 0, '2014-2015', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(6, 'M', 10, 20, '2014-2015', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(7, 'MC', 10, 20, '2014-2015', '0000-00-00 00:00:00', '0000-00-00 00:00:00'),
(8, 'MJL1', 25, 25, '2014-2015', '0000-00-00 00:00:00', '2015-05-15 15:18:34'),
(9, 'MJL2', 0, 0, '2014-2015', '0000-00-00 00:00:00', '2015-04-20 18:13:16'),
(10, 'CMGL', 0, 0, '2014-2015', '0000-00-00 00:00:00', '0000-00-00 00:00:00');
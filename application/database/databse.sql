Database name: blog_rest_api

Table code:

CREATE TABLE `users` (
 `id` int(11) NOT NULL AUTO_INCREMENT,
 `name` varchar(128) NOT NULL,
 `email` varchar(128) NOT NULL,
 `mobile` varchar(11) NOT NULL,
 `upazila_id` int(11) NOT NULL,
 `status` tinyint(1) NOT NULL DEFAULT '1',
 `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1

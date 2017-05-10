-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- Client: localhost
-- Généré le: Mer 10 Mai 2017 à 02:36
-- Version du serveur: 5.5.54-0ubuntu0.14.04.1
-- Version de PHP: 5.5.9-1ubuntu4.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de données: `net_musiquesincongrues_www_flarum`
--

-- --------------------------------------------------------

--
-- Structure de la table `access_tokens`
--

CREATE TABLE IF NOT EXISTS `access_tokens` (
  `id` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `last_activity` int(11) NOT NULL,
  `lifetime` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `api_keys`
--

CREATE TABLE IF NOT EXISTS `api_keys` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `auth_tokens`
--

CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `discussions`
--

CREATE TABLE IF NOT EXISTS `discussions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `comments_count` int(10) unsigned NOT NULL DEFAULT '0',
  `participants_count` int(10) unsigned NOT NULL DEFAULT '0',
  `number_index` int(10) unsigned NOT NULL DEFAULT '0',
  `start_time` datetime NOT NULL,
  `start_user_id` int(10) unsigned DEFAULT NULL,
  `start_post_id` int(10) unsigned DEFAULT NULL,
  `last_time` datetime DEFAULT NULL,
  `last_user_id` int(10) unsigned DEFAULT NULL,
  `last_post_id` int(10) unsigned DEFAULT NULL,
  `last_post_number` int(10) unsigned DEFAULT NULL,
  `hide_time` datetime DEFAULT NULL,
  `hide_user_id` int(10) unsigned DEFAULT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  `is_locked` tinyint(1) NOT NULL DEFAULT '0',
  `is_sticky` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `discussions_tags`
--

CREATE TABLE IF NOT EXISTS `discussions_tags` (
  `discussion_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`discussion_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `email_tokens`
--

CREATE TABLE IF NOT EXISTS `email_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `flags`
--

CREATE TABLE IF NOT EXISTS `flags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `post_id` int(10) unsigned NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `reason` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reason_detail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_singular` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_plural` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=5 ;

--
-- Contenu de la table `groups`
--

INSERT INTO `groups` (`id`, `name_singular`, `name_plural`, `color`, `icon`) VALUES
(1, 'Admin', 'Admins', '#B72A2A', 'wrench'),
(2, 'Guest', 'Guests', NULL, NULL),
(3, 'Member', 'Members', NULL, NULL),
(4, 'Mod', 'Mods', '#80349E', 'bolt');

-- --------------------------------------------------------

--
-- Structure de la table `mentions_posts`
--

CREATE TABLE IF NOT EXISTS `mentions_posts` (
  `post_id` int(10) unsigned NOT NULL,
  `mentions_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`mentions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `mentions_users`
--

CREATE TABLE IF NOT EXISTS `mentions_users` (
  `post_id` int(10) unsigned NOT NULL,
  `mentions_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`mentions_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE IF NOT EXISTS `migrations` (
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `extension` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `migrations`
--

INSERT INTO `migrations` (`migration`, `extension`) VALUES
('2015_02_24_000000_create_access_tokens_table', NULL),
('2015_02_24_000000_create_api_keys_table', NULL),
('2015_02_24_000000_create_config_table', NULL),
('2015_02_24_000000_create_discussions_table', NULL),
('2015_02_24_000000_create_email_tokens_table', NULL),
('2015_02_24_000000_create_groups_table', NULL),
('2015_02_24_000000_create_notifications_table', NULL),
('2015_02_24_000000_create_password_tokens_table', NULL),
('2015_02_24_000000_create_permissions_table', NULL),
('2015_02_24_000000_create_posts_table', NULL),
('2015_02_24_000000_create_users_discussions_table', NULL),
('2015_02_24_000000_create_users_groups_table', NULL),
('2015_02_24_000000_create_users_table', NULL),
('2015_09_15_000000_create_auth_tokens_table', NULL),
('2015_09_20_224327_add_hide_to_discussions', NULL),
('2015_09_22_030432_rename_notification_read_time', NULL),
('2015_10_07_130531_rename_config_to_settings', NULL),
('2015_10_24_194000_add_ip_address_to_posts', NULL),
('2015_12_05_042721_change_access_tokens_columns', NULL),
('2015_12_17_194247_change_settings_value_column_to_text', NULL),
('2016_02_04_095452_add_slug_to_discussions', NULL),
('2015_09_21_011527_add_is_approved_to_discussions', 'flarum-approval'),
('2015_09_21_011706_add_is_approved_to_posts', 'flarum-approval'),
('2015_09_02_000000_add_flags_read_time_to_users_table', 'flarum-flags'),
('2015_09_02_000000_create_flags_table', 'flarum-flags'),
('2015_05_11_000000_create_posts_likes_table', 'flarum-likes'),
('2015_09_04_000000_add_default_like_permissions', 'flarum-likes'),
('2015_02_24_000000_add_locked_to_discussions', 'flarum-lock'),
('2015_05_11_000000_create_mentions_posts_table', 'flarum-mentions'),
('2015_05_11_000000_create_mentions_users_table', 'flarum-mentions'),
('2015_02_24_000000_add_sticky_to_discussions', 'flarum-sticky'),
('2015_05_11_000000_add_subscription_to_users_discussions_table', 'flarum-subscriptions'),
('2015_05_11_000000_add_suspended_until_to_users_table', 'flarum-suspend'),
('2015_09_14_000000_rename_suspended_until_column', 'flarum-suspend'),
('2015_02_24_000000_create_discussions_tags_table', 'flarum-tags'),
('2015_02_24_000000_create_tags_table', 'flarum-tags'),
('2015_02_24_000000_create_users_tags_table', 'flarum-tags'),
('2015_02_24_000000_set_default_settings', 'flarum-tags'),
('2015_10_19_061223_make_slug_unique', 'flarum-tags');

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `sender_id` int(10) unsigned DEFAULT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `subject_type` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` int(10) unsigned DEFAULT NULL,
  `data` blob,
  `time` datetime NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT '0',
  `is_deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `password_tokens`
--

CREATE TABLE IF NOT EXISTS `password_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE IF NOT EXISTS `permissions` (
  `group_id` int(10) unsigned NOT NULL,
  `permission` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`group_id`,`permission`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `permissions`
--

INSERT INTO `permissions` (`group_id`, `permission`) VALUES
(2, 'viewDiscussions'),
(3, 'discussion.likePosts'),
(3, 'discussion.reply'),
(3, 'startDiscussion'),
(4, 'discussion.delete'),
(4, 'discussion.deletePosts'),
(4, 'discussion.editPosts'),
(4, 'discussion.rename');

-- --------------------------------------------------------

--
-- Structure de la table `posts`
--

CREATE TABLE IF NOT EXISTS `posts` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `discussion_id` int(10) unsigned NOT NULL,
  `number` int(10) unsigned DEFAULT NULL,
  `time` datetime NOT NULL,
  `user_id` int(10) unsigned DEFAULT NULL,
  `type` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `edit_time` datetime DEFAULT NULL,
  `edit_user_id` int(10) unsigned DEFAULT NULL,
  `hide_time` datetime DEFAULT NULL,
  `hide_user_id` int(10) unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `posts_discussion_id_number_unique` (`discussion_id`,`number`),
  FULLTEXT KEY `content` (`content`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Structure de la table `posts_likes`
--

CREATE TABLE IF NOT EXISTS `posts_likes` (
  `post_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`post_id`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `settings`
--

INSERT INTO `settings` (`key`, `value`) VALUES
('allow_post_editing', 'reply'),
('allow_renaming', '10'),
('allow_sign_up', '1'),
('custom_less', ''),
('default_locale', 'en'),
('default_route', '/all'),
('extensions_enabled', '["flarum-approval","flarum-bbcode","flarum-emoji","flarum-english","flarum-flags","flarum-likes","flarum-lock","flarum-markdown","flarum-mentions","flarum-sticky","flarum-subscriptions","flarum-suspend","flarum-tags"]'),
('flarum-tags.max_primary_tags', '1'),
('flarum-tags.max_secondary_tags', '3'),
('flarum-tags.min_primary_tags', '1'),
('flarum-tags.min_secondary_tags', '0'),
('forum_description', ''),
('forum_title', 'Musiques Incongrues'),
('mail_driver', 'mail'),
('mail_from', 'noreply@vanilla.musiques-incongrues.vagrant.test'),
('theme_colored_header', '0'),
('theme_dark_mode', '0'),
('theme_primary_color', '#4D698E'),
('theme_secondary_color', '#4D698E'),
('version', '0.1.0-beta.6'),
('welcome_message', 'This is beta software and you should not use it in production.'),
('welcome_title', 'Welcome to Musiques Incongrues');

-- --------------------------------------------------------

--
-- Structure de la table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `color` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background_path` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `background_mode` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `default_sort` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_restricted` tinyint(1) NOT NULL DEFAULT '0',
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  `discussions_count` int(10) unsigned NOT NULL DEFAULT '0',
  `last_time` datetime DEFAULT NULL,
  `last_discussion_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tags_slug_unique` (`slug`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `tags`
--

INSERT INTO `tags` (`id`, `name`, `slug`, `description`, `color`, `background_path`, `background_mode`, `position`, `parent_id`, `default_sort`, `is_restricted`, `is_hidden`, `discussions_count`, `last_time`, `last_discussion_id`) VALUES
(1, 'General', 'general', NULL, '#888', NULL, NULL, 0, NULL, NULL, 0, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_activated` tinyint(1) NOT NULL DEFAULT '0',
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bio` text COLLATE utf8mb4_unicode_ci,
  `avatar_path` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `preferences` blob,
  `join_time` datetime DEFAULT NULL,
  `last_seen_time` datetime DEFAULT NULL,
  `read_time` datetime DEFAULT NULL,
  `notifications_read_time` datetime DEFAULT NULL,
  `discussions_count` int(10) unsigned NOT NULL DEFAULT '0',
  `comments_count` int(10) unsigned NOT NULL DEFAULT '0',
  `flags_read_time` datetime DEFAULT NULL,
  `suspend_until` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=2 ;

--
-- Contenu de la table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `is_activated`, `password`, `bio`, `avatar_path`, `preferences`, `join_time`, `last_seen_time`, `read_time`, `notifications_read_time`, `discussions_count`, `comments_count`, `flags_read_time`, `suspend_until`) VALUES
(1, 'bertier', 'bertier@musiques-incongrues.net', 1, '$2y$10$LIbHYFpuRY1qtj7hwIU/Y.JKWBFunQw7b.NtWMS0MypmvsmXN.wxu', NULL, NULL, NULL, '2017-05-10 00:30:47', '2017-05-10 00:35:53', NULL, NULL, 0, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `users_discussions`
--

CREATE TABLE IF NOT EXISTS `users_discussions` (
  `user_id` int(10) unsigned NOT NULL,
  `discussion_id` int(10) unsigned NOT NULL,
  `read_time` datetime DEFAULT NULL,
  `read_number` int(10) unsigned DEFAULT NULL,
  `subscription` enum('follow','ignore') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`user_id`,`discussion_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users_groups`
--

CREATE TABLE IF NOT EXISTS `users_groups` (
  `user_id` int(10) unsigned NOT NULL,
  `group_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`user_id`,`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Contenu de la table `users_groups`
--

INSERT INTO `users_groups` (`user_id`, `group_id`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `users_tags`
--

CREATE TABLE IF NOT EXISTS `users_tags` (
  `user_id` int(10) unsigned NOT NULL,
  `tag_id` int(10) unsigned NOT NULL,
  `read_time` datetime DEFAULT NULL,
  `is_hidden` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`tag_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 11, 2008 at 03:50 PM
-- Server version: 5.0.41
-- PHP Version: 5.2.3

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `easydev0`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `adminmain`
-- 

CREATE TABLE `adminmain` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `text` varchar(255) collate latin1_german1_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `text` (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=7 ;

-- 
-- Dumping data for table `adminmain`
-- 

INSERT INTO `adminmain` (`id`, `text`) VALUES 
(6, 'console_easydev_compiler'),
(5, 'console_easydev_config'),
(3, 'console_help'),
(4, 'console_log'),
(2, 'console_personal_info'),
(1, 'console_superadmin');

-- --------------------------------------------------------

-- 
-- Table structure for table `adminsub`
-- 

CREATE TABLE `adminsub` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `id_mainmenu` int(10) unsigned NOT NULL,
  `text` varchar(255) collate latin1_german1_ci NOT NULL,
  `url` varchar(255) collate latin1_german1_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_mainmenu` (`id_mainmenu`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=10 ;

-- 
-- Dumping data for table `adminsub`
-- 

INSERT INTO `adminsub` (`id`, `id_mainmenu`, `text`, `url`) VALUES 
(1, 1, 'console_add_admin', 'addadmin'),
(2, 1, 'console_remove_admin', 'removeadmin'),
(3, 6, 'console_compiler_new_object', 'compiler'),
(4, 1, 'console_edit_admin_permissions', 'permissionadmin'),
(7, 6, 'console_compiler_remove_object', 'removeeasydevobject'),
(8, 5, 'console_easydev_configure', 'config'),
(9, 4, 'console_view_admin_logs', 'logsadmin');

-- --------------------------------------------------------

-- 
-- Table structure for table `authorized_admins`
-- 

CREATE TABLE `authorized_admins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate latin1_german1_ci NOT NULL,
  `password` varchar(255) collate latin1_german1_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `authorized_admins`
-- 

INSERT INTO `authorized_admins` (`id`, `name`, `password`) VALUES 
(2, 'admin', '2dd07c9ce0189aaacacff6a86a5fc61a8d38d851');

-- --------------------------------------------------------

-- 
-- Table structure for table `configuration`
-- 

CREATE TABLE `configuration` (
  `id` varchar(25) collate latin1_german1_ci NOT NULL,
  `value` varchar(255) collate latin1_german1_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- 
-- Dumping data for table `configuration`
-- 

INSERT INTO `configuration` (`id`, `value`) VALUES 
('default_language', 'fr'),
('version', '1.0');

-- --------------------------------------------------------

-- 
-- Table structure for table `easydev_objects`
-- 

CREATE TABLE `easydev_objects` (
  `id_mainmenu` int(10) unsigned NOT NULL,
  `name` varchar(50) collate latin1_german1_ci NOT NULL,
  `definition` text collate latin1_german1_ci,
  PRIMARY KEY  (`id_mainmenu`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- 
-- Dumping data for table `easydev_objects`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `easydev_objects_foreign_key`
-- 

CREATE TABLE `easydev_objects_foreign_key` (
  `id_object` int(10) unsigned NOT NULL,
  `id_foreign_object` int(10) unsigned NOT NULL,
  `relationname` varchar(255) collate latin1_german1_ci NOT NULL,
  PRIMARY KEY  (`id_object`,`id_foreign_object`,`relationname`),
  KEY `id_foreign_object` (`id_foreign_object`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- 
-- Dumping data for table `easydev_objects_foreign_key`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `easydev_objects_linking_tables`
-- 

CREATE TABLE `easydev_objects_linking_tables` (
  `id_objet1` int(10) unsigned NOT NULL,
  `id_objet2` int(10) unsigned NOT NULL,
  `table_name` varchar(64) collate latin1_german1_ci NOT NULL,
  `relationname` varchar(255) collate latin1_german1_ci NOT NULL,
  PRIMARY KEY  (`id_objet1`,`id_objet2`,`table_name`),
  KEY `id_objet2` (`id_objet2`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- 
-- Dumping data for table `easydev_objects_linking_tables`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `logs`
-- 

CREATE TABLE `logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `log` varchar(255) collate latin1_german1_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=1 ;

-- 
-- Dumping data for table `logs`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `permission_admins`
-- 

CREATE TABLE `permission_admins` (
  `id_admin` int(10) unsigned NOT NULL,
  `id_mainsection` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`id_admin`,`id_mainsection`),
  KEY `id_mainsection` (`id_mainsection`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- 
-- Dumping data for table `permission_admins`
-- 

INSERT INTO `permission_admins` (`id_admin`, `id_mainsection`) VALUES 
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6);

-- --------------------------------------------------------

-- 
-- Table structure for table `translation_languages`
-- 

CREATE TABLE `translation_languages` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `language` varchar(50) collate latin1_german1_ci NOT NULL,
  `tag` varchar(2) collate latin1_german1_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `translation_languages`
-- 

INSERT INTO `translation_languages` (`id`, `language`, `tag`) VALUES 
(1, 'fran�ais', 'fr'),
(2, 'english', 'en');

-- --------------------------------------------------------

-- 
-- Table structure for table `translation_strings`
-- 

CREATE TABLE `translation_strings` (
  `keyword` varchar(64) collate latin1_german1_ci NOT NULL,
  `fr` text collate latin1_german1_ci,
  `en` text collate latin1_german1_ci,
  PRIMARY KEY  (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

-- 
-- Dumping data for table `translation_strings`
-- 

INSERT INTO `translation_strings` (`keyword`, `fr`, `en`) VALUES 
('cancel', 'annuler', 'cancel'),
('change_permission', 'changer les permissions', 'change permissions'),
('compile', 'compiler', 'compile'),
('compile_add_object_sub_menu_title', 'ajouter', 'add'),
('compile_autogenerated_page_exist_error', 'Un script g�n�r� par la compilation existe d�j�. Contactez la personne en charge de l''installation de la console EasyDev.', 'One of the compiler-generated scripts already exists. Contact the person in charge of the EasyDev console.'),
('compile_character_error', 'Des caract�res non permis sont contenus dans la d�finition de l''objet. Les caract�res admissibles sont les chiffres, les lettres majuscules et minuscules ainsi que les signes _,;"(){}.', 'Some characters in the definition of the object are not allowed. The allowed characters are numbers, lower and upper case letters and _,;"(){} special signs.'),
('compile_database_duplicate_object_error', 'Un des objets existe d�j� dans la base de donn�es.', 'One of the objects already exists in database.'),
('compile_delete_object_sub_menu_title', 'objets existants', 'existing objects'),
('compile_duplicate_field_name_error', 'Ce nom de champs ne peut �tre utilis� pour d�finir plusieurs champs : ', 'This field name cannot be used to define several fields : '),
('compile_duplicate_object_error', 'Deux objets portant le m�me nom sont interdits.', 'Two objects with the same name are not allowed.'),
('compile_duplicate_relation_error', 'Deux relations de m�me nom portant sur le m�me objet sont interdites.', 'Two relations with same names on the same object are not allowed.'),
('compile_duplicate_sql_function_error', 'Ce nom de finder ou d''updater ne peut �tre utilis� pour deux fonctions diff�rentes :', 'This finder or updater name cannot be reused for several different functions : '),
('compile_expected_identifier', 'identifiant attendu', 'identifier expected'),
('compile_fopen_pointer_error', 'L''ouverture d''un fichier en �criture sur le disque n''a pu �tre faite. Contactez la personne en charge de la console EasyDev.', 'A file opening on disc for writing was aborted. Contact the person in charge of the EasyDev console.'),
('compile_identifier_error', 'Identifiant non permis : ', 'Not-allowed identifier encountered : '),
('compile_no_class_def_found_error', 'Aucune d�finition de classe n''a �t� trouv�e.', 'No class definition found.'),
('compile_no_field_def_found_error', 'Cette classe ne contient pas de d�finition de champs :', 'This class do not contains any field definition : '),
('compile_relationnm_not_recursive_error', 'Cette relation n''a pas pu �tre identifi�e r�ciproquement dans l''objet distant : ', 'This relation could not be verified in the foreign object : '),
('compile_relation_unknown_object_error', 'Une relation avec un objet non-d�fini a �t� d�tect�e.', 'A relation with an undefined object has been detected.'),
('compile_self_relation_error', 'Aucune relation n''est permise avec l''objet dans lequel est d�finie la relation.', 'No relation is allowed with the object in which the relation is defined.'),
('compile_token_mismatch_error', 'Token inattendu rencontr�.', 'Unexpected token error.'),
('compile_unexpected_token_error', 'Un token inattendu a �t� rencontr� en fin de d�finition d''objet : ', 'An unexpected token has been encountered at the end of a class definition : '),
('confirm_password', 'confirmation du mot de passe', 'confirmation of the password'),
('connect', 'se connecter', 'log in'),
('console_add_admin', 'ajouter', 'add'),
('console_add_admin_confirmation', 'Le nouvel administrateur a �t� correctement cr��.', 'The new administrator has been successfully created.'),
('console_add_admin_header', 'Cr�ation d''un nouvel administrateur : ', 'Create a new administrator : '),
('console_change_admin_permission_header', 'Choisissez l''administrateur dont vous d�sirez modifier les permissions :', 'Choose the administrator for a permission modification :'),
('console_compilation_confirmation', 'La compilation s''est termin�e correctement.', 'The compilation has successfully terminated.'),
('console_compiler_new_object', 'ajouter', 'add'),
('console_compiler_remove_object', 'supprimer', 'remove'),
('console_config_modify_info', 'Configuration de la console EasyDev : ', 'EasyDev console configuration :'),
('console_config_modif_confirmation', 'La configuration a �t� modifi�e correctement.', 'The configuration has been successfully updated.'),
('console_duplicate_admin_username_error', 'Un administrateur avec ce nom existe d�j�.', 'An administrator with this name already exists.'),
('console_easydev_compiler', 'Compilateur Easydev', 'Easydev compiler'),
('console_easydev_config', 'Configurer EasyDev', 'EasyDev configuration'),
('console_easydev_configure', 'configuration', 'configuration'),
('console_edit_admin_permissions', 'permissions', 'permissions'),
('console_foreign_key_constraint_delete_explanations', 'Ces classes sont la cible d''une relation 1:N. Afin d''�viter des probl�mes de coh�rence, elles ne peuvent �tre supprim�es tant que la classe qui contient la relation 1:N qui pointe dessus n''est pas supprim�e.', 'These classes are targets of a 1:N relation. To avoid any consistency problem, they cannot be deleted unless the class containing the relation 1:N pointing on it is deleted.'),
('console_help', 'Aide d''EasyDev', 'EasyDev help'),
('console_index_login_title', 'Bienvenue dans la console EasyDev.', 'Welcome on EasyDev console.'),
('console_linking_table_constraint_delete_explanations', 'Ces classes contiennent des relations N:M. Ces relations seront perdues si vous supprimez l''un ou l''autre des objets de la relation.', 'These classes contain N:M relations. These relations will be lost if you delete one of the classes involved in the relation.'),
('console_log', 'Log d''EasyDev', 'EasyDev logs'),
('console_main_default_content', 'Bienvenue sur la console de d�veloppement EasyDev. ', 'Welcome on the EasyDev development tool.'),
('console_permission_admin_change', 'Changement des permissions de l''administrateur : ', 'Change permissions for the administrator : '),
('console_permission_admin_confirmation', 'Les modifications de permissions ont �t� enregistr�es correctement.', 'The changes on permissions has been successfully saved.'),
('console_personal_info', 'Informations personnelles', 'Personal informations'),
('console_remove_admin', 'supprimer', 'remove'),
('console_remove_admin_confirmation', 'L''administrateur a �t� correctement supprim�. ', 'The administrator has been successfully deleted.'),
('console_remove_admin_header', 'Si vous supprimez un administrateur, ses permissions ainsi que ses informations personnelles seront d�finitivement supprim�s. Les actions dans les logs qu''il a effectu�es seront quant � elles conserv�es. Si vous d�sirez conserver les informations personnelles de l''administrateur, il est pr�f�rable de lui retirer toutes les permissions.', 'If you delete any administrator, his permissions and personal information will be deleted. The logs of his actions are not deleted. If you want to keep his personal informations, you should just delete all his permissions.'),
('console_remove_objects_header', 'La suppression d''une classe supprime tout les scripts g�n�r�s lors de la compilation, tous les objets se trouvant dans la base de donn�es ainsi que la table de la classe.', 'The suppression of a class deletes any script generated by the compilation, any object entered in the database, and the table defining the class.'),
('console_superadmin', 'Gestion des administrateurs', 'Manage administrators'),
('console_title', 'EasyDev - Module de d�veloppement de base de donn�es en PHP.', 'EasyDev - Development module for databases with PHP.'),
('console_too_short_password_error', 'Le mot de passe doit avoir au minimum 6 caract�res.', 'The password must contains at least 6 characters.'),
('console_username_error', 'Le nom de l''administrateur ne doit pas contenir de caract�res sp�ciaux.', 'The administrator name must not contains special chars.'),
('console_view_admin_logs', 'voir les logs', 'view logs'),
('console_wrong_confirm_password_error', 'Le mot de passe et la confirmation ne correspondent pas.', 'The password and the confirmation do not match.'),
('default_language', 'langue par d�faut', 'default language'),
('delete', 'supprimer', 'delete'),
('expected', 'attendu', 'expected'),
('found', 'trouv�', 'found'),
('generator_add_object_expected_double', 'Valeur num�rique attendue pour le champs : ', 'Numeric value expected for field : '),
('generator_add_object_expected_integer', 'Valeur num�rique enti�re attendue pour le champs : ', 'Numeric value expected for field : '),
('generator_confirm_insert', 'L''objet a �t� correctement ins�r� dans la base de donn�es.', 'The object has been successfully added to the database.'),
('generator_confirm_modify', 'Les changements pour l''objet ont �t� enregistr�s correctement dans la base de donn�es.', 'The modifications on the object has been successfully saved in database.'),
('language', 'langue', 'language'),
('log_out', 'deconnexion', 'log out'),
('name', 'nom', 'name'),
('password', 'mot de passe', 'password'),
('submit', 'envoyer', 'submit'),
('update', 'modifier', 'update'),
('username', 'nom d''utilisateur', 'username'),
('version', 'version', 'version');

-- 
-- Constraints for dumped tables
-- 

-- 
-- Constraints for table `adminsub`
-- 
ALTER TABLE `adminsub`
  ADD CONSTRAINT `adminsub_ibfk_1` FOREIGN KEY (`id_mainmenu`) REFERENCES `adminmain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `easydev_objects`
-- 
ALTER TABLE `easydev_objects`
  ADD CONSTRAINT `easydev_objects_ibfk_1` FOREIGN KEY (`id_mainmenu`) REFERENCES `adminmain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `easydev_objects_foreign_key`
-- 
ALTER TABLE `easydev_objects_foreign_key`
  ADD CONSTRAINT `easydev_objects_foreign_key_ibfk_1` FOREIGN KEY (`id_object`) REFERENCES `easydev_objects` (`id_mainmenu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `easydev_objects_foreign_key_ibfk_2` FOREIGN KEY (`id_foreign_object`) REFERENCES `easydev_objects` (`id_mainmenu`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `easydev_objects_linking_tables`
-- 
ALTER TABLE `easydev_objects_linking_tables`
  ADD CONSTRAINT `easydev_objects_linking_tables_ibfk_1` FOREIGN KEY (`id_objet1`) REFERENCES `easydev_objects` (`id_mainmenu`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `easydev_objects_linking_tables_ibfk_2` FOREIGN KEY (`id_objet2`) REFERENCES `easydev_objects` (`id_mainmenu`) ON DELETE CASCADE ON UPDATE CASCADE;

-- 
-- Constraints for table `permission_admins`
-- 
ALTER TABLE `permission_admins`
  ADD CONSTRAINT `permission_admins_ibfk_1` FOREIGN KEY (`id_admin`) REFERENCES `authorized_admins` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `permission_admins_ibfk_2` FOREIGN KEY (`id_mainsection`) REFERENCES `adminmain` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
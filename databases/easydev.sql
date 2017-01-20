-- phpMyAdmin SQL Dump
-- version 2.10.1
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Jan 20, 2017 at 12:19 PM
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
  `text` varchar(255) collate utf8_general_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `text` (`text`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=10 ;

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
  `text` varchar(255) collate utf8_general_ci NOT NULL,
  `url` varchar(255) collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `id_mainmenu` (`id_mainmenu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=20 ;

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
(9, 4, 'console_view_admin_logs', 'logsadmin'),
(10, 6, 'console_compiler_regen_scripts', 'regenerate');

-- --------------------------------------------------------

-- 
-- Table structure for table `authorized_admins`
-- 

CREATE TABLE `authorized_admins` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(255) collate utf8_general_ci NOT NULL,
  `password` varchar(255) collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=5 ;

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
  `id` varchar(25) collate utf8_general_ci NOT NULL,
  `value` varchar(255) collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- 
-- Dumping data for table `configuration`
-- 

INSERT INTO `configuration` (`id`, `value`) VALUES 
('default_language', 'fr'),
('version', '2.3');

-- --------------------------------------------------------

-- 
-- Table structure for table `easydev_objects`
-- 

CREATE TABLE `easydev_objects` (
  `id_mainmenu` int(10) unsigned NOT NULL,
  `name` varchar(50) collate utf8_general_ci NOT NULL,
  `definition` text collate utf8_general_ci,
  PRIMARY KEY  (`id_mainmenu`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  `relationname` varchar(255) collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id_object`,`id_foreign_object`,`relationname`),
  KEY `id_foreign_object` (`id_foreign_object`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  `table_name` varchar(64) collate utf8_general_ci NOT NULL,
  `relationname` varchar(255) collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id_objet1`,`id_objet2`,`table_name`),
  KEY `id_objet2` (`id_objet2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- 
-- Dumping data for table `easydev_objects_linking_tables`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `logs`
-- 

CREATE TABLE `logs` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `log` varchar(255) collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=1 ;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
  `language` varchar(50) collate utf8_general_ci NOT NULL,
  `tag` varchar(2) collate utf8_general_ci NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tag` (`tag`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `translation_languages`
-- 

INSERT INTO `translation_languages` (`id`, `language`, `tag`) VALUES 
(1, 'français', 'fr'),
(2, 'english', 'en');

-- --------------------------------------------------------
--
-- Structure de la table `translation_strings`
--


CREATE TABLE `translation_strings` (
  `keyword` varchar(64) collate utf8_general_ci NOT NULL,
  `fr` text collate utf8_general_ci,
  `en` text collate utf8_general_ci,
  PRIMARY KEY  (`keyword`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Contenu de la table `translation_strings`
--


INSERT INTO `translation_strings` (`keyword`, `fr`, `en`) VALUES
('back_to_list_page', 'Revenir à la liste des objets', 'Back to the object list'),
('cancel', 'annuler', 'cancel'),
('change_permission', 'changer les permissions', 'change permissions'),
('compile', 'compiler', 'compile'),
('compile_add_object_sub_menu_title', 'ajouter', 'add'),
('compile_autogenerated_page_exist_error', 'Un script généré par la compilation existe déjà. Contactez la personne en charge de l''installation de la console EasyDev.', 'One of the compiler-generated scripts already exists. Contact the person in charge of the EasyDev console.'),
('compile_character_error', 'Des caractères non permis sont contenus dans la définition de l''objet. Les caractères admissibles sont les chiffres, les lettres majuscules et minuscules ainsi que les signes _,;"(){}.', 'Some characters in the definition of the object are not allowed. The allowed characters are numbers, lower and upper case letters and _,;"(){} special signs.'),
('compile_database_duplicate_object_error', 'Un des objets existe déjà dans la base de données.', 'One of the objects already exists in database.'),
('compile_datetime_conflicting_suffix_field', 'Aucun champ ne peut porter comme nom la concaténation de "date", "hour" ou "mins" avec le nom d''un champ de type datetime,  un conflit a été détecté pour le champ ', 'No field can have as name the words "date", "hour" or "mins" appended to the name of a datetime field, a conflict has been detected for the field '),
('compile_delete_object_sub_menu_title', 'objets existants', 'existing objects'),
('compile_duplicate_field_name_error', 'Ce nom de champs ne peut être utilisé pour définir plusieurs champs : ', 'This field name cannot be used to define several fields : '),
('compile_duplicate_object_error', 'Deux objets portant le même nom sont interdits.', 'Two objects with the same name are not allowed.'),
('compile_duplicate_relation_error', 'Deux relations de même nom portant sur le même objet sont interdites.', 'Two relations with same names on the same object are not allowed.'),
('compile_duplicate_sql_function_error', 'Ce nom de finder ou d''updater ne peut être utilisé pour deux fonctions différentes :', 'This finder or updater name cannot be reused for several different functions : '),
('compile_expected_identifier', 'identifiant attendu', 'identifier expected'),
('compile_file_conflicting_suffix_field', 'Aucun champ ne peut porter comme nom la concaténation de "_server_temp_file" ou "_delete_flag" avec le nom d''un champ de type file,  un conflit a été détecté pour ', 'No field can have as name "_server_temp_file" or "_delete_flag" appended to the name of a file field, a conflict has been detected for '),
('compile_fopen_pointer_error', 'L''ouverture d''un fichier en écriture sur le disque n''a pu être faite. Contactez la personne en charge de la console EasyDev.', 'A file opening on disc for writing was aborted. Contact the person in charge of the EasyDev console.'),
('compile_identifier_error', 'Identifiant non permis : ', 'Not-allowed identifier encountered : '),
('compile_image_conflicting_suffix_field', 'Aucun champ ne peut porter comme nom la concaténation de "_server_temp_file" ou "_delete_flag" avec le nom d''un champ de type image,  un conflit a été détecté pour le champ ', 'No field can have as name  "_server_temp_file" or "_delete_flag" appended to the name of an image field, a conflict has been detected for the field '),
('compile_mysql_reserved_token_error', 'Un ou plusieurs identifiant rencontré fait partie de la liste des mots réservés pour MySQL (http://dev.mysql.com/doc/refman/5.1/en/reserved-words.html). Veuillez supprimer ces mots de votre compilation.', 'One or more identifiers are MySQL reserved words (http://dev.mysql.com/doc/refman/5.1/en/reserved-words.html). Please remove any of these words from your compilation.'),
('compile_no_class_def_found_error', 'Aucune définition de classe n''a été trouvée.', 'No class definition found.'),
('compile_no_field_def_found_error', 'Cette classe ne contient pas de définition de champs :', 'This class do not contains any field definition : '),
('compile_password_conflicting_suffix_field', 'Aucun champ ne peut porter comme nom la concaténation de "_confirmation" ou "_hashed" avec le nom d''un champ de type password,  un conflit a été détecté pour le champ ', 'No field can have as name "_confirmation" or "_hashed" appended to the name of a password field, a conflict has been detected for the field '),
('compile_password_nullable_error', 'Les champs de type "password" ne peuvent pas être défini comme "nullable", une erreur a été détectée pour le champ ', 'The "password" fields can not be defined as "nullable", an error has been encountered for field '),
('compile_relationnm_not_recursive_error', 'Cette relation n''a pas pu être identifiée réciproquement dans l''objet distant : ', 'This relation could not be verified in the foreign object : '),
('compile_relation_unknown_object_error', 'L''objet cible n''a pas été trouvé pour la relation : ', 'The relation target object has not been found for : '),
('compile_self_relation_error', 'Aucune relation n''est permise avec l''objet dans lequel est définie la relation.', 'No relation is allowed with the object in which the relation is defined.'),
('compile_token_mismatch_error', 'Token inattendu rencontré.', 'Unexpected token error.'),
('compile_unexpected_token_error', 'Un token inattendu a été rencontré en fin de définition d''objet : ', 'An unexpected token has been encountered at the end of a class definition : '),
('connect', 'se connecter', 'log in'),
('console_add_admin', 'ajouter', 'add'),
('console_add_admin_confirmation', 'Le nouvel administrateur a été correctement créé.', 'The new administrator has been successfully created.'),
('console_add_admin_header', 'Création d''un nouvel administrateur : ', 'Create a new administrator : '),
('console_change_admin_permission_header', 'Choisissez l''administrateur dont vous désirez modifier les permissions :', 'Choose the administrator for a permission modification :'),
('console_compilation_confirmation', 'La compilation s''est terminée correctement.', 'The compilation has successfully terminated.'),
('console_compiler_new_object', 'ajouter', 'add'),
('console_compiler_regen_scripts', 'regénérer', 'script update'),
('console_compiler_remove_object', 'supprimer', 'remove'),
('console_config_modify_info', 'Configuration de la console EasyDev : ', 'EasyDev console configuration :'),
('console_config_modif_confirmation', 'La configuration a été modifiée correctement.', 'The configuration has been successfully updated.'),
('console_duplicate_admin_username_error', 'Un administrateur avec ce nom existe déjà.', 'An administrator with this name already exists.'),
('console_easydev_compiler', 'Compilateur Easydev', 'Easydev compiler'),
('console_easydev_config', 'Configurer EasyDev', 'EasyDev configuration'),
('console_easydev_configure', 'configuration', 'configuration'),
('console_edit_admin_permissions', 'permissions', 'permissions'),
('console_foreign_key_constraint_delete_explanations', 'Ces classes sont la cible d''une relation 1:N. Afin d''éviter des problèmes de cohérence, elles ne peuvent être supprimées tant que la classe qui contient la relation 1:N qui pointe dessus n''est pas supprimée.', 'These classes are targets of a 1:N relation. To avoid any consistency problem, they cannot be deleted unless the class containing the relation 1:N pointing on it is deleted.'),
('console_help', 'Aide d''EasyDev', 'EasyDev help'),
('console_index_login_title', 'Bienvenue dans la console EasyDev.', 'Welcome on EasyDev console.'),
('console_linking_table_constraint_delete_explanations', 'Ces classes contiennent des relations N:M. Ces relations seront perdues si vous supprimez l''un ou l''autre des objets de la relation.', 'These classes contain N:M relations. These relations will be lost if you delete one of the classes involved in the relation.'),
('console_log', 'Log d''EasyDev', 'EasyDev logs'),
('console_main_default_content', 'Bienvenue sur la console de développement EasyDev. ', 'Welcome on the EasyDev development tool.'),
('console_permission_admin_change', 'Changement des permissions de l''administrateur : ', 'Change permissions for the administrator : '),
('console_permission_admin_confirmation', 'Les modifications de permissions ont été enregistrées correctement.', 'The changes on permissions has been successfully saved.'),
('console_personal_info', 'Informations personnelles', 'Personal informations'),
('console_regeneration_confirmation', 'La regénération des scripts a été effectuée correctement.', 'The script update was performed successfully.'),
('console_regeneration_link_text', 'regénérer les scripts', 'update the scripts'),
('console_regeneration_page_title', 'La regénération des scripts permet de réécrire les scripts sur le disque après un changement de version mineure de l''application EasyDev. La regénération se fait en choisissant une compilation effectuée auparavant dans l''application EasyDev.', 'The script update function let you refresh the scripts on the disc after a version update of the EasyDev application. The script update is performed by compilation made on the EasyDev console.'),
('console_remove_admin', 'supprimer', 'remove'),
('console_remove_admin_confirmation', 'L''administrateur a été correctement supprimé. ', 'The administrator has been successfully deleted.'),
('console_remove_admin_header', 'Si vous supprimez un administrateur, ses permissions ainsi que ses informations personnelles seront définitivement supprimés. Les actions dans les logs qu''il a effectuées seront quant à elles conservées. Si vous désirez conserver les informations personnelles de l''administrateur, il est préférable de lui retirer toutes les permissions.', 'If you delete any administrator, his permissions and personal information will be deleted. The logs of his actions are not deleted. If you want to keep his personal informations, you should just delete all his permissions.'),
('console_remove_easydev_object_confirmation', 'Suppression de l''objet effectuée correctement.', 'Removing the object has been done successfully.'),
('console_remove_objects_header', 'La suppression d''une classe supprime tout les scripts générés lors de la compilation, tous les objets se trouvant dans la base de données ainsi que la table de la classe.', 'The suppression of a class deletes any script generated by the compilation, any object entered in the database, and the table defining the class.'),
('console_superadmin', 'Gestion des administrateurs', 'Manage administrators'),
('console_title', 'EasyDev - Module de développement de base de données en PHP.', 'EasyDev - Development module for databases with PHP.'),
('console_too_short_password_error', 'Le mot de passe doit avoir au minimum 6 caractères.', 'The password must contains at least 6 characters.'),
('console_username_error', 'Le nom de l''administrateur ne doit pas contenir de caractères spéciaux.', 'The administrator name must not contains special chars.'),
('console_view_admin_logs', 'voir les logs', 'view logs'),
('console_wrong_confirm_password_error', 'Le mot de passe et la confirmation ne correspondent pas.', 'The password and the confirmation do not match.'),
('create_relations', 'Créer les relations', 'Create relations'),
('default_language', 'langue par défaut', 'default language'),
('delete', 'supprimer', 'delete'),
('delete_image', 'supprimer cette image', 'delete this image'),
('expected', 'attendu', 'expected'),
('filter', 'filtrer', 'filter'),
('found', 'trouvé', 'found'),
('generator_add_object_boolean_unset', 'Valeur booléenne (vrai ou faux) attendue pour le champs : ', 'Boolean value (true or false) expected for field : '),
('generator_add_object_date_format_error', 'Le format de la date est incorrect. Veuillez insérer une date au format aaaa-mm-jj ou utilisez le calendrier visuel pour le champ : ', 'Date format is incorrect. Please enter date in yyyy-mm-dd format or use the visual calendar for the field : '),
('generator_add_object_empty_text_error', 'Veuillez insérer un texte pour le champ ', 'Please insert a text in the field '),
('generator_add_object_expected_double', 'Valeur numérique attendue pour le champs : ', 'Numeric value expected for field : '),
('generator_add_object_expected_integer', 'Valeur numérique entière attendue pour le champs : ', 'Numeric value expected for field : '),
('generator_add_object_file_html_too_large_file', 'Le fichier que vous avez envoyé dépasse la taille maximale définie pour le formulaire, veuillez réduire la taille du fichier ', 'The uploaded file is larger than the maximum size defined for the form, please reduce the size of file '),
('generator_add_object_file_no_extension', 'Un fichier sans extension ne peut être envoyé pour le champ ', 'A file without extension cannot been sent for field '),
('generator_add_object_file_no_file', 'Un fichier doit être insérer pour le champ ', 'Please insert a file for field '),
('generator_add_object_file_no_such_file', 'Aucun fichier n''a pu être vérifié pour le champ ', 'No file could be verified for the field '),
('generator_add_object_file_no_tmp_dir', 'Une erreur est survenue lors de la tentative d''écriture dans le dossier temporaire du serveur pour le fichier ', 'An error occurred while trying to write in the temporary directory of the server for the file '),
('generator_add_object_file_partial_file', 'Une erreur de transfert est survenue et le fichier n''a été que partiellement envoyée, veuillez réessayer l''envoi pour le fichier ', 'An upload error has occurred and the file has only be partially uploaded, please retry to send the file '),
('generator_add_object_file_too_large_file', 'Le fichier que vous avez envoyé dépasse la taille maximale définie pour le serveur, veuillez réduire la taille du fichier ', 'The uploaded file is larger than the maximum size defined for the server, please reduce the size of file '),
('generator_add_object_file_unknown_err', 'Une erreur inconnue est survenue pendant le transfert du fichier ', 'An unknown error has occurred during upload of the file '),
('generator_add_object_hour_format_error', 'Veuillez sélectionner une heure pour le champ : ', 'Please insert an hour value for the field : '),
('generator_add_object_image_bad_type', 'Les formats d''image acceptés sont les JPG, PNG et GIF, un format non supporté a été trouvé pour le champ ', 'The accepted image formats are JPG, PNG and GIF, an unsupported format has been found for field '),
('generator_add_object_image_format_error', 'Un format de fichier ne correspondant pas à une image a été trouvé pour le champ ', 'A file format that do not correspond to an image has been found for field '),
('generator_add_object_image_html_too_large_file', 'L''image que vous avez envoyé dépasse la taille maximale définie pour le formulaire, veuillez réduire la taille du fichier ', 'The uploaded image is larger than the maximum size defined for the form, please reduce the size of file '),
('generator_add_object_image_no_file', 'Un fichier de type image doit être insérer pour le champ ', 'Please insert an image file for field '),
('generator_add_object_image_no_such_file', 'Aucun fichier n''a pu être vérifié pour le champ ', 'No file could be verified for the field '),
('generator_add_object_image_no_tmp_dir', 'Une erreur est survenue lors de la tentative d''écriture dans le dossier temporaire du serveur pour l''image ', 'An error occurred while trying to write in the temporary directory of the server for the image '),
('generator_add_object_image_partial_file', 'Une erreur de transfert est survenue et l''image n''a été que partiellement envoyée, veuillez réessayer l''envoi pour l''image ', 'An upload error has occurred and the image has only be partially uploaded, please retry to send the image '),
('generator_add_object_image_too_large_file', 'L''image que vous avez envoyé dépasse la taille maximale définie pour le serveur, veuillez réduire la taille du fichier ', 'The uploaded image is larger than the maximum size defined for the server, please reduce the size of file '),
('generator_add_object_image_unknown_err', 'Une erreur inconnue est survenue pendant le transfert de l''image ', 'An unknown error has occurred during upload of the image '),
('generator_add_object_mins_format_error', 'Veuillez insérer une valeur pour les minutes du champ : ', 'Please insert a value for the minutes of the field : '),
('generator_add_object_pass_and_confirm_not_identical_error', 'Le mot de passe et sa confirmation doivent être identique pour le champ : ', 'The password and its confirmation must be equal for the field : '),
('generator_add_object_pass_length_error', 'Ce mot de passe doit contenir au moins 8 caractères : ', 'This password should contains at least 8 characters : '),
('generator_add_object_pass_unset_error', 'Veuillez indiquer un mot de passe pour le champ ', 'Please enter a password for the field '),
('generator_add_page_submit_button', 'Ajouter', 'Add'),
('generator_add_page_title', 'Ajouter un objet dans la base de données', 'Add an object into the database'),
('generator_confirm_insert', 'L''objet a été correctement inséré dans la base de données.', 'The object has been successfully added to the database.'),
('generator_confirm_modify', 'Les changements pour l''objet ont été enregistrés correctement dans la base de données.', 'The modifications on the object has been successfully saved in database.'),
('generator_delete_page_filter_title', 'Filtrer les objets par relation 1:N : ', 'Filter objects with the 1:N relations : '),
('generator_delete_page_title', 'Liste des objets : ', 'List of the objects : '),
('generator_modify_page_submit_button', 'Modifier', 'Modify'),
('generator_modify_page_title', 'Modifier un objet', 'Modify an object'),
('generator_nm_rel_page_main_title', 'Choix de l''objet auquel modifier des relations', 'Choice of the target object for the relations modifications'),
('generator_nm_rel_page_second_title', 'Modifications des relations sur l''objet sélectionné', 'Modifications on the relations of the selected object'),
('language', 'langue', 'language'),
('log_out', 'deconnexion', 'log out'),
('modify', 'modifier', 'modify'),
('name', 'nom', 'name'),
('password', 'mot de passe', 'password'),
('password_confirmation', 'confirmation du mot de passe', 'password confirmation'),
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

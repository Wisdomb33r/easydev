INSERT INTO `translation_strings` (`keyword`, `fr`, `en`) VALUES
('compile_max_string_fields_number_error', 'Nombre maximal de champs de type string dépassé dans la classe : ', 'Max number of string fields exceeded in class :'),
('generator_add_object_date_empty_error', 'Champ date obligatoire : ', 'Mandatory date field : '),
('generator_add_object_image_too_width', 'Largeur maximale dépassée pour l''image : ', 'Max width exceeded for image : '),
('generator_add_object_image_too_height', 'Hauteur maximale dépassée pour l''image : ', 'Max height exceeded for image : ');
UPDATE `configuration` SET `value` = '2.4' WHERE `configuration`.`id` = 'version';
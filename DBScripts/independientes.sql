SELECT * FROM independientes.meta;CREATE TABLE `meta` (
  `id_config` int(11) NOT NULL AUTO_INCREMENT,
  `padron` int(11) DEFAULT NULL,
  `meta_padron` decimal(4,2) DEFAULT NULL,
  `num_secciones` int(11) DEFAULT NULL,
  `meta_secciones` decimal(4,2) DEFAULT NULL,
  `descripcion` varchar(45) DEFAULT NULL,
  `candidato` varchar(45) DEFAULT NULL,
  `nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`id_config`),
  UNIQUE KEY `candidatura_UNIQUE` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;


CREATE TABLE `colonia` (
  `id_colonia` int(11) NOT NULL AUTO_INCREMENT,
  `clave` int(11) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  `id_seccion` int(11) NOT NULL,
  PRIMARY KEY (`id_colonia`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `registro` (
  `id_registro` int(11) NOT NULL AUTO_INCREMENT,
  `clave_elector` varchar(19) DEFAULT NULL,
  `ocr` varchar(13) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `cel` varchar(45) DEFAULT NULL,
  `id_seccion` int(11) NOT NULL,
  `id_colonia` int(11) NOT NULL,
  `folio` varchar(6) DEFAULT NULL,
  `descripcion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_registro`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `registro_error` (
  `id_registro` int(11) NOT NULL AUTO_INCREMENT,
  `clave_elector` varchar(19) DEFAULT NULL,
  `ocr` varchar(13) DEFAULT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `cel` varchar(45) DEFAULT NULL,
  `seccion` varchar(6) DEFAULT NULL,
  `colonia` varchar(6) DEFAULT NULL,
  `folio` varchar(6) DEFAULT NULL,
  `descripcion` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_registro`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `seccion` (
  `id_seccion` int(11) NOT NULL AUTO_INCREMENT,
  `clave` int(11) NOT NULL,
  `nombre` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id_seccion`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(45) NOT NULL,
  `password` varchar(45) NOT NULL,
  `db` varchar(45) DEFAULT NULL,
  `rol` tinyint(4) NOT NULL,
  `empresa` varchar(45) DEFAULT NULL,
  `nombre` varchar(45) NOT NULL,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;

INSERT INTO `independientes_master`.`usuario`
(`id_usuario`,
`email`,
`password`,
`db`,
`rol`,
`nombre`)
VALUES
(1,
'ramiro',
'ramiro',
'independientes_ramiro',
1,
'Ramiro Jiménez'),
(1,
'emilio',
'emilio',
'independientes_emilio',
1,
'Emilio Sol');






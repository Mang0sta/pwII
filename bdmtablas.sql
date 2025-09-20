CREATE DATABASE db_users;
USE db_users;
SHOW TABLES;
DROP DATABASE db_users;

-- MySQL dump 10.13  Distrib 8.0.40, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: db_users
-- ------------------------------------------------------
-- Server version	8.0.40

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `a`
--

DROP TABLE IF EXISTS `a`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `a` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(45) NOT NULL,
  `apellidos` varchar(45) DEFAULT NULL,
  `genero` enum('hombre','mujer') DEFAULT NULL,
  `email` varchar(45) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `cumpleanos` date DEFAULT NULL,
  `rol` enum('usuario','admin') DEFAULT 'usuario',
  `fotoNombre` varchar(100) DEFAULT NULL,
  `fotoRuta` varchar(255) DEFAULT NULL,
  `firebase_uid` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_firebase_uid` (`firebase_uid`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `a`
--


LOCK TABLES `a` WRITE;
/*!40000 ALTER TABLE `a` DISABLE KEYS */;
INSERT INTO `a` VALUES (8,'aaaaad','Qdds','mujer','ejemplo2@gmail.com','11','2025-03-01','admin',NULL,NULL,NULL),(21,'hola','holafv','hombre','hola@gmail.com','$2y$10$Q/4NvTKWHBJ2HuLHcLkph.r.NYLhZdiqcCD4TjqNDiHy3/5rj/1P.','2025-04-09','usuario',NULL,NULL,NULL),(37,'bb','bb','hombre','bb@gmail.com','12345678aA$','2025-04-09','usuario','0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg','uploads/profile_pics/680f47562605e_0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg',NULL),(39,'xx','xx','hombre','xx@gmail.com','12345678aA$','2025-04-04','usuario','2.Circle-the-Dots.jpg','uploads/profile_pics/6812f92ab94df_2.Circle-the-Dots.jpg',NULL),(40,'cc','cc','hombre','cc@gmail.com','12345678aA$','2025-04-09','usuario','420756545_10233894434928939_5994798341606864059_n.jpg','uploads/profile_pics/68109548cbf4c_420756545_10233894434928939_5994798341606864059_n.jpg',NULL),(41,'dds','dd','hombre','dd@gmail.com','12345678aA$','2025-04-10','usuario','31 minutos miku.jpg','uploads/profile_pics/681099971384e_31 minutos miku.jpg',NULL),(42,'kka','kk','hombre','kk@gmail.com','12345678aA$','2025-04-10','usuario','422ba474a0506609819fb9887b13581f.jpg','uploads/profile_pics/68146135e4d2d_422ba474a0506609819fb9887b13581f.jpg',NULL),(43,'qq','qq','hombre','qq@gmail.com','12345678aA$','2025-04-09','admin','422ba474a0506609819fb9887b13581f.jpg','uploads/profile_pics/6813069a5a234_422ba474a0506609819fb9887b13581f.jpg',NULL),(44,'ff','ff','hombre','ff@gmail.com','12345678aA$','2025-04-09','usuario','31 minutos miku.jpg','uploads/profile_pics/68161ce78a552_31 minutos miku.jpg',NULL),(46,'uu','uu','hombre','uu@gmail.com','123456789qwertyQ#$','2025-04-09','usuario','0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg','uploads/profile_pics/6811fdc220386_0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg',NULL),(47,'aa','aa','hombre','aa@gmail.com','12345678aA$','2025-04-02','usuario','0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg','uploads/profile_pics/6812fa761f990_0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg',NULL),(48,'dddd','ddddd','mujer','prueba@gmail.com','12345678aA$','2025-04-01','usuario','0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg','uploads/profile_pics/6815ff49a1905_0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg',NULL),(49,'saas','saas','hombre','saas@gmail.com','12345678aA$','2025-05-02','usuario','31 minutos miku.jpg','uploads/profile_pics/6819d6df29341_31 minutos miku.jpg','m5is54T2koUaXBKuqmI2047LAIx2'),(51,'prueba2','prueba2','hombre','prueba2@gmail.com','12345678aA$','2025-04-30','usuario','0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg','uploads/profile_pics/6819cb8a72d78_0c0637d791902983ca683dc2a91dda58cad19683288379c35c411989222d468b.jpg',NULL),(52,'ejemplo3','ejemplo3','hombre','ejemplo3@gmail.com','12345678aA$','2025-05-02','usuario','357162593_756122082922674_1249516776128530287_n.jpg','uploads/profile_pics/6819da93b49e8_357162593_756122082922674_1249516776128530287_n.jpg',NULL);
/*!40000 ALTER TABLE `a` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nombre` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (2,'Arte'),(4,'Deportes'),(3,'Música'),(1,'Noticias'),(5,'Tecnología');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comentarios`
--

DROP TABLE IF EXISTS `comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comentarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `comentario_text` text NOT NULL,
  `tiempo_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tiempo_edicion` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `comentarios_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comentarios_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `a` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comentarios`
--



LOCK TABLES `comentarios` WRITE;
/*!40000 ALTER TABLE `comentarios` DISABLE KEYS */;
INSERT INTO `comentarios` VALUES (34,77,51,'hola','2025-05-06 08:42:15',NULL),(36,77,49,'hola','2025-05-06 09:36:35',NULL);
/*!40000 ALTER TABLE `comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes`
--

DROP TABLE IF EXISTS `likes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes` (
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`,`post_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `a` (`id`),
  CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `publicaciones` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes`
--

LOCK TABLES `likes` WRITE;
/*!40000 ALTER TABLE `likes` DISABLE KEYS */;
INSERT INTO `likes` VALUES (49,77,'2025-05-06 09:32:15'),(49,78,'2025-05-06 09:32:13'),(51,77,'2025-05-06 08:42:11');
/*!40000 ALTER TABLE `likes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `likes_comentarios`
--

DROP TABLE IF EXISTS `likes_comentarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `likes_comentarios` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `comentario_id` int NOT NULL,
  `tiempo` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_like` (`user_id`,`comentario_id`),
  KEY `comentario_id` (`comentario_id`),
  CONSTRAINT `likes_comentarios_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `a` (`id`) ON DELETE CASCADE,
  CONSTRAINT `likes_comentarios_ibfk_2` FOREIGN KEY (`comentario_id`) REFERENCES `comentarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `likes_comentarios`
--

LOCK TABLES `likes_comentarios` WRITE;
/*!40000 ALTER TABLE `likes_comentarios` DISABLE KEYS */;
INSERT INTO `likes_comentarios` VALUES (24,49,34,'2025-05-06 09:32:22');
/*!40000 ALTER TABLE `likes_comentarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mensajes`
--

DROP TABLE IF EXISTS `mensajes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `mensajes` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL,
  `receiver_id` int NOT NULL,
  `contenido` text NOT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `tipo` enum('texto','imagen') NOT NULL DEFAULT 'texto',
  `ruta_archivo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sender_id` (`sender_id`),
  KEY `receiver_id` (`receiver_id`),
  CONSTRAINT `mensajes_ibfk_1` FOREIGN KEY (`sender_id`) REFERENCES `a` (`id`),
  CONSTRAINT `mensajes_ibfk_2` FOREIGN KEY (`receiver_id`) REFERENCES `a` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `mensajes`
--

LOCK TABLES `mensajes` WRITE;
/*!40000 ALTER TABLE `mensajes` DISABLE KEYS */;
INSERT INTO `mensajes` VALUES (1,47,39,'aa','2025-05-05 06:16:34','texto',NULL),(2,47,39,'hola','2025-05-05 06:16:49','texto',NULL),(3,39,47,'hey','2025-05-05 06:19:13','texto',NULL),(4,47,39,'d','2025-05-05 06:21:03','texto',NULL),(5,47,39,'s','2025-05-05 06:48:54','texto',NULL),(6,47,39,'d','2025-05-05 06:49:02','texto',NULL),(7,47,39,'s','2025-05-05 06:49:21','texto',NULL),(8,39,47,'a','2025-05-05 06:49:53','texto',NULL),(9,47,39,'a','2025-05-05 06:57:56','texto',NULL),(10,47,39,'s','2025-05-05 06:59:23','texto',NULL),(11,39,47,'a','2025-05-05 07:00:37','texto',NULL),(12,39,47,'s','2025-05-05 07:00:45','texto',NULL),(13,47,8,'a','2025-05-05 07:01:49','texto',NULL),(14,47,8,'a','2025-05-05 07:01:56','texto',NULL),(15,47,8,'b','2025-05-05 07:02:10','texto',NULL),(16,47,8,'s','2025-05-05 07:03:56','texto',NULL),(17,47,8,'a','2025-05-05 07:05:10','texto',NULL),(18,47,8,'c','2025-05-05 07:05:22','texto',NULL),(19,39,47,'s','2025-05-05 07:07:11','texto',NULL),(20,39,47,'ss','2025-05-05 07:07:34','texto',NULL),(21,47,21,'s','2025-05-05 07:09:46','texto',NULL),(22,47,8,'ss','2025-05-05 07:10:57','texto',NULL),(23,47,21,'a','2025-05-05 07:12:05','texto',NULL),(24,47,21,'d','2025-05-05 07:12:58','texto',NULL),(25,47,21,'a','2025-05-05 07:15:13','texto',NULL),(26,47,21,'ds','2025-05-05 07:15:37','texto',NULL),(27,47,39,'aa','2025-05-05 07:18:18','texto',NULL),(28,47,8,'a','2025-05-05 07:18:47','texto',NULL),(29,37,47,'aa','2025-05-05 07:26:26','texto',NULL),(30,37,21,'s','2025-05-05 08:06:36','texto',NULL),(31,37,21,'s','2025-05-05 08:07:42','texto',NULL),(32,37,21,'s','2025-05-05 08:09:19','texto',NULL),(33,47,37,'a','2025-05-05 08:42:03','texto',NULL),(34,47,37,'a','2025-05-05 08:42:09','texto',NULL),(35,37,21,'s','2025-05-05 08:44:18','texto',NULL),(36,37,47,'s','2025-05-05 08:44:25','texto',NULL);
/*!40000 ALTER TABLE `mensajes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `publicaciones`
--

DROP TABLE IF EXISTS `publicaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `publicaciones` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `titulo` varchar(255) DEFAULT NULL,
  `categoria_id` int DEFAULT NULL,
  `contenido` text NOT NULL,
  `ruta_imagen` varchar(255) DEFAULT NULL,
  `likes` int DEFAULT '0',
  `tiempo_creacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_actualizacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `categoria_id` (`categoria_id`),
  CONSTRAINT `publicaciones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `a` (`id`),
  CONSTRAINT `publicaciones_ibfk_2` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=82 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `publicaciones`
--

LOCK TABLES `publicaciones` WRITE;
/*!40000 ALTER TABLE `publicaciones` DISABLE KEYS */;
INSERT INTO `publicaciones` VALUES (77,51,'prueba2',5,'prueba2','uploads/post_6819cb5d7264a5.35404567.jpg',2,'2025-05-06 08:42:05','2025-05-06 09:32:15'),(78,49,'prueba',1,'prueba','uploads/post_6819d719c64be5.49436021.jpg',1,'2025-05-06 09:32:09','2025-05-06 09:32:14'),(79,49,'prueba3',2,'texto','uploads/post_6819d7672a93c0.23995799.jpg',0,'2025-05-06 09:33:27','2025-05-06 09:33:27'),(80,49,'prueba 4',4,'texto','uploads/post_6819d7a38bbe5_fondo_webs.jpeg',0,'2025-05-06 09:33:56','2025-05-06 09:34:27'),(81,49,'prueba 5',3,'texto','uploads/post_6819d7f98a34c7.64513335.png',0,'2025-05-06 09:35:53','2025-05-06 09:35:53');
/*!40000 ALTER TABLE `publicaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `ID_usuario` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(45) NOT NULL,
  `apellidos_usuario` varchar(45) NOT NULL,
  `genero_usuario` enum('H','M') NOT NULL,
  `email_usuario` varchar(45) NOT NULL,
  `password_usuario` varchar(45) NOT NULL,
  `cumpleanos_usuario` date NOT NULL,
  `rol_usuario` enum('A','U') NOT NULL DEFAULT 'U',
  PRIMARY KEY (`ID_usuario`),
  UNIQUE KEY `idusuario_UNIQUE` (`ID_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-05-06  4:37:34
-- vistas
-- vista1
CREATE VIEW vista_publicaciones_completas AS
SELECT 
    p.id AS id_publicacion,
    p.titulo,
    p.contenido,
    p.tiempo_creacion,
    a.nombre AS autor,
    c.nombre AS categoria
FROM publicaciones p
JOIN a ON p.user_id = a.id
LEFT JOIN categorias c ON p.categoria_id = c.id;
SELECT * FROM vista_publicaciones_completas;

-- vista2
CREATE VIEW vista_likes_publicaciones AS
SELECT 
    p.id AS id_publicacion,
    p.titulo,
    COUNT(l.user_id) AS likes
FROM publicaciones p
LEFT JOIN likes l ON p.id = l.post_id
GROUP BY p.id, p.titulo;

SELECT * FROM vista_likes_publicaciones;

-- vista3
CREATE VIEW vista_comentarios_usuarios AS
SELECT 
    a.id AS id_usuario,
    a.nombre,
    COUNT(c.id) AS total_comentarios
FROM a
LEFT JOIN comentarios c ON a.id = c.user_id
GROUP BY a.id, a.nombre;

SELECT * FROM vista_comentarios_usuarios;

-- vista4
CREATE VIEW vista_likes_comentarios AS
SELECT 
    c.id AS id_comentario,
    c.comentario_text,
    COUNT(lc.id) AS likes_comentarios
FROM comentarios c
LEFT JOIN likes_comentarios lc ON c.id = lc.comentario_id
GROUP BY c.id, c.comentario_text;

SELECT * FROM  vista_likes_comentarios;

-- vista5
CREATE VIEW vista_comentarios_publicaciones AS
SELECT 
    p.id AS id_publicacion,
    p.titulo,
    COUNT(c.id) AS total_comentarios
FROM publicaciones p
LEFT JOIN comentarios c ON p.id = c.post_id
GROUP BY p.id, p.titulo;

SELECT * FROM  vista_comentarios_publicaciones;

-- vista6
CREATE VIEW vista_comentarios_completos AS
SELECT 
    c.id AS id_comentario,
    c.comentario_text,
    c.tiempo_creacion AS fecha_publicacion,
    a.nombre AS autor
FROM comentarios c
JOIN a ON c.user_id = a.id;

SELECT * FROM vista_comentarios_completos;

-- vista7
CREATE VIEW vista_usuarios_completos AS
SELECT 
    a.id AS user_id,
    a.nombre,
    
    COUNT(DISTINCT p.id) AS total_publicaciones,
    COUNT(DISTINCT c.id) AS total_comentarios,
    COUNT(DISTINCT l.post_id) AS total_likes,
    COUNT(DISTINCT lc.id) AS total_likes_en_comentarios

FROM a
LEFT JOIN publicaciones p ON a.id = p.user_id
LEFT JOIN comentarios c ON a.id = c.user_id
LEFT JOIN likes l ON a.id = l.user_id
LEFT JOIN likes_comentarios lc ON a.id = lc.user_id

GROUP BY a.id, a.nombre;

SELECT * FROM vista_usuarios_completos;

-- vista8
CREATE VIEW vista_usuarios AS
SELECT 

    
    COUNT(DISTINCT a.id) AS total_usuarios

FROM a;

SELECT * FROM vista_usuarios;

-- triggers
DELIMITER $$

CREATE TRIGGER prevent_delete_popular_post
BEFORE DELETE ON publicaciones
FOR EACH ROW
BEGIN
  IF OLD.likes >= 5 THEN
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'No se puede eliminar publicaciones con 5 o más likes.';
  END IF;
END$$

DELIMITER ;

SELECT * FROM log_comentarios;

CREATE TABLE log_comentarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT,
  post_id INT,
  comentario_id INT,
  accion VARCHAR(20),
  fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DELIMITER $$

CREATE TRIGGER after_insert_comentario
AFTER INSERT ON comentarios
FOR EACH ROW
BEGIN
  INSERT INTO comentarios (user_id, post_id, comentario_id, accion)
  VALUES (NEW.user_id, NEW.post_id, NEW.id, 'insertado');
END$$

DELIMITER ;
-- funciones
DELIMITER //

CREATE FUNCTION contar_comentarios(postId INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE total INT;

    SELECT COUNT(*) INTO total
    FROM comentarios
    WHERE post_id = postId;

    RETURN total;
END //

DELIMITER ;
SELECT contar_comentarios(77); 

DELIMITER $$

CREATE FUNCTION promedio_publicaciones_global()
RETURNS DECIMAL(10,2)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE total_publicaciones INT;
    DECLARE primer_dia DATE;
    DECLARE dias_entre INT;

    -- Total de publicaciones
    SELECT COUNT(*) INTO total_publicaciones
    FROM publicaciones;

    -- Fecha de la primera publicación
    SELECT MIN(DATE(tiempo_creacion)) INTO primer_dia
    FROM publicaciones;

    -- Calcular días desde la primera publicación hasta hoy
    SET dias_entre = DATEDIFF(CURDATE(), primer_dia) + 1;

    -- Retornar promedio
    RETURN total_publicaciones / dias_entre;
END$$

DELIMITER ;
SELECT promedio_publicaciones_global() AS promedio_diario_global;


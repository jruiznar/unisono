CREATE DATABASE unisono;
USE unisono;

CREATE TABLE usuario (
    id_usuario INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(30) NOT NULL,
    apellidos VARCHAR(50) NOT NULL, -- ← NUEVO CAMPO
    nombre_usuario VARCHAR(30) NOT NULL UNIQUE,
    pass VARCHAR(255) NOT NULL,
    edad INT NOT NULL,
    localidad VARCHAR(50) NOT NULL,
    instrumento VARCHAR(30) NOT NULL,
    gustos TEXT NOT NULL,
    nivel VARCHAR(30) NOT NULL
);

CREATE TABLE evento (
    id_evento INT PRIMARY KEY AUTO_INCREMENT,
    id_creador INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    ubicacion VARCHAR(100)  NOT null,
    FOREIGN KEY (id_creador) REFERENCES Usuario(id_usuario)
);

CREATE TABLE mensaje (
    id_mensaje INT PRIMARY KEY AUTO_INCREMENT,
    emisor_id INT NOT NULL,
    receptor_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_envio DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emisor_id) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (receptor_id) REFERENCES Usuario(id_usuario)
);

CREATE TABLE video (
    id_video INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    descripcion TEXT,
    url_video VARCHAR(255) NOT NULL,
    fecha_subida DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

CREATE TABLE comentario (
    id_comentario INT PRIMARY KEY AUTO_INCREMENT,
    id_video INT NOT NULL,
    id_usuario INT NOT NULL,
    comentario TEXT NOT NULL,
    fecha_comentario DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_video) REFERENCES Video(id_video),
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario)
);

CREATE TABLE seguimiento (
    seguidor_id INT NOT NULL,
    seguido_id INT NOT NULL,
    PRIMARY KEY (seguidor_id, seguido_id),
    FOREIGN KEY (seguidor_id) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (seguido_id) REFERENCES Usuario(id_usuario)
);

CREATE TABLE sugerencia (
    id_sugerencia INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT NOT NULL,
    id_sugerido INT NOT NULL,
    motivo VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_usuario) REFERENCES Usuario(id_usuario),
    FOREIGN KEY (id_sugerido) REFERENCES Usuario(id_usuario)
);
CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    rol ENUM('usuario', 'admin') DEFAULT 'usuario',
    saldo DECIMAL(10,2) DEFAULT 100.00,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE apuestas (
    id INT PRIMARY KEY AUTO_INCREMENT,
    creador_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT,
    cantidad DECIMAL(10,2) NOT NULL,
    estado ENUM('abierta', 'aceptada', 'cerrada') DEFAULT 'abierta',
    aceptante_id INT DEFAULT NULL,
    ganador_id INT DEFAULT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (creador_id) REFERENCES usuarios(id),
    FOREIGN KEY (aceptante_id) REFERENCES usuarios(id),
    FOREIGN KEY (ganador_id) REFERENCES usuarios(id)
);

CREATE TABLE logs_actividad (
    id INT PRIMARY KEY AUTO_INCREMENT,
    usuario_id INT,
    accion VARCHAR(100),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
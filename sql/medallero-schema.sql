CREATE DATABASE IF NOT EXISTS medallero;
USE medallero;

CREATE TABLE medallas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('Autonómico', 'Nacional', 'Internacional'),
    competicion VARCHAR(255),
    deporte VARCHAR(100),
    posicion ENUM('Oro', 'Plata', 'Bronce', 'Participante'),
    division VARCHAR(100) NULL,
    lugar VARCHAR(100),
    provincia VARCHAR(100),
    comunidad VARCHAR(100),
    pais VARCHAR(100),
    year YEAR
);

CREATE TABLE IF NOT EXISTS competiciones AS (
    SELECT DISTINCT tipo, competicion, lugar, provincia, comunidad, pais, year
    FROM medallas
);

CREATE TABLE IF NOT EXISTS competiciones_slalom AS (
    SELECT DISTINCT tipo, competicion, lugar, provincia, comunidad, year
    FROM medallas
    WHERE deporte = 'Slalom'
);

CREATE TABLE IF NOT EXISTS competiciones_boccia AS (
    SELECT DISTINCT tipo, competicion, lugar, provincia, comunidad, year
    FROM medallas
    WHERE deporte = 'Boccia'
);

CREATE TABLE IF NOT EXISTS partidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM ('Autonómico', 'Nacional', 'Internacional'),
    division ENUM ('Individual', 'Parejas'),
    participante VARCHAR(100),
    fase ENUM ('Pool', 'Eliminación', 'Ida', 'Vuelta', 'Triangular'),
    miColor ENUM ('Rojo', 'Azul'),
    colorRival ENUM ('Rojo', 'Azul'),
    fecha DATE,
    ubicacion VARCHAR(100),
    provincia VARCHAR(100) NULL,
    comunidad VARCHAR(100) NULL,
    pais VARCHAR(100),
    parcial1A INT NOT NULL,
    parcial1B INT NOT NULL,
    parcial2A INT NOT NULL,
    parcial2B INT NOT NULL,
    parcial3A INT NOT NULL,
    parcial3B INT NOT NULL,
    parcial4A INT NOT NULL,
    parcial4B INT NOT NULL,
    desempateA INT NULL,
    desempateB INT NULL,
    resultadoA VARCHAR(3) NOT NULL,
    resultadoB VARCHAR(3) NOT NULL,
    resultadoFinal ENUM ('Victoria', 'Derrota')
);

CREATE TABLE IF NOT EXISTS partidos_por_participante AS (
    SELECT participante, SUM(CASE WHEN resultadoFinal = 'Victoria' THEN 1 ELSE 0 END) AS victorias, SUM(CASE WHEN resultadoFinal = 'Derrota' THEN 1 ELSE 0 END) AS derrotas
    FROM partidos
    WHERE division = 'Individual'
    GROUP BY participante
    ORDER BY victorias DESC, derrotas ASC
);

CREATE TABLE IF NOT EXISTS partidos_por_parejas AS (
    SELECT participante AS pareja, SUM(CASE WHEN resultadoFinal = 'Victoria' THEN 1 ELSE 0 END) AS victorias, SUM(CASE WHEN resultadoFinal = 'Derrota' THEN 1 ELSE 0 END) AS derrotas
    FROM partidos
    WHERE division = 'Parejas'
    GROUP BY participante
    ORDER BY victorias DESC, derrotas ASC
);

CREATE TABLE IF NOT EXISTS participantes_nacionales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100),
    club VARCHAR(100),
    provincia VARCHAR(100),
    comunidad VARCHAR(100),
    year YEAR
);



CREATE TABLE IF NOT EXISTS equipos_boccia (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(255),
    integrantes VARCHAR(100),
    club VARCHAR(100),
    comunidad VARCHAR(100),
    year YEAR
);
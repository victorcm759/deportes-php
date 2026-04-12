CREATE DATABASE IF NOT EXISTS medallero;
USE medallero;

CREATE TABLE medallas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('Autonómico', 'Nacional', 'Internacional'),
    competicion VARCHAR(255),
    deporte VARCHAR(100),
    posicion ENUM('Oro', 'Plata', 'Bronce', 'Participante'),
    lugar VARCHAR(100),
    provincia VARCHAR(100),
    comunidad VARCHAR(100),
    pais VARCHAR(100),
    year YEAR
);

INSERT INTO medallas (tipo, competicion, deporte, posicion, lugar, provincia, comunidad, pais, year) VALUES
('Autonómico', 'Campeonato de Cataluña de Eliminación por Equipos', 'Slalom', 'Oro', 'Vilafranca del Penedès', 'Barcelona', 'Cataluña', 'España', 2023),
('Autonómico', 'Campeonato de Cataluña de Slalom (Absoluto División WS4M)', 'Slalom', 'Plata', 'Granollers', 'Barcelona', 'Cataluña', 'España', 2023),
('Autonómico', 'Campeonato de Cataluña de Slalom (Absoluto Juvenil)', 'Slalom', 'Oro', 'Granollers', 'Barcelona', 'Cataluña', 'España', 2023),
('Nacional', 'Campeonato de España de Slalom en Silla de Ruedas (Crono WS4M)', 'Slalom', 'Bronce', 'Getafe', 'Madrid', 'Comunidad de Madrid', 'España', 2023),
('Nacional', 'Campeonato de España de Slalom en Silla de Ruedas (Eliminación Individual WS4M)', 'Slalom', 'Plata', 'Getafe', 'Madrid', 'Comunidad de Madrid', 'España', 2023),
('Autonómico', 'Campeonato de Cataluña de Slalom de Eliminación por Equipos', 'Slalom', 'Plata', 'Granollers', 'Barcelona', 'Cataluña', 'España', 2024),
('Autonómico', 'Campeonato de Cataluña de Slalom de Eliminación Individual', 'Slalom', 'Oro', 'Vilafranca del Penedès', 'Barcelona', 'Cataluña', 'España', 2024),
('Nacional', 'Campeonato de España de Slalom en Silla de Ruedas (Crono WS4M)', 'Slalom', 'Participante', 'Santa Marta de Tormes', 'Salamanca', 'Castilla y León', 'España', 2024),
('Nacional', 'Campeonato de España de Slalom en Silla de Ruedas (Eliminación Individual WS4M)', 'Slalom', 'Participante', 'Santa Marta de Tormes', 'Salamanca', 'Castilla y León', 'España', 2024),
('Autonómico', 'Campeonato de Cataluña de Slalom de Eliminación por Equipos', 'Slalom', 'Bronce', 'Esplugues de Llobregat', 'Barcelona', 'Cataluña', 'España', 2025),
('Autonómico', 'Campeonato de Cataluña de Slalom (Absoluto Eliminación Individual)', 'Slalom', 'Plata', 'Sant Pere de Ribes', 'Barcelona', 'Cataluña', 'España', 2025),
('Autonómico', 'Liga Catalana de Boccia', 'Boccia', 'Bronce', 'Santa Coloma de Gramenet', 'Barcelona', 'Cataluña', 'España', 2025),
('Nacional', 'Campeonato de España de Slalom en Silla de Ruedas (Crono WS4M)', 'Slalom', 'Plata', 'Santa Marta de Tormes', 'Salamanca', 'Castilla y León', 'España', 2025),
('Nacional', 'Campeonato de España de Slalom en Silla de Ruedas (Eliminación Individual WS4M)', 'Slalom', 'Bronce', 'Santa Marta de Tormes', 'Salamanca', 'Castilla y León', 'España', 2025),
('Autonómico', 'Campeonato de Cataluña de Boccia Infantil y Juvenil', 'Boccia', 'Oro', 'Barcelona', 'Barcelona', 'Cataluña', 'España', 2025),
('Nacional', 'Copa de España de Boccia Individual por Selecciones Autonómicas', 'Boccia', 'Bronce', 'Lloret de Mar', 'Girona', 'Cataluña', 'España', 2025),
('Autonómico', 'Campeonato de Cataluña de Boccia por Parejas y Equipos', 'Boccia', 'Plata', 'Esplugues de Llobregat', 'Barcelona', 'Cataluña', 'España', 2026),
('Autonómico', 'Campeonato de Cataluña de Slalom de Eliminación Por Equipos', 'Slalom', 'Oro', 'Sant Feliu de Llobregat', 'Barcelona', 'Cataluña', 'España', 2026),
('Nacional', 'Campeonato de España de Boccia de Jóvenes', 'Boccia', 'Oro', 'Granada', 'Granada', 'Andalucía', 'España', 2026),
('Autonómico', 'Liga Catalana de Boccia', 'Boccia', 'Participante', 'Santa Coloma de Cervelló', 'Barcelona', 'Cataluña', 'España', 2026)
-- ('', '', '', '', '', '', '', '', null),
;

CREATE TABLE IF NOT EXISTS partidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM ('Autonómico', 'Nacional', 'Internacional'),
    participante VARCHAR(100),
    fase ENUM ('Pool', 'Eliminación', 'Ida', 'Vuelta'),
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

INSERT INTO partidos (tipo, participante, fase, miColor, colorRival, fecha, ubicacion, provincia, comunidad, pais, parcial1A, parcial1B, parcial2A, parcial2B, parcial3A, parcial3B, parcial4A, parcial4B, desempateA, desempateB, resultadoA, resultadoB, resultadoFinal) VALUES 
('Autonómico', 'Vasile Agache', 'Pool', 'Azul', 'Rojo', '2024-12-15', 'Mataró', 'Barcelona', 'Cataluña', 'España', 0, 6, 0, 3, 0, 6, 0, 1, null, null, '0', '16', 'Derrota'),
('Autonómico', 'Bernard Guerra', 'Pool', 'Rojo', 'Azul', '2024-12-15', 'Mataró', 'Barcelona', 'Cataluña', 'España', 0, 2, 0, 1, 0, 1, 1, 0, null, null, '1', '4', 'Victoria'),
('Autonómico', 'Ramon Prat', 'Pool', 'Rojo', 'Azul', '2025-02-02', 'Santa Coloma de Cervelló', 'Barcelona', 'Cataluña', 'España', 3, 0, 2, 0, 1, 0, 3, 0, null, null, '9', '0', 'Derrota'),
('Autonómico', 'Lucía Rovira', 'Pool', 'Azul', 'Rojo', '2025-02-02', 'Santa Coloma de Cervelló', 'Barcelona', 'Cataluña', 'España', 0, 3, 0, 1, 0, 1, 0, 1, null, null, '0', '6', 'Victoria'),
('Autonómico', 'Vasile Agache', 'Eliminación', 'Azul', 'Rojo', '2025-03-30', 'Santa Coloma de Gramenet', 'Barcelona', 'Cataluña', 'España', 5, 0, 3, 0, 3, 0, 1, 0, null, null, '12', '0', 'Derrota'),
('Autonómico', 'Lucía Rovira', 'Eliminación', 'Azul', 'Rojo', '2025-03-30', 'Santa Coloma de Gramenet', 'Barcelona', 'Cataluña', 'España', 1, 0, 0, 1, 6, 0, 0, 2, null, null, '7', '3', 'Victoria'),
('Autonómico', 'Montse Blanch', 'Pool', 'Azul', 'Rojo', '2025-05-25', 'Barcelona', 'Barcelona', 'Cataluña', 'España', 1, 0, 0, 1, 1, 0, 0, 1, 0, 4, '2', '2*', 'Victoria'),
('Autonómico', 'Lucía Rovira', 'Pool', 'Azul', 'Rojo', '2025-05-25', 'Barcelona', 'Barcelona', 'Cataluña', 'España', 1, 0, 0, 3, 0, 3, 0, 1, null, null, '1', '7', 'Victoria'),
('Nacional', 'Elena Valencia (NAV)', 'Pool', 'Rojo', 'Azul', '2025-05-30', 'Lloret de Mar', 'Girona', 'Cataluña', 'España', 3, 0, 0, 2, 4, 0, 0, 1, null, null, '7', '4', 'Derrota'),
('Nacional', 'Rosa María de Dios (CYL)', 'Pool', 'Rojo', 'Azul', '2025-05-30', 'Lloret de Mar', 'Girona', 'Cataluña', 'España', 2, 0, 0, 1, 2, 0, 0, 1, null, null, '4', '2', 'Victoria'),
('Nacional', 'José Miguel Ramón (VAL)', 'Pool', 'Azul', 'Rojo', '2025-06-01', 'Lloret de Mar', 'Girona', 'Cataluña', 'España', 3, 0, 0, 3, 3, 0, 0, 4, null, null, '6', '7', 'Victoria'),
('Autonómico','Lucía Rovira', 'Pool', 'Rojo', 'Azul', '2025-11-30', 'Molins de Rei', 'Barcelona', 'Cataluña', 'España', 3, 0, 1, 1, 2, 0, 0, 1, null, null, '6', '3', 'Victoria'),
('Autonómico','Ramon Prat', 'Pool', 'Azul', 'Rojo', '2025-11-30', 'Molins de Rei', 'Barcelona', 'Cataluña', 'España', 6, 0, 1, 0, 2, 0, 1, 0, null, null, '10', '0', 'Derrota'),
('Autonómico','Vasile Agache', 'Pool', 'Rojo', 'Azul', '2026-03-01', 'Blanes', 'Girona', 'Cataluña', 'España', 0, 4, 0, 1, 0, 2, 0, 3, null, null, '0', '10', 'Derrota'),
('Autonómico','Carlos Javier Vera', 'Pool', 'Azul', 'Rojo', '2026-03-01', 'Blanes', 'Girona', 'Cataluña', 'España', 0, 1, 0, 1, 0, 2, 0, 5, null, null, '1', '8', 'Victoria'),
('Autonómico','Combinado BC4 (Ida)', 'Ida', 'Azul', 'Rojo', '2026-03-07', 'Esplugues de Llobregat', 'Barcelona', 'Cataluña', 'España', 0, 2, 0, 3, 2, 0, 0, 1, null, null, '2', '5', 'Derrota'),
('Autonómico','Combinado BC4 (Vuelta)', 'Vuelta', 'Rojo', 'Azul', '2026-03-07', 'Esplugues de Llobregat', 'Barcelona', 'Cataluña', 'España', 0, 3, 1, 0, 2, 0, 0, 1, null, null, '3', '4', 'Victoria'),
('Nacional','Lucía Rovira', 'Ida', 'Azul', 'Rojo', '2026-03-14', 'Granada', 'Granada', 'Andalucía', 'España', 0, 3, 0, 1, 1, 1, 0, 4, null, null, '1', '9', 'Victoria'),
('Nacional','Izan Camacho', 'Ida', 'Rojo', 'Azul', '2026-03-14', 'Granada', 'Granada', 'Andalucía', 'España', 3, 0, 1, 0, 0, 4, 1, 0, null, null, '4', '5', 'Derrota'),
('Nacional','Lucía Rovira', 'Vuelta', 'Rojo', 'Azul', '2026-03-15', 'Granada', 'Granada', 'Andalucía', 'España', 3, 0, 1, 0, 1, 0, 1, 0, null, null, '6', '0', 'Victoria'),
('Nacional','Izan Camacho', 'Vuelta', 'Azul', 'Rojo', '2026-03-15', 'Granada', 'Granada', 'Andalucía', 'España', 1, 0, 0, 1, 0, 2, 0, 4, null, null, '1', '6', 'Victoria'),
('Autonómico','Ramon Prat', 'Eliminación', 'Rojo', 'Azul', '2026-04-12', 'Santa Coloma de Cervelló', 'Barcelona', 'Cataluña', 'España', 1, 0, 0, 1, 0, 1, 0, 1, null, null, '1', '3', 'Derrota'),
('Autonómico','Lucía Rovira', 'Eliminación', 'Azul', 'Rojo', '2026-04-12', 'Santa Coloma de Cervelló', 'Barcelona', 'Cataluña', 'España', 1, 0, 0, 1, 1, 0, 1, 0, null, null, '3', '1', 'Derrota')
--('','', '', '', '', '2026', '', '', '', 'España', 0, 0, 0, 0, 0, 0, 0, 0, null, null, '', '', ''),
;
-- Migración v2: rediseño de inst_agrupacion_calibre
-- Reemplaza rango_min/rango_max por calibres individuales + agrega id_exportadora

-- 1. Eliminar tabla anterior si existe (perderá datos existentes)
IF OBJECT_ID('inst_agrupacion_calibre_detalle', 'U') IS NOT NULL
    DROP TABLE inst_agrupacion_calibre_detalle;
IF OBJECT_ID('inst_agrupacion_calibre', 'U') IS NOT NULL
    DROP TABLE inst_agrupacion_calibre;

-- 2. Tabla cabecera de agrupación (especie + exportadora + categoría + nombre grupo)
CREATE TABLE inst_agrupacion_calibre (
    id              INT IDENTITY(1,1) PRIMARY KEY,
    id_especie      INT          NOT NULL,
    id_exportadora  INT          NOT NULL,
    id_categoria    INT          NOT NULL,
    nombre_grupo    NVARCHAR(50) NOT NULL,
    CONSTRAINT FK_agrc_especie     FOREIGN KEY (id_especie)     REFERENCES especie(id_especie),
    CONSTRAINT FK_agrc_exportadora FOREIGN KEY (id_exportadora) REFERENCES inst_exportadora(id),
    CONSTRAINT FK_agrc_categoria   FOREIGN KEY (id_categoria)   REFERENCES inst_categoria(id)
);

-- 3. Tabla detalle: calibres que pertenecen a cada grupo
CREATE TABLE inst_agrupacion_calibre_detalle (
    id              INT IDENTITY(1,1) PRIMARY KEY,
    id_agrupacion   INT NOT NULL,
    id_calibre      INT NOT NULL,
    CONSTRAINT FK_agrd_agrupacion FOREIGN KEY (id_agrupacion) REFERENCES inst_agrupacion_calibre(id) ON DELETE CASCADE,
    CONSTRAINT FK_agrd_calibre    FOREIGN KEY (id_calibre)    REFERENCES inst_calibre(id)
);

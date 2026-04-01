-- Tabla para configurar agrupaciones de calibres por especie + categoría
-- Permite definir grupos (ej: XLA, XLB) con rango numérico para una especie y categoría específica
CREATE TABLE inst_agrupacion_calibre (
    id           INT IDENTITY(1,1) PRIMARY KEY,
    id_especie   INT          NOT NULL,
    id_categoria INT          NOT NULL,
    nombre_grupo NVARCHAR(50) NOT NULL,
    rango_min    INT          NOT NULL,
    rango_max    INT          NOT NULL,
    CONSTRAINT FK_agrupacion_especie   FOREIGN KEY (id_especie)   REFERENCES especie(id_especie),
    CONSTRAINT FK_agrupacion_categoria FOREIGN KEY (id_categoria) REFERENCES inst_categoria(id)
);

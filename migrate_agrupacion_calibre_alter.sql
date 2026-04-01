-- Agregar columna id_categoria a inst_agrupacion_calibre
-- Ejecutar solo si la tabla ya fue creada con migrate_agrupacion_calibre.sql
ALTER TABLE inst_agrupacion_calibre
    ADD id_categoria INT NOT NULL DEFAULT 0;

-- Quitar el default temporal usado para el ALTER
ALTER TABLE inst_agrupacion_calibre
    DROP CONSTRAINT DF__inst_agru__id_ca__XXXXXXXX; -- reemplazar con el nombre real del constraint

-- Agregar FK
ALTER TABLE inst_agrupacion_calibre
    ADD CONSTRAINT FK_agrupacion_categoria FOREIGN KEY (id_categoria) REFERENCES inst_categoria(id);

-- Si la tabla NO fue creada aún, usar directamente migrate_agrupacion_calibre.sql
-- que ya incluye id_categoria desde el inicio.

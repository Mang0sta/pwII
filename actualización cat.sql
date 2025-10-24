use db_users;

-- Actualiza la Categoría 1
UPDATE categorias
SET nombre = 'Clasificatorias'
WHERE id = 1;

-- Actualiza la Categoría 2
UPDATE categorias
SET nombre = 'Sedes, Viajes y Recomendaciones'
WHERE id = 2;

-- Actualiza la Categoría 3
UPDATE categorias
SET nombre = 'Noticias del Mundial'
WHERE id = 3;

-- Actualiza la Categoría 4
UPDATE categorias
SET nombre = 'Pronósticos'
WHERE id = 4;

-- Actualiza la Categoría 5
UPDATE categorias
SET nombre = 'Entretenimiento'
WHERE id = 5;

select * from categorias;
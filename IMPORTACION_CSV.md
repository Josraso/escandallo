# IMPORTACIÓN CSV - MÓDULO ESCANDALLO

## Formato del CSV

**IMPORTANTE: El separador es punto y coma (;) - Compatible con Excel**

El módulo soporta dos formatos de CSV: formato clásico (un solo idioma) y formato multiidioma (v2.0+).

---

## FORMATO CLÁSICO (un idioma)

El archivo CSV debe tener las siguientes columnas (16 columnas en total):

1. **id_principal** - ID del principal (ej: 1)
2. **nombre_principal** - Nombre del principal (ej: "Motor 2T")
3. **imagen_principal** - Nombre del archivo de imagen del principal (ej: "motor.jpg")
4. **id_parte** - ID de la parte/diagrama (ej: 101)
5. **nombre_parte** - Nombre de la parte (ej: "Cilindro")
6. **imagen_parte** - Nombre del archivo de imagen de la parte (ej: "cilindro.jpg")
7. **id_product** - ID del producto en PrestaShop (0 para crear nuevo, ej: 1500)
8. **numero_imagen** - Número de referencia en el diagrama (ej: 5)
9. **referencia** - Referencia del producto (ej: "CYL-001")
10. **nombre_producto** - Nombre del producto (ej: "Cilindro completo")
11. **descripcion** - Descripción del producto
12. **precio** - Precio del producto sin IVA (ej: 89.99)
13. **imagen_producto** - Nombre del archivo de imagen del producto (ej: "cyl001.jpg")
14. **stock** - Stock disponible (ej: 10)
15. **id_category** - ID de la categoría en PrestaShop (ej: 5)
16. **id_tax_rules_group** - ID del grupo de impuestos (ej: 1 para 21% IVA)

### Ejemplo de línea CSV (formato clásico):

```
id_principal;nombre_principal;imagen_principal;id_parte;nombre_parte;imagen_parte;id_product;numero_imagen;referencia;nombre_producto;descripcion;precio;imagen_producto;stock;id_categoria;id_tax_rules_group
1;"Motor 2T";"motor.jpg";101;"Cilindro";"cilindro.jpg";0;1;"CYL-001";"Cilindro completo";"Cilindro para motor 2T";89.99;"cyl001.jpg";10;5;1
```

**Nota:** Cuando se usa el formato clásico, el nombre se duplica automáticamente a todos los idiomas activos en la tienda.

---

## FORMATO MULTIIDIOMA (v2.0+)

A partir de la versión 2.0, puedes usar columnas específicas por idioma. El sistema detecta automáticamente si el CSV usa formato multiidioma analizando las cabeceras.

### Columnas multiidioma

En lugar de `nombre_principal`, usa `nombre_principal_XX` donde XX es el código ISO del idioma:

- `nombre_principal_es` - Nombre del principal en Español
- `nombre_principal_en` - Nombre del principal en English
- `nombre_principal_fr` - Nombre del principal en Français
- `nombre_parte_es`, `nombre_parte_en`, etc.
- `nombre_producto_es`, `nombre_producto_en`, etc.
- `descripcion_es`, `descripcion_en`, etc.

### Códigos de idioma soportados

Usa el código ISO 639-1 de dos letras del idioma configurado en tu PrestaShop:

- `es` - Español
- `en` - English
- `fr` - Français
- `de` - Deutsch
- `it` - Italiano
- `pt` - Português
- `ca` - Català
- `eu` - Euskara
- `gl` - Galego

**Nota:** Solo necesitas incluir los idiomas activos en tu tienda. Si un idioma no tiene valor, quedará vacío.

### Ejemplo multiidioma completo:

```
id_principal;nombre_principal_es;nombre_principal_en;imagen_principal;id_parte;nombre_parte_es;nombre_parte_en;imagen_parte;id_product;numero_imagen;referencia;nombre_producto_es;nombre_producto_en;descripcion_es;descripcion_en;precio;imagen_producto;stock;id_categoria;id_tax_rules_group
1;"Motor 2T";"2T Engine";"motor.jpg";101;"Cilindro";"Cylinder";"cilindro.jpg";0;1;"CYL-001";"Cilindro completo";"Complete cylinder";"Cilindro para motor 2T";"Cylinder for 2T engine";89.99;"cyl001.jpg";10;5;1
```

### Detección automática

El sistema detecta el formato analizando las cabeceras:
- Si encuentra columnas con patrón `nombre_principal_XX` → formato multiidioma
- Si encuentra `nombre_principal` sin sufijo → formato clásico (se duplica a todos los idiomas)

### Exportación CSV

Al exportar desde el módulo, el CSV incluirá automáticamente columnas para todos los idiomas activos:
- `nombre_principal_es`, `nombre_principal_en`, etc.
- `nombre_parte_es`, `nombre_parte_en`, etc.
- `nombre_producto_es`, `nombre_producto_en`, etc.
- `descripcion_es`, `descripcion_en`, etc.

Esto permite editar el CSV en Excel y reimportarlo sin perder traducciones.

---

## UBICACIÓN DE LAS IMÁGENES

### Imágenes de los Principales

Las imágenes de los productos principales se deben colocar en:

```
/modules/escandallo/views/img/principales/
```

**Ejemplo:**
- Si en el CSV pones `imagen_principal` = "motor.jpg"
- El archivo debe estar en: `/modules/escandallo/views/img/principales/motor.jpg`

### Imágenes de las Partes (Diagramas Técnicos)

Las imágenes de las partes/diagramas se deben colocar en:

```
/modules/escandallo/views/img/partes/
```

**Ejemplo:**
- Si en el CSV pones `imagen_parte` = "cilindro.jpg"
- El archivo debe estar en: `/modules/escandallo/views/img/partes/cilindro.jpg`

### Imágenes de los Productos

Las imágenes de los productos se deben colocar en:

```
/modules/escandallo/views/img/productos/
```

**Ejemplo:**
- Si en el CSV pones `imagen_producto` = "cyl001.jpg"
- El archivo debe estar en: `/modules/escandallo/views/img/productos/cyl001.jpg`

**IMPORTANTE:** El sistema copiará automáticamente la imagen a la carpeta de PrestaShop y la asociará al producto durante la importación.

## PROCESO DE IMPORTACIÓN

1. **Preparar las imágenes:**
   - Subir imágenes de principales a: `/modules/escandallo/views/img/principales/`
   - Subir imágenes de partes a: `/modules/escandallo/views/img/partes/`
   - Subir imágenes de productos a: `/modules/escandallo/views/img/productos/`
   - Los nombres deben coincidir exactamente con lo que pones en el CSV

2. **Preparar el archivo CSV:**
   - Columnas separadas por **punto y coma (;)**
   - Compatible con Excel - guarda como CSV (delimitado por punto y coma)
   - Primera fila: encabezados (obligatorio para formato multiidioma)
   - Codificación: UTF-8
   - Puedes descargar un archivo de ejemplo desde el backoffice

3. **Importar:**
   - Backoffice > Módulos > Escandallo > Configuración
   - Tab "Importar CSV"
   - Seleccionar archivo y hacer clic en "Importar CSV"

## NOTAS IMPORTANTES

- Si pones `id_product = 0`, el sistema creará un nuevo producto con los datos del CSV
- Si pones un `id_product` existente, solo asociará ese producto a la parte
- **Categorías:** Deben existir previamente en PrestaShop
- **Impuestos:** El `id_tax_rules_group` debe existir en PrestaShop (ej: 1 para 21% IVA). Si pones 0, se usará el grupo 1 por defecto
- **Imágenes:** Todos los archivos de imagen deben estar subidos ANTES de importar el CSV
- Formatos soportados de imagen: .jpg, .jpeg, .png, .gif
- Tamaño recomendado de imagen: 800x600px mínimo
- El sistema copiará automáticamente las imágenes de productos a la carpeta de PrestaShop

## SOLUCIÓN DE PROBLEMAS

**Error: "No se encuentra la imagen"**
- Verifica que el archivo esté en la carpeta correcta
- Verifica que el nombre coincida exactamente (mayúsculas/minúsculas)

**Error: "Producto no encontrado"**
- Si el id_product no existe, el CSV intentará crear el producto
- Asegúrate de que la categoría (id_category) existe

**Error: "Formato CSV incorrecto"**
- Verifica que las cabeceras estén correctas
- Usa punto y coma (;) como separador
- Usa comillas dobles para campos con punto y coma o saltos de línea

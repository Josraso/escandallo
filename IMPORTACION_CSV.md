# IMPORTACIÓN CSV - MÓDULO ESCANDALLO

## Formato del CSV

**IMPORTANTE: El separador es punto y coma (;) - Compatible con Excel**

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

## Ejemplo de línea CSV:

```
1;"Motor 2T";"motor.jpg";101;"Cilindro";"cilindro.jpg";0;1;"CYL-001";"Cilindro completo";"Cilindro para motor 2T";89.99;"cyl001.jpg";10;5;1
```

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
   - 16 columnas separadas por **punto y coma (;)**
   - Compatible con Excel - guarda como CSV (delimitado por punto y coma)
   - Primera fila: encabezados (opcional, se ignora)
   - Codificación: UTF-8
   - Puedes descargar un archivo de ejemplo desde el backoffice

3. **Importar:**
   - Backoffice > Módulos > Escandallo > Configuración
   - Tab "Importar CSV"
   - Seleccionar archivo y hacer clic en "Importar CSV"

## NOTAS IMPORTANTES

- Si pones `id_product = 0`, el sistema creará un nuevo producto con los datos del CSV
- Si pones un `id_product` existente, solo asociará ese producto a la parte
- **Categorías:** Deben existir previamente en PrestaShop. El sistema asignará correctamente el producto a la categoría especificada
- **Impuestos:** El `id_tax_rules_group` debe existir en PrestaShop (ej: 1 para 21% IVA). Si pones 0, se usará el grupo 1 por defecto
- **Imágenes:** Todos los archivos de imagen deben estar subidos ANTES de importar el CSV
- Formatos soportados de imagen: .jpg, .jpeg, .png, .gif
- Tamaño recomendado de imagen: 800x600px mínimo
- El sistema copiará automáticamente las imágenes de productos a la carpeta de PrestaShop

## SOLUCIÓN DE PROBLEMAS

**Error: "No se encuentra la imagen"**
- Verifica que el archivo esté en `/modules/escandallo/views/img/partes/`
- Verifica que el nombre coincida exactamente (mayúsculas/minúsculas)

**Error: "Producto no encontrado"**
- Si el id_product no existe, el CSV intentará crear el producto
- Asegúrate de que la categoría (id_category) existe

**Error: "Formato CSV incorrecto"**
- Verifica que todas las líneas tengan 14 columnas
- Usa comas como separador
- Usa comillas dobles para campos con comas o saltos de línea

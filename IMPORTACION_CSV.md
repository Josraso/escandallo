# IMPORTACIÓN CSV - MÓDULO ESCANDALLO

## Formato del CSV

El archivo CSV debe tener las siguientes columnas (14 columnas en total):

1. **id_principal** - ID del principal (ej: 1)
2. **nombre_principal** - Nombre del principal (ej: "Motor 2T")
3. **id_parte** - ID de la parte/diagrama (ej: 101)
4. **nombre_parte** - Nombre de la parte (ej: "Cilindro")
5. **imagen_parte** - Nombre del archivo de imagen de la parte (ej: "cilindro.jpg")
6. **id_product** - ID del producto en PrestaShop (ej: 1500)
7. **numero_imagen** - Número de referencia en el diagrama (ej: 5)
8. **referencia** - Referencia del producto (ej: "CYL-001")
9. **nombre_producto** - Nombre del producto (ej: "Cilindro completo")
10. **descripcion** - Descripción del producto
11. **precio** - Precio del producto (ej: 89.99)
12. **imagen_producto** - Nombre del archivo de imagen del producto (ej: "cyl001.jpg")
13. **stock** - Stock disponible (ej: 10)
14. **id_category** - ID de la categoría en PrestaShop (ej: 5)

## Ejemplo de línea CSV:

```
1,"Motor 2T",101,"Cilindro","cilindro.jpg",1500,5,"CYL-001","Cilindro completo","Cilindro para motor 2T",89.99,"cyl001.jpg",10,5
```

## UBICACIÓN DE LAS IMÁGENES

### Imágenes de las Partes (Diagramas Técnicos)

Las imágenes de las partes/diagramas se deben colocar en:

```
/modules/escandallo/views/img/partes/
```

**Ejemplo:**
- Si en el CSV pones `imagen_parte` = "cilindro.jpg"
- El archivo debe estar en: `/modules/escandallo/views/img/partes/cilindro.jpg`

### Imágenes de los Productos

Las imágenes de los productos se manejan directamente en PrestaShop y NO se importan mediante este CSV.

Para añadir imágenes a los productos:
1. Ve al backoffice de PrestaShop
2. Catálogo > Productos
3. Edita el producto
4. Sube la imagen en la pestaña "Imágenes"

**IMPORTANTE:** El campo `imagen_producto` del CSV actualmente NO se usa. Las imágenes de productos se gestionan mediante el sistema estándar de PrestaShop.

## PROCESO DE IMPORTACIÓN

1. **Preparar las imágenes de las partes:**
   - Subir todos los archivos de imágenes a `/modules/escandallo/views/img/partes/`
   - Los nombres deben coincidir exactamente con lo que pones en el CSV

2. **Preparar el archivo CSV:**
   - 14 columnas separadas por comas
   - Primera fila: encabezados (opcional, se ignora)
   - Codificación: UTF-8

3. **Importar:**
   - Backoffice > Módulos > Escandallo > Configuración
   - Sección "Importar CSV"
   - Seleccionar archivo y hacer clic en "Importar CSV"

## NOTAS IMPORTANTES

- Los productos deben existir previamente en PrestaShop o el CSV los creará
- Si el producto ya existe, solo se asocia a la parte
- Las categorías deben existir previamente en PrestaShop
- Los archivos de imagen deben estar en el servidor ANTES de importar el CSV
- Formatos soportados de imagen: .jpg, .jpeg, .png, .gif
- Tamaño recomendado de imagen: 800x600px mínimo

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

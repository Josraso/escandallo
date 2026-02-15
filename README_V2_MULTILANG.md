# Escandallo v2.0 - Sistema Multiidioma

## 📖 Guía Completa del Sistema Multiidioma

### 🌍 ¿Qué es el Sistema Multiidioma?

La versión 2.0 del módulo Escandallo introduce soporte completo para múltiples idiomas, permitiendo que los nombres de productos principales y partes estén disponibles en todos los idiomas activos de tu tienda PrestaShop.

---

## 🔄 Actualización desde v1.0 a v2.0

### Proceso Automático

Cuando actualices el módulo de v1.0 a v2.0, PrestaShop ejecutará automáticamente un script de migración que:

1. ✅ **Crea nuevas tablas multiidioma:**
   - `escandallo_principal_lang` (id_principal, id_lang, nombre)
   - `escandallo_parte_lang` (id_parte, id_lang, nombre)

2. ✅ **Migra datos existentes:**
   - Copia todos los nombres actuales a **TODOS** los idiomas activos
   - Ejemplo: Si tienes "Motor Principal" en español, se copiará a inglés, francés, etc.

3. ✅ **Limpia estructura antigua:**
   - Elimina el campo `nombre` de las tablas principales
   - Mantiene todos los demás datos intactos

4. ✅ **Sin pérdida de datos:**
   - Todos tus diagramas, partes y productos se mantienen
   - Las imágenes no se modifican
   - Las asociaciones de productos se conservan

### ⚠️ Importante

- **Backup:** Aunque el proceso es seguro, siempre haz un backup de tu base de datos antes de actualizar
- **Idiomas:** Todos los idiomas activos en tu tienda recibirán una copia del nombre actual
- **Traducciones:** Después de la migración, puedes editar las traducciones manualmente

---

## 💻 Cómo Usar el Sistema Multiidioma

### 1. Añadir un Producto Principal

1. Ve a **Módulos > Escandallo > Productos Principales**
2. Haz clic en **"Añadir Producto Principal"**
3. Verás un campo de texto con una bandera del idioma por defecto
4. Haz clic en **"Cambiar idioma"** para ver los otros idiomas disponibles
5. Rellena el nombre en cada idioma que desees
6. **Solo el idioma por defecto es obligatorio**
7. Sube una imagen (opcional)
8. Haz clic en **"Añadir Principal"**

**Ejemplo:**
```
🇪🇸 Español: Motor Principal
🇬🇧 English: Main Engine
🇫🇷 Français: Moteur Principal
```

### 2. Añadir una Parte/Diagrama

El proceso es idéntico al de Productos Principales:

1. Ve a **Módulos > Escandallo > Partes/Diagramas**
2. Selecciona el producto principal al que pertenece
3. Cambia entre idiomas con el botón **"Cambiar idioma"**
4. Rellena el nombre en cada idioma
5. Sube la imagen del diagrama (obligatorio)
6. Haz clic en **"Añadir Parte"**

**Ejemplo:**
```
🇪🇸 Español: Cilindro
🇬🇧 English: Cylinder
🇫🇷 Français: Cylindre
```

### 3. Editar Principales y Partes

1. Haz clic en el botón **"Editar"** junto al elemento
2. Se abrirá un modal con los campos de todos los idiomas
3. El campo mostrará el nombre actual
4. Puedes cambiar de idioma con el botón **"Cambiar idioma"**
5. Modifica los nombres que necesites
6. Haz clic en **"Guardar cambios"**

---

## 📊 Sistema de Visualización

### Frontend (Tienda)

El módulo muestra automáticamente el contenido en el idioma que el cliente esté usando:

- **Cliente en español** → Ve "Motor Principal"
- **Cliente en inglés** → Ve "Main Engine"
- **Cliente en francés** → Ve "Moteur Principal"

### Backend (Admin)

En el panel de administración siempre ves el nombre en el idioma por defecto de tu tienda, pero puedes editar cualquier idioma.

---

## 📁 Sistema CSV Multiidioma

### Exportar CSV

1. Ve a **Módulos > Escandallo > Exportar**
2. Haz clic en **"Exportar CSV"** o **"Exportar ZIP"**
3. El CSV exportará los nombres en el **idioma actualmente seleccionado** en el admin
4. Si cambias el idioma del admin y exportas de nuevo, obtendrás nombres en ese idioma

**Estructura CSV:**
```csv
id_principal;nombre_principal;imagen_principal;id_parte;nombre_parte;imagen_parte;id_product;numero_imagen;referencia;nombre_producto;descripcion;precio;imagen_producto;stock;id_categoria;id_tax_rules_group
```

### Importar CSV

1. Prepara tu archivo CSV con separador `;` (punto y coma)
2. Los nombres se importarán y **replicarán automáticamente a todos los idiomas**
3. Si importas "Motor Principal", se guardará en español, inglés, francés, etc.
4. Esto es útil para importación masiva; luego puedes editar las traducciones

**Nota:** El sistema replica el mismo nombre a todos los idiomas durante la importación. Para traducciones personalizadas, edita cada registro manualmente después de importar.

---

## 🔧 Características Técnicas

### Compatibilidad

- ✅ PrestaShop 1.7
- ✅ PrestaShop 1.8
- ✅ PrestaShop 9
- ✅ Cualquier número de idiomas activos
- ✅ Funciona perfectamente con 1 solo idioma

### Estructura de Base de Datos

**Tablas Principales:**
- `escandallo_principal` (sin campo nombre)
- `escandallo_parte` (sin campo nombre)
- `escandallo_producto_parte` (sin cambios)

**Tablas Multiidioma (nuevas en v2.0):**
- `escandallo_principal_lang`
  - `id_principal` (FK)
  - `id_lang` (FK)
  - `nombre` VARCHAR(255)
  - PRIMARY KEY (id_principal, id_lang)

- `escandallo_parte_lang`
  - `id_parte` (FK)
  - `id_lang` (FK)
  - `nombre` VARCHAR(255)
  - PRIMARY KEY (id_parte, id_lang)

### Queries SQL

El módulo usa LEFT JOIN automáticos para obtener el nombre en el idioma del contexto actual:

```sql
SELECT p.*, pl.nombre
FROM escandallo_principal p
LEFT JOIN escandallo_principal_lang pl
  ON (p.id_principal = pl.id_principal AND pl.id_lang = {id_lang})
ORDER BY pl.nombre ASC
```

---

## ❓ Preguntas Frecuentes (FAQ)

### ¿Qué pasa si solo tengo un idioma?

No hay problema. El sistema funciona perfectamente con 1 solo idioma. Solo verás un campo de texto normal.

### ¿Puedo dejar idiomas vacíos?

Sí, pero al menos **un idioma debe tener nombre** (por defecto, el idioma principal de tu tienda). Los idiomas vacíos simplemente no mostrarán nada en el frontend.

### ¿Cómo edito traducciones existentes?

1. Haz clic en "Editar" en el principal o parte
2. Usa el botón "Cambiar idioma"
3. Modifica el nombre en el idioma deseado
4. Guarda los cambios

### ¿Los productos asociados son multiidioma?

Los productos usan el sistema multiidioma de PrestaShop, no requieren cambios adicionales. El módulo solo maneja principales y partes.

### ¿Puedo volver a v1.0?

No es recomendable. Una vez migrado a v2.0, volver a v1.0 requeriría restaurar un backup de la base de datos.

### ¿El CSV exportado es compatible con v1.0?

Sí, la estructura CSV es la misma. Sin embargo, al importar en v1.0 no se crearían las traducciones multiidioma.

---

## 🚀 Mejores Prácticas

### 1. Traducciones Coherentes

Mantén consistencia en las traducciones:
- Usa terminología similar en todos los idiomas
- Evita traducciones literales si no tienen sentido técnico
- Considera usar términos técnicos universales cuando sea apropiado

### 2. Gestión de Contenido

- **Pequeñas tiendas:** Usa el formulario admin para añadir/editar
- **Tiendas grandes:** Importa CSV en idioma principal, luego edita traducciones
- **Multitienda:** Exporta de una tienda, importa en otra

### 3. Workflow Recomendado

1. Añade principales/partes en tu idioma principal
2. Asocia productos
3. Prueba en frontend que todo funcione
4. Edita traducciones a otros idiomas
5. Prueba en cada idioma del frontend

---

## 📝 Notas Técnicas para Desarrolladores

### Hooks de Idioma

El módulo usa `Context::getLanguage()->id` para determinar el idioma actual.

### Extensión del Sistema

Si necesitas añadir más campos multiidioma:

1. Crea tabla `_lang` correspondiente
2. Modifica queries con LEFT JOIN
3. Actualiza formularios con tabs de idioma
4. Añade campos en los process functions

### Debugging

Para verificar qué idioma se está usando:
```php
var_dump($this->context->language->id);
var_dump($this->context->language->name);
```

---

## 📞 Soporte

Si encuentras problemas con el sistema multiidioma:

1. Verifica que todos los idiomas estén activos en PrestaShop
2. Comprueba que las imágenes de banderas existan en `/img/l/`
3. Revisa los logs de PrestaShop en `/var/logs/`
4. Haz un backup antes de hacer cambios importantes

---

## 🎯 Resumen Rápido

| Característica | Descripción |
|---------------|-------------|
| **Idiomas soportados** | Todos los activos en PrestaShop |
| **Campos multiidioma** | Nombre de principales y partes |
| **Frontend** | Automático según idioma del cliente |
| **Backend** | Tabs para cambiar entre idiomas |
| **CSV** | Exporta en idioma actual, importa y replica |
| **Migración** | Automática desde v1.0 |
| **Compatibilidad** | PrestaShop 1.7, 1.8, 9 |

---

**Versión del documento:** 2.0
**Fecha:** 2025
**Módulo:** Escandallo v2.0

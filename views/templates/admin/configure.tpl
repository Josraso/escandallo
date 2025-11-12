<div class="panel escandallo-admin-panel">
    <div class="panel-heading">
        <i class="icon-cogs"></i>
        {l s='Configuración del Módulo Escandallo' mod='escandallo'}
    </div>
    
    <div class="panel-body">
        <!-- Tabs de navegación -->
        <ul class="nav nav-tabs" role="tablist">
            <li class="active">
                <a href="#tab-config" role="tab" data-toggle="tab">
                    <i class="icon-cog"></i>
                    {l s='Configuración' mod='escandallo'}
                </a>
            </li>
            <li>
                <a href="#tab-principales" role="tab" data-toggle="tab">
                    <i class="icon-list"></i>
                    {l s='Productos Principales' mod='escandallo'}
                </a>
            </li>
            <li>
                <a href="#tab-partes" role="tab" data-toggle="tab">
                    <i class="icon-puzzle-piece"></i>
                    {l s='Partes/Diagramas' mod='escandallo'}
                </a>
            </li>
            <li>
                <a href="#tab-productos" role="tab" data-toggle="tab">
                    <i class="icon-shopping-cart"></i>
                    {l s='Productos Asociados' mod='escandallo'}
                </a>
            </li>
            <li>
                <a href="#tab-import" role="tab" data-toggle="tab">
                    <i class="icon-upload"></i>
                    {l s='Importar CSV' mod='escandallo'}
                </a>
            </li>
        </ul>

        <!-- Contenido de los tabs -->
        <div class="tab-content">
            
            <!-- TAB: Configuración General -->
            <div class="tab-pane active" id="tab-config">
                <h3>{l s='Configuración General' mod='escandallo'}</h3>

                <div class="alert alert-success">
                    <h4><i class="icon-link"></i> {l s='URL del Módulo (NUEVA)' mod='escandallo'}</h4>
                    <p>
                        <strong style="font-size: 18px; color: #28a745;">
                            <a href="{$shop_url}escandallo-piezas" target="_blank" style="color: #28a745;">
                                {$shop_url}escandallo-piezas
                            </a>
                        </strong>
                    </p>
                    <p class="text-muted" style="margin-top: 10px;">
                        <small>{l s='URL alternativa: ' mod='escandallo'}
                        <code>{$shop_url}index.php?fc=module&module=escandallo&controller=index</code></small>
                    </p>
                </div>

                <div class="alert alert-info">
                    <h4><i class="icon-wrench"></i> {l s='Activar URL amigable' mod='escandallo'}</h4>
                    <p>{l s='Para que /escandallo-piezas funcione, haz esto:' mod='escandallo'}</p>

                    <ol style="margin: 10px 0;">
                        <li><strong>{l s='1. Regenerar Rutas' mod='escandallo'}</strong>
                            <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post" style="display: inline-block; margin-left: 10px;">
                                <button type="submit" name="regenerateRoutes" class="btn btn-warning btn-sm">
                                    <i class="icon-refresh"></i> {l s='Regenerar' mod='escandallo'}
                                </button>
                            </form>
                        </li>
                        <li>{l s='2. Limpiar caché (Rendimiento)' mod='escandallo'}</li>
                        <li>{l s='3. Verificar URLs amigables (SEO y URLs)' mod='escandallo'}</li>
                    </ol>
                </div>
            </div>

            <!-- TAB: Productos Principales -->
            <div class="tab-pane" id="tab-principales">
                <h3>{l s='Gestión de Productos Principales' mod='escandallo'}</h3>
                
                <!-- Formulario para añadir principal -->
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-plus"></i>
                        {l s='Añadir Producto Principal' mod='escandallo'}
                    </div>
                    <div class="panel-body">
                        <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-lg-3 required">
                                    {l s='Nombre' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <input type="text"
                                           name="nombre_principal"
                                           class="form-control"
                                           placeholder="{l s='Ej: Moto Deportiva, Scooter 125cc...' mod='escandallo'}"
                                           required>
                                    <p class="help-block">
                                        <i class="icon-info-circle"></i> {l s='Nombre del producto principal que contendr\u00e1 varios diagramas' mod='escandallo'}
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3">
                                    {l s='Imagen' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <input type="file"
                                           name="imagen_principal"
                                           accept="image/*">
                                    <p class="help-block">
                                        <i class="icon-picture-o"></i> {l s='Imagen representativa del producto principal. Formatos: JPG, PNG, GIF (Tama\u00f1o recomendado: 800x600px)' mod='escandallo'}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="panel-footer">
                                <button type="submit" name="submitAddPrincipal" class="btn btn-default pull-right">
                                    <i class="process-icon-save"></i>
                                    {l s='Añadir Principal' mod='escandallo'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de principales existentes -->
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-list"></i>
                        {l s='Productos Principales Existentes' mod='escandallo'}
                    </div>
                    <div class="panel-body">
                        {if $principales && count($principales) > 0}
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{l s='ID' mod='escandallo'}</th>
                                        <th>{l s='Nombre' mod='escandallo'}</th>
                                        <th>{l s='Imagen' mod='escandallo'}</th>
                                        <th>{l s='Fecha creación' mod='escandallo'}</th>
                                        <th class="text-center">{l s='Acciones' mod='escandallo'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$principales item=principal}
                                        <tr>
                                            <td>{$principal.id_principal}</td>
                                            <td><strong>{$principal.nombre|escape:'html':'UTF-8'}</strong></td>
                                            <td>
                                                {if $principal.imagen}
                                                    <img src="{$module_dir}views/img/principales/{$principal.imagen}" 
                                                         alt="{$principal.nombre|escape:'html':'UTF-8'}" 
                                                         style="max-width: 50px;">
                                                {else}
                                                    <em>{l s='Sin imagen' mod='escandallo'}</em>
                                                {/if}
                                            </td>
                                            <td>{$principal.date_add|date_format:'%d/%m/%Y %H:%M'}</td>
                                            <td class="text-center">
                                                <a href="{$link->getModuleLink('escandallo', 'partes', ['id_principal' => $principal.id_principal])}"
                                                   class="btn btn-default btn-sm"
                                                   target="_blank"
                                                   title="{l s='Ver en Front' mod='escandallo'}">
                                                    <i class="icon-eye"></i>
                                                </a>
                                                <a href="javascript:void(0);"
                                                   class="btn btn-primary btn-sm btn-edit-principal"
                                                   data-id="{$principal.id_principal}"
                                                   data-nombre="{$principal.nombre|escape:'html':'UTF-8'}"
                                                   data-imagen="{$principal.imagen}"
                                                   title="{l s='Editar' mod='escandallo'}">
                                                    <i class="icon-edit"></i>
                                                </a>
                                                <a href="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&deletePrincipal=1&id_principal={$principal.id_principal}"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('{l s='¿Estás seguro de eliminar este principal y todas sus partes?' mod='escandallo'}');"
                                                   title="{l s='Eliminar' mod='escandallo'}">
                                                    <i class="icon-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        {else}
                            <div class="alert alert-warning">
                                {l s='No hay productos principales creados a�n.' mod='escandallo'}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>

            <!-- TAB: Partes/Diagramas -->
            <div class="tab-pane" id="tab-partes">
                <h3>{l s='Gestión de Partes/Diagramas' mod='escandallo'}</h3>
                
                <!-- Formulario para añadir parte -->
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-plus"></i>
                        {l s='Añadir Parte/Diagrama' mod='escandallo'}
                    </div>
                    <div class="panel-body">
                        <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-lg-3 required">
                                    {l s='Producto Principal' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <select name="id_principal_parte" class="form-control" required>
                                        <option value="">{l s='Selecciona un principal' mod='escandallo'}</option>
                                        {foreach from=$principales item=principal}
                                            <option value="{$principal.id_principal}">
                                                {$principal.nombre|escape:'html':'UTF-8'}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3 required">
                                    {l s='Nombre de la Parte' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <input type="text"
                                           name="nombre_parte"
                                           class="form-control"
                                           placeholder="{l s='Ej: C\u00fapula, Motor, Suspensi\u00f3n Delantera...' mod='escandallo'}"
                                           required>
                                    <p class="help-block">
                                        <i class="icon-info-circle"></i> {l s='Nombre de la secci\u00f3n o parte del diagrama t\u00e9cnico' mod='escandallo'}
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3 required">
                                    {l s='Imagen del Diagrama' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <input type="file"
                                           name="imagen_parte"
                                           accept="image/*"
                                           required>
                                    <p class="help-block">
                                        <i class="icon-wrench"></i> {l s='Imagen t\u00e9cnica con numeraci\u00f3n de referencia para identificar cada pieza. IMPORTANTE: Los n\u00fameros en la imagen deben coincidir con el "N\u00famero en Imagen" de los productos asociados' mod='escandallo'}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="panel-footer">
                                <button type="submit" name="submitAddParte" class="btn btn-default pull-right">
                                    <i class="process-icon-save"></i>
                                    {l s='Añadir Parte' mod='escandallo'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de partes existentes -->
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-list"></i>
                        {l s='Partes/Diagramas Existentes' mod='escandallo'}
                    </div>
                    <div class="panel-body">
                        {if $partes && count($partes) > 0}
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{l s='ID' mod='escandallo'}</th>
                                        <th>{l s='Principal' mod='escandallo'}</th>
                                        <th>{l s='Nombre' mod='escandallo'}</th>
                                        <th>{l s='Imagen' mod='escandallo'}</th>
                                        <th>{l s='Fecha creación' mod='escandallo'}</th>
                                        <th class="text-center">{l s='Acciones' mod='escandallo'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$partes item=parte}
                                        <tr>
                                            <td>{$parte.id_parte}</td>
                                            <td>{$parte.nombre_principal|escape:'html':'UTF-8'}</td>
                                            <td><strong>{$parte.nombre|escape:'html':'UTF-8'}</strong></td>
                                            <td>
                                                {if $parte.imagen}
                                                    <img src="{$module_dir}views/img/partes/{$parte.imagen}" 
                                                         alt="{$parte.nombre|escape:'html':'UTF-8'}" 
                                                         style="max-width: 50px;">
                                                {else}
                                                    <em>{l s='Sin imagen' mod='escandallo'}</em>
                                                {/if}
                                            </td>
                                            <td>{$parte.date_add|date_format:'%d/%m/%Y %H:%M'}</td>
                                            <td class="text-center">
                                                <a href="{$link->getModuleLink('escandallo', 'productos', ['id_parte' => $parte.id_parte])}"
                                                   class="btn btn-default btn-sm"
                                                   target="_blank"
                                                   title="{l s='Ver en Front' mod='escandallo'}">
                                                    <i class="icon-eye"></i>
                                                </a>
                                                <a href="javascript:void(0);"
                                                   class="btn btn-primary btn-sm btn-edit-parte"
                                                   data-id="{$parte.id_parte}"
                                                   data-id-principal="{$parte.id_principal}"
                                                   data-nombre="{$parte.nombre|escape:'html':'UTF-8'}"
                                                   data-imagen="{$parte.imagen}"
                                                   title="{l s='Editar' mod='escandallo'}">
                                                    <i class="icon-edit"></i>
                                                </a>
                                                <a href="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&deleteParte=1&id_parte={$parte.id_parte}"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('{l s='¿Estás seguro de eliminar esta parte?' mod='escandallo'}');"
                                                   title="{l s='Eliminar' mod='escandallo'}">
                                                    <i class="icon-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        {else}
                            <div class="alert alert-warning">
                                {l s='No hay partes/diagramas creados a�n.' mod='escandallo'}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>

            <!-- TAB: Productos Asociados -->
            <div class="tab-pane" id="tab-productos">
                <h3>{l s='Gestión de Productos Asociados' mod='escandallo'}</h3>
                
                <!-- Formulario para asociar producto -->
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-plus"></i>
                        {l s='Asociar Producto a Parte' mod='escandallo'}
                    </div>
                    <div class="panel-body">
                        <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post" class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-lg-3 required">
                                    {l s='Parte/Diagrama' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <select name="id_parte_producto" class="form-control" required>
                                        <option value="">{l s='Selecciona una parte' mod='escandallo'}</option>
                                        {foreach from=$partes item=parte}
                                            <option value="{$parte.id_parte}">
                                                {$parte.nombre_principal|escape:'html':'UTF-8'} - {$parte.nombre|escape:'html':'UTF-8'}
                                            </option>
                                        {/foreach}
                                    </select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3 required">
                                    {l s='ID del Producto' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <input type="number"
                                           name="id_product"
                                           class="form-control"
                                           min="1"
                                           placeholder="{l s='Ej: 123' mod='escandallo'}"
                                           required>
                                    <p class="help-block">
                                        <i class="icon-barcode"></i> {l s='ID del producto existente en PrestaShop. El producto se ocultar\u00e1 autom\u00e1ticamente del cat\u00e1logo (visibility=none) y solo ser\u00e1 visible a trav\u00e9s del escandallo' mod='escandallo'}
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3 required">
                                    {l s='N\u00famero en Imagen' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <input type="number"
                                           name="numero_imagen"
                                           class="form-control"
                                           min="1"
                                           placeholder="{l s='Ej: 1, 2, 3...' mod='escandallo'}"
                                           required>
                                    <p class="help-block">
                                        <i class="icon-map-marker"></i> {l s='N\u00famero que aparece en el diagrama t\u00e9cnico para identificar esta pieza. Debe coincidir exactamente con el n\u00famero mostrado en la imagen del diagrama' mod='escandallo'}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="panel-footer">
                                <button type="submit" name="submitAddProductoParte" class="btn btn-default pull-right">
                                    <i class="process-icon-save"></i>
                                    {l s='Asociar Producto' mod='escandallo'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Lista de productos asociados -->
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-list"></i>
                        {l s='Productos Asociados Existentes' mod='escandallo'}
                    </div>
                    <div class="panel-body">
                        {if $productos_partes && count($productos_partes) > 0}
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{l s='Principal' mod='escandallo'}</th>
                                        <th>{l s='Parte' mod='escandallo'}</th>
                                        <th>{l s='N� Imagen' mod='escandallo'}</th>
                                        <th>{l s='Referencia' mod='escandallo'}</th>
                                        <th>{l s='Producto' mod='escandallo'}</th>
                                        <th class="text-center">{l s='Acciones' mod='escandallo'}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {foreach from=$productos_partes item=prod}
                                        <tr>
                                            <td>{$prod.nombre_principal|escape:'html':'UTF-8'}</td>
                                            <td>{$prod.nombre_parte|escape:'html':'UTF-8'}</td>
                                            <td><strong>{$prod.numero_imagen}</strong></td>
                                            <td>{$prod.reference|escape:'html':'UTF-8'}</td>
                                            <td>{$prod.name|escape:'html':'UTF-8'}</td>
                                            <td class="text-center">
                                                <a href="javascript:void(0);"
                                                   class="btn btn-primary btn-sm btn-edit-producto"
                                                   data-id="{$prod.id_escandallo_producto}"
                                                   data-id-parte="{$prod.id_parte}"
                                                   data-id-product="{$prod.id_product}"
                                                   data-numero="{$prod.numero_imagen}"
                                                   title="{l s='Editar' mod='escandallo'}">
                                                    <i class="icon-edit"></i>
                                                </a>
                                                <a href="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}&deleteProductoParte=1&id_escandallo_producto={$prod.id_escandallo_producto}"
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('{l s='¿Estás seguro de desasociar este producto?' mod='escandallo'}');"
                                                   title="{l s='Desasociar' mod='escandallo'}">
                                                    <i class="icon-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        {else}
                            <div class="alert alert-warning">
                                {l s='No hay productos asociados a�n.' mod='escandallo'}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>

            <!-- TAB: Importar CSV -->
            <div class="tab-pane" id="tab-import">
                <h3>{l s='Importaci�n Masiva CSV' mod='escandallo'}</h3>
                
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-upload"></i>
                        {l s='Subir Archivo CSV' mod='escandallo'}
                    </div>
                    <div class="panel-body">
                        <div class="alert alert-info">
                            <h4><i class="icon-info-circle"></i> {l s='Formato del CSV' mod='escandallo'}</h4>
                            <p>{l s='El archivo CSV debe tener las siguientes columnas en este orden:' mod='escandallo'}</p>
                            <ol>
                                <li><strong>id_principal:</strong> {l s='ID del producto principal' mod='escandallo'}</li>
                                <li><strong>nombre_principal:</strong> {l s='Nombre del producto principal' mod='escandallo'}</li>
                                <li><strong>id_parte:</strong> {l s='ID de la parte/diagrama' mod='escandallo'}</li>
                                <li><strong>nombre_parte:</strong> {l s='Nombre de la parte/diagrama' mod='escandallo'}</li>
                                <li><strong>imagen_parte:</strong> {l s='Nombre del archivo de imagen de la parte' mod='escandallo'}</li>
                                <li><strong>id_product:</strong> {l s='ID del producto (0 para crear nuevo)' mod='escandallo'}</li>
                                <li><strong>numero_imagen:</strong> {l s='Número de referencia en el diagrama' mod='escandallo'}</li>
                                <li><strong>referencia:</strong> {l s='Referencia del producto' mod='escandallo'}</li>
                                <li><strong>nombre_producto:</strong> {l s='Nombre del producto' mod='escandallo'}</li>
                                <li><strong>descripcion:</strong> {l s='Descripci�n del producto' mod='escandallo'}</li>
                                <li><strong>precio:</strong> {l s='Precio del producto' mod='escandallo'}</li>
                                <li><strong>imagen_producto:</strong> {l s='Nombre del archivo de imagen del producto' mod='escandallo'}</li>
                                <li><strong>stock:</strong> {l s='Cantidad en stock' mod='escandallo'}</li>
                                <li><strong>id_categoria:</strong> {l s='ID de la categor�a de PrestaShop' mod='escandallo'}</li>
                            </ol>
                            <p class="text-muted">
                                <em>{l s='Nota: Las im�genes deben estar previamente subidas en las carpetas correspondientes.' mod='escandallo'}</em>
                            </p>
                        </div>

                        <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group">
                                <label class="control-label col-lg-3 required">
                                    {l s='Archivo CSV' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <input type="file" 
                                           name="csv_file" 
                                           accept=".csv" 
                                           required>
                                </div>
                            </div>
                            
                            <div class="panel-footer">
                                <button type="submit" name="submitImportCSV" class="btn btn-default pull-right">
                                    <i class="icon-upload"></i>
                                    {l s='Importar CSV' mod='escandallo'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Ejemplo de CSV -->
                <div class="panel">
                    <div class="panel-heading">
                        <i class="icon-file-text"></i>
                        {l s='Ejemplo de archivo CSV' mod='escandallo'}
                    </div>
                    <div class="panel-body">
                        <pre>id_principal,nombre_principal,id_parte,nombre_parte,imagen_parte,id_product,numero_imagen,referencia,nombre_producto,descripcion,precio,imagen_producto,stock,id_categoria
1,Moto Deportiva,1,Cupula,cupula.jpg,0,1,REF-001,Pantalla cupula,Pantalla original,99.90,pantalla.jpg,10,5
1,Moto Deportiva,1,Cupula,cupula.jpg,0,2,REF-002,Arandela M6,Arandela de fijación,2.50,arandela.jpg,50,5</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para editar Principal -->
<div class="modal fade" id="modalEditPrincipal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{l s='Editar Producto Principal' mod='escandallo'}</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_principal_edit" id="edit_principal_id">
                    <div class="form-group">
                        <label class="required">{l s='Nombre' mod='escandallo'}</label>
                        <input type="text" name="nombre_principal_edit" id="edit_principal_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{l s='Imagen actual' mod='escandallo'}</label>
                        <div id="edit_principal_imagen_actual"></div>
                        <input type="hidden" name="imagen_actual_principal" id="edit_principal_imagen_hidden">
                    </div>
                    <div class="form-group">
                        <label>{l s='Nueva imagen (opcional)' mod='escandallo'}</label>
                        <input type="file" name="imagen_principal_edit" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancelar' mod='escandallo'}</button>
                    <button type="submit" name="submitEditPrincipal" class="btn btn-primary">{l s='Guardar cambios' mod='escandallo'}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar Parte -->
<div class="modal fade" id="modalEditParte" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{l s='Editar Parte/Diagrama' mod='escandallo'}</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_parte_edit" id="edit_parte_id">
                    <div class="form-group">
                        <label class="required">{l s='Producto Principal' mod='escandallo'}</label>
                        <select name="id_principal_parte_edit" id="edit_parte_id_principal" class="form-control" required>
                            <option value="">{l s='Selecciona un principal' mod='escandallo'}</option>
                            {foreach from=$principales item=principal}
                                <option value="{$principal.id_principal}">{$principal.nombre|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="required">{l s='Nombre' mod='escandallo'}</label>
                        <input type="text" name="nombre_parte_edit" id="edit_parte_nombre" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label>{l s='Imagen actual' mod='escandallo'}</label>
                        <div id="edit_parte_imagen_actual"></div>
                        <input type="hidden" name="imagen_actual_parte" id="edit_parte_imagen_hidden">
                    </div>
                    <div class="form-group">
                        <label>{l s='Nueva imagen (opcional)' mod='escandallo'}</label>
                        <input type="file" name="imagen_parte_edit" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancelar' mod='escandallo'}</button>
                    <button type="submit" name="submitEditParte" class="btn btn-primary">{l s='Guardar cambios' mod='escandallo'}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar Producto Asociado -->
<div class="modal fade" id="modalEditProducto" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{l s='Editar Producto Asociado' mod='escandallo'}</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="id_escandallo_producto_edit" id="edit_producto_id">
                    <div class="form-group">
                        <label class="required">{l s='Parte/Diagrama' mod='escandallo'}</label>
                        <select name="id_parte_producto_edit" id="edit_producto_id_parte" class="form-control" required>
                            <option value="">{l s='Selecciona una parte' mod='escandallo'}</option>
                            {foreach from=$partes item=parte}
                                <option value="{$parte.id_parte}">{$parte.nombre_principal|escape:'html':'UTF-8'} - {$parte.nombre|escape:'html':'UTF-8'}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="required">{l s='ID del Producto' mod='escandallo'}</label>
                        <input type="number" name="id_product_edit" id="edit_producto_id_product" class="form-control" min="1" required>
                    </div>
                    <div class="form-group">
                        <label class="required">{l s='Número en Imagen' mod='escandallo'}</label>
                        <input type="number" name="numero_imagen_edit" id="edit_producto_numero" class="form-control" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">{l s='Cancelar' mod='escandallo'}</button>
                    <button type="submit" name="submitEditProductoParte" class="btn btn-primary">{l s='Guardar cambios' mod='escandallo'}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para vista previa de imágenes -->
<div class="modal fade" id="modalImagePreview" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{l s='Vista previa de imagen' mod='escandallo'}</h4>
            </div>
            <div class="modal-body text-center">
                <img id="preview_image" src="" alt="" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Editar Principal
    $('.btn-edit-principal').on('click', function() {
        var id = $(this).data('id');
        var nombre = $(this).data('nombre');
        var imagen = $(this).data('imagen');

        $('#edit_principal_id').val(id);
        $('#edit_principal_nombre').val(nombre);
        $('#edit_principal_imagen_hidden').val(imagen);

        if (imagen) {
            $('#edit_principal_imagen_actual').html('<img src="{$module_dir}views/img/principales/' + imagen + '" style="max-width: 150px; margin-top: 10px;">');
        } else {
            $('#edit_principal_imagen_actual').html('<em>{l s='Sin imagen' mod='escandallo'}</em>');
        }

        $('#modalEditPrincipal').modal('show');
    });

    // Editar Parte
    $('.btn-edit-parte').on('click', function() {
        var id = $(this).data('id');
        var idPrincipal = $(this).data('id-principal');
        var nombre = $(this).data('nombre');
        var imagen = $(this).data('imagen');

        $('#edit_parte_id').val(id);
        $('#edit_parte_id_principal').val(idPrincipal);
        $('#edit_parte_nombre').val(nombre);
        $('#edit_parte_imagen_hidden').val(imagen);

        if (imagen) {
            $('#edit_parte_imagen_actual').html('<img src="{$module_dir}views/img/partes/' + imagen + '" style="max-width: 150px; margin-top: 10px;">');
        } else {
            $('#edit_parte_imagen_actual').html('<em>{l s='Sin imagen' mod='escandallo'}</em>');
        }

        $('#modalEditParte').modal('show');
    });

    // Editar Producto Asociado
    $('.btn-edit-producto').on('click', function() {
        var id = $(this).data('id');
        var idParte = $(this).data('id-parte');
        var idProduct = $(this).data('id-product');
        var numero = $(this).data('numero');

        $('#edit_producto_id').val(id);
        $('#edit_producto_id_parte').val(idParte);
        $('#edit_producto_id_product').val(idProduct);
        $('#edit_producto_numero').val(numero);

        $('#modalEditProducto').modal('show');
    });

    // Vista previa de imágenes - click en imágenes de las tablas
    $('table img').on('click', function() {
        var src = $(this).attr('src');
        $('#preview_image').attr('src', src);
        $('#modalImagePreview').modal('show');
    });

    // Búsqueda en tablas
    function addTableSearch(tableId) {
        var table = $(tableId);
        if (table.length === 0) return;

        var searchInput = $('<input type="text" class="form-control" placeholder="{l s='Buscar en la tabla...' mod='escandallo'}" style="margin-bottom: 15px;">');
        table.before(searchInput);

        searchInput.on('keyup', function() {
            var value = $(this).val().toLowerCase();
            table.find('tbody tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });
    }

    // Añadir búsqueda a cada tabla
    addTableSearch('#tab-principales table');
    addTableSearch('#tab-partes table');
    addTableSearch('#tab-productos table');
});
</script>

<style>
.escandallo-admin-panel .nav-tabs {
    margin-bottom: 20px;
}

.escandallo-admin-panel .tab-pane {
    padding: 20px 0;
}

.escandallo-admin-panel .panel {
    margin-bottom: 20px;
}

.escandallo-admin-panel .required:after {
    content: " *";
    color: red;
}

/* Mejoras visuales para botones */
.btn-edit-principal,
.btn-edit-parte,
.btn-edit-producto {
    margin-right: 5px;
}

.btn-edit-principal:hover,
.btn-edit-parte:hover,
.btn-edit-producto:hover {
    transform: scale(1.05);
    transition: all 0.2s ease;
}

/* Mejorar imágenes en tablas para que sean clicables */
table img {
    cursor: pointer;
    transition: transform 0.2s ease;
    border-radius: 4px;
}

table img:hover {
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Estilo para alertas mejoradas */
.alert {
    border-radius: 6px;
    border-left: 4px solid;
    animation: slideInDown 0.3s ease;
}

@keyframes slideInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.alert-success {
    border-left-color: #28a745;
    background-color: #d4edda;
}

.alert-danger,
.alert-error {
    border-left-color: #dc3545;
    background-color: #f8d7da;
}

.alert-info {
    border-left-color: #17a2b8;
    background-color: #d1ecf1;
}

.alert-warning {
    border-left-color: #ffc107;
    background-color: #fff3cd;
}

/* Mejorar help-blocks */
.help-block {
    color: #6c757d;
    font-size: 12px;
    margin-top: 5px;
}

.help-block i {
    margin-right: 5px;
}

/* Mejorar modales */
.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 6px 6px 0 0;
}

.modal-header .close {
    color: white;
    opacity: 0.8;
}

.modal-header .close:hover {
    opacity: 1;
}

.modal-title {
    font-weight: 600;
}

/* Estilos para inputs con placeholders */
input::-webkit-input-placeholder,
textarea::-webkit-input-placeholder {
    font-style: italic;
    color: #999;
}

input:-moz-placeholder,
textarea:-moz-placeholder {
    font-style: italic;
    color: #999;
}

/* Mejorar tablas responsivas */
.table-bordered {
    border-radius: 6px;
    overflow: hidden;
}

.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.table tbody tr:hover {
    background-color: #f1f3f5;
    transition: background-color 0.2s ease;
}

/* Loading spinner para botones */
.btn[disabled] {
    position: relative;
    opacity: 0.6;
}
</style>
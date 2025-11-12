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
                <form action="{$smarty.server.REQUEST_URI|escape:'html':'UTF-8'}" method="post" class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-lg-3">
                            {l s='Elementos por página' mod='escandallo'}
                        </label>
                        <div class="col-lg-9">
                            <input type="number" 
                                   name="items_per_page" 
                                   value="{$items_per_page}" 
                                   class="form-control" 
                                   min="1" 
                                   max="100">
                            <p class="help-block">
                                {l s='Número de productos principales o partes a mostrar por página' mod='escandallo'}
                            </p>
                        </div>
                    </div>
                    
                    <div class="panel-footer">
                        <button type="submit" name="submitEscandalloConfig" class="btn btn-default pull-right">
                            <i class="process-icon-save"></i>
                            {l s='Guardar' mod='escandallo'}
                        </button>
                    </div>
                </form>

                <hr>

                <div class="alert alert-info">
                    <h4><i class="icon-info"></i> {l s='Acceso al Escandallo' mod='escandallo'}</h4>
                    <p>
                        {l s='URL del escandallo (Frontend):' mod='escandallo'} 
                        <strong>{$shop_url}escandallo</strong>
                    </p>
                    <p class="text-muted">
                        <small>{l s='Esta es la URL pública donde los clientes verán el escandallo' mod='escandallo'}</small>
                    </p>
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
                                           required>
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
                                        {l s='Formatos permitidos: JPG, PNG, GIF' mod='escandallo'}
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
                                                <a href="{$shop_url}escandallo/principal/{$principal.id_principal}" 
                                                   class="btn btn-default btn-sm" 
                                                   target="_blank"
                                                   title="{l s='Ver en Front' mod='escandallo'}">
                                                    <i class="icon-eye"></i>
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
                                {l s='No hay productos principales creados aún.' mod='escandallo'}
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
                                           required>
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
                                        {l s='Imagen técnica con numeración de referencia' mod='escandallo'}
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
                                                <a href="{$shop_url}escandallo/parte/{$parte.id_parte}" 
                                                   class="btn btn-default btn-sm" 
                                                   target="_blank"
                                                   title="{l s='Ver en Front' mod='escandallo'}">
                                                    <i class="icon-eye"></i>
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
                                {l s='No hay partes/diagramas creados aún.' mod='escandallo'}
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
                                           required>
                                    <p class="help-block">
                                        {l s='ID del producto existente en PrestaShop' mod='escandallo'}
                                    </p>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="control-label col-lg-3 required">
                                    {l s='Número en Imagen' mod='escandallo'}
                                </label>
                                <div class="col-lg-9">
                                    <input type="number" 
                                           name="numero_imagen" 
                                           class="form-control" 
                                           min="1"
                                           required>
                                    <p class="help-block">
                                        {l s='Número de referencia en el diagrama técnico' mod='escandallo'}
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
                                        <th>{l s='Nº Imagen' mod='escandallo'}</th>
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
                                {l s='No hay productos asociados aún.' mod='escandallo'}
                            </div>
                        {/if}
                    </div>
                </div>
            </div>

            <!-- TAB: Importar CSV -->
            <div class="tab-pane" id="tab-import">
                <h3>{l s='Importación Masiva CSV' mod='escandallo'}</h3>
                
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
                                <li><strong>descripcion:</strong> {l s='Descripción del producto' mod='escandallo'}</li>
                                <li><strong>precio:</strong> {l s='Precio del producto' mod='escandallo'}</li>
                                <li><strong>imagen_producto:</strong> {l s='Nombre del archivo de imagen del producto' mod='escandallo'}</li>
                                <li><strong>stock:</strong> {l s='Cantidad en stock' mod='escandallo'}</li>
                                <li><strong>id_categoria:</strong> {l s='ID de la categoría de PrestaShop' mod='escandallo'}</li>
                            </ol>
                            <p class="text-muted">
                                <em>{l s='Nota: Las imágenes deben estar previamente subidas en las carpetas correspondientes.' mod='escandallo'}</em>
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
</style>
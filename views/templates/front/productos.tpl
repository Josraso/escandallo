{extends file='page.tpl'}

{block name='page_title'}
    <h1 class="escandallo-page-title">{$parte.nombre|escape:'html':'UTF-8'}</h1>
    <p class="escandallo-subtitle">{$parte.nombre_principal|escape:'html':'UTF-8'}</p>
{/block}

{block name='page_content'}
    <div class="escandallo-container">
        <!-- Buscador fijo -->
        <div class="escandallo-search-bar">
            <form action="{$search_url}" method="get" class="escandallo-search-form">
                <div class="input-group">
                    <input type="text" 
                           name="q" 
                           class="form-control escandallo-search-input" 
                           placeholder="{l s='Buscar por referencia o nombre...' mod='escandallo'}"
                           value="{if isset($smarty.get.q)}{$smarty.get.q|escape:'html':'UTF-8'}{/if}"
                           required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary escandallo-search-btn">
                            <i class="fa fa-search"></i>
                            {l s='Buscar' mod='escandallo'}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Botón regresar -->
        <div class="escandallo-navigation">
            <a href="{$partes_url}" class="btn btn-secondary escandallo-btn-back">
                <i class="fa fa-arrow-left"></i>
                {l s='Regresar' mod='escandallo'}
            </a>
        </div>

        <!-- Imagen del diagrama técnico -->
        {if $parte.imagen_url}
            <div class="escandallo-diagram-container">
                <img src="{$parte.imagen_url}" 
                     alt="{$parte.nombre|escape:'html':'UTF-8'}" 
                     class="escandallo-diagram-image img-fluid">
            </div>
        {/if}

        <!-- Tabla de productos -->
        <div class="escandallo-productos-table-container">
            {if $productos && count($productos) > 0}
                <table class="table table-bordered escandallo-productos-table">
                    <thead>
                        <tr>
                            <th>{l s='Referencia' mod='escandallo'}</th>
                            <th>{l s='Código' mod='escandallo'}</th>
                            <th>{l s='Nombre' mod='escandallo'}</th>
                            <th class="text-center">{l s='Stock' mod='escandallo'}</th>
                            <th class="text-right">{l s='Precio' mod='escandallo'}</th>
                            <th class="text-center">{l s='Acciones' mod='escandallo'}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {foreach from=$productos item=producto}
                            <tr class="escandallo-producto-row">
                                <td class="escandallo-producto-ref">
                                    <strong>{$producto.numero_imagen}</strong>
                                </td>
                                <td class="escandallo-producto-code">
                                    {$producto.reference|escape:'html':'UTF-8'}
                                </td>
                                <td class="escandallo-producto-name">
                                    <a href="{$producto.product_url}" class="escandallo-producto-link">
                                        {$producto.name|escape:'html':'UTF-8'}
                                    </a>
                                </td>
                                <td class="text-center escandallo-producto-stock">
                                    {if $producto.tiene_stock}
                                        <span class="escandallo-stock-badge escandallo-stock-available">
                                            <i class="fa fa-check-circle"></i>
                                        </span>
                                    {else}
                                        <span class="escandallo-stock-badge escandallo-stock-unavailable">
                                            <i class="fa fa-times-circle"></i>
                                        </span>
                                    {/if}
                                </td>
                                <td class="text-right escandallo-producto-price">
                                    <strong>{$producto.precio_formateado}</strong>
                                </td>
                                <td class="text-center escandallo-producto-actions">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary escandallo-btn-ver-imagen"
                                            data-imagen="{$producto.imagen_url}"
                                            data-nombre="{$producto.name|escape:'html':'UTF-8'}"
                                            data-referencia="{$producto.reference|escape:'html':'UTF-8'}">
                                        <i class="fa fa-eye"></i>
                                        {l s='Ver' mod='escandallo'}
                                    </button>

                                    <form action="{$urls.pages.cart}" method="post" style="display: inline-block;">
                                        <input type="hidden" name="token" value="{$static_token}">
                                        <input type="hidden" name="id_product" value="{$producto.id_product}">
                                        <input type="hidden" name="qty" value="1">
                                        <input type="hidden" name="add" value="1">
                                        <input type="hidden" name="action" value="update">
                                        <button type="submit"
                                                class="btn btn-sm btn-primary"
                                                data-button-action="add-to-cart"
                                                {if !$producto.puede_comprar}disabled style="opacity: 0.5; cursor: not-allowed;"{/if}>
                                            <i class="fa fa-shopping-cart"></i>
                                            {l s='Añadir' mod='escandallo'}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>
            {else}
                <div class="alert alert-info">
                    {l s='No hay productos disponibles para este diagrama.' mod='escandallo'}
                </div>
            {/if}
        </div>
    </div>
{/block}
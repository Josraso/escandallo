{extends file='page.tpl'}

{block name='page_title'}
    <h1 class="escandallo-page-title">{l s='Búsqueda de Productos' mod='escandallo'}</h1>
    {if $query}
        <p class="escandallo-subtitle">{l s='Resultados para:' mod='escandallo'} "{$query|escape:'html':'UTF-8'}"</p>
    {/if}
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
                           value="{$query|escape:'html':'UTF-8'}"
                           required>
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary escandallo-search-btn">
                            <i class="material-icons">search</i>
                            {l s='Buscar' mod='escandallo'}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Botón regresar -->
        <div class="escandallo-navigation">
            <a href="{$index_url}" class="btn btn-secondary escandallo-btn-back">
                <i class="material-icons">arrow_back</i>
                {l s='Regresar' mod='escandallo'}
            </a>
        </div>

        <!-- Resultados de búsqueda -->
        <div class="escandallo-search-results">
            {if $query && strlen($query) >= 2}
                {if $resultados && count($resultados) > 0}
                    <div class="alert alert-success">
                        {l s='Se encontraron' mod='escandallo'} <strong>{count($resultados)}</strong> {l s='resultados' mod='escandallo'}
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped escandallo-search-table">
                            <thead>
                                <tr>
                                    <th>{l s='Imagen' mod='escandallo'}</th>
                                    <th>{l s='Referencia' mod='escandallo'}</th>
                                    <th>{l s='Nombre' mod='escandallo'}</th>
                                    <th>{l s='Producto Principal' mod='escandallo'}</th>
                                    <th>{l s='Parte/Diagrama' mod='escandallo'}</th>
                                    <th class="text-center">{l s='Acciones' mod='escandallo'}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {foreach from=$resultados item=resultado}
                                    <tr>
                                        <td class="escandallo-search-image">
                                            <img src="{$resultado.imagen_url}" 
                                                 alt="{$resultado.name|escape:'html':'UTF-8'}" 
                                                 class="img-thumbnail"
                                                 style="max-width: 60px;">
                                        </td>
                                        <td>
                                            <strong>{$resultado.reference|escape:'html':'UTF-8'}</strong>
                                        </td>
                                        <td>
                                            {$resultado.name|escape:'html':'UTF-8'}
                                        </td>
                                        <td>
                                            <a href="{$resultado.principal_url}" class="escandallo-link">
                                                {$resultado.nombre_principal|escape:'html':'UTF-8'}
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{$resultado.parte_url}" class="escandallo-link">
                                                {$resultado.nombre_parte|escape:'html':'UTF-8'}
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <a href="{$resultado.parte_url}" 
                                               class="btn btn-sm btn-primary">
                                                {l s='Ver en Diagrama' mod='escandallo'}
                                            </a>
                                        </td>
                                    </tr>
                                {/foreach}
                            </tbody>
                        </table>
                    </div>
                {else}
                    <div class="alert alert-warning">
                        {l s='No se encontraron resultados para tu búsqueda.' mod='escandallo'}
                    </div>
                {/if}
            {else}
                <div class="alert alert-info">
                    {l s='Por favor, introduce al menos 2 caracteres para realizar la búsqueda.' mod='escandallo'}
                </div>
            {/if}
        </div>
    </div>
{/block}
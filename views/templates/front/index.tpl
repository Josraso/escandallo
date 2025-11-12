{extends file='page.tpl'}

{block name='page_title'}
    <h1 class="escandallo-page-title">{l s='Productos Principales' mod='escandallo'}</h1>
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
                            <i class="material-icons">search</i>
                            {l s='Buscar' mod='escandallo'}
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Grid de productos principales -->
        <div class="escandallo-principales-grid">
            {if $principales && count($principales) > 0}
                {foreach from=$principales item=principal}
                    <div class="escandallo-principal-card">
                        <div class="escandallo-card-image">
                            <img src="{$principal.imagen_url}" 
                                 alt="{$principal.nombre|escape:'html':'UTF-8'}" 
                                 class="img-fluid">
                        </div>
                        <div class="escandallo-card-body">
                            <h3 class="escandallo-card-title">{$principal.nombre|escape:'html':'UTF-8'}</h3>
                            <a href="{$link->getModuleLink('escandallo', 'partes', ['id_principal' => $principal.id_principal])}" 
                               class="btn btn-primary escandallo-btn-view">
                                {l s='VER DIAGRAMAS' mod='escandallo'}
                            </a>
                        </div>
                    </div>
                {/foreach}
            {else}
                <div class="alert alert-info">
                    {l s='No hay productos principales disponibles en este momento.' mod='escandallo'}
                </div>
            {/if}
        </div>
    </div>
{/block}
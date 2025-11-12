{extends file='page.tpl'}

{block name='page_title'}
    <h1 class="escandallo-page-title">{l s='Diagramas Disponibles' mod='escandallo'}</h1>
    <p class="escandallo-subtitle">{$principal.nombre|escape:'html':'UTF-8'}</p>
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
            <a href="{$index_url}" class="btn btn-secondary escandallo-btn-back">
                <i class="fa fa-arrow-left"></i>
                {l s='Regresar' mod='escandallo'}
            </a>
        </div>

        <!-- Grid de partes/diagramas -->
        <div class="escandallo-partes-grid">
            {if $partes && count($partes) > 0}
                {foreach from=$partes item=parte}
                    <div class="escandallo-parte-card">
                        <div class="escandallo-card-image">
                            <img src="{$parte.imagen_url}" 
                                 alt="{$parte.nombre|escape:'html':'UTF-8'}" 
                                 class="img-fluid">
                        </div>
                        <div class="escandallo-card-body">
                            <h3 class="escandallo-card-title">{$parte.nombre|escape:'html':'UTF-8'}</h3>
                            <a href="{$link->getModuleLink('escandallo', 'productos', ['id_parte' => $parte.id_parte])}" 
                               class="btn btn-primary escandallo-btn-view">
                                {l s='VER DIAGRAMAS' mod='escandallo'}
                            </a>
                        </div>
                    </div>
                {/foreach}
            {else}
                <div class="alert alert-info">
                    {l s='No hay diagramas disponibles para este producto.' mod='escandallo'}
                </div>
            {/if}
        </div>
    </div>
{/block}
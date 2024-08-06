{block name="ls-search-page"}
    {assign var="key" value= $smarty.session.key}
    {assign var="type" value= $smarty.session.type}
    {assign var="headerDesc" value= $smarty.session.headerDesc}
    {assign var="hinweis" value= $smarty.session.hinweis}
    {assign var="parentCat" value= $smarty.session.parentCat}
    {assign var="level1Cat" value= $smarty.session.parentCat}
    {assign var="img" value= $smarty.session.img}
    {assign var="catIds" value= $smarty.session.cat_ids}
    {assign var="productMarke" value= ""}
    {assign var="productType" value= ""}
    {assign var="catFilters" value= ""}
    {assign "catTitle" value=$key}

    {* in smarty ->  *}
    {assign var="catIds" value= $smarty.session.cat_ids}
    {assign var="key" value= $smarty.session.key}
    {assign var="pids" value= $smarty.session.ids} 

    {if $type == "p"}
        {get_product_by_id productIds=$catIds assign="lsItems"}
    {else}
        {ls_get_prods_by_cat_ids catIds=$catIds  assign="lsItems" resType="prods"}  
    {/if}

    {if $parentCat && !$img}
        
        {ls_get_category_by_id justCat=true assign="pCat" catId=$parentCat}
        {$img = $pCat->getImage()}
        
        {if $pCat->categoryFunctionAttributes['ls_brand']}
            {$productMarke = $pCat->categoryFunctionAttributes['ls_brand']}
        {/if}

        {if $pCat->kOberKategorie}
            {ls_get_category_by_id justCat=true assign="level1Cat" catId=$pCat->kOberKategorie}
            {if $level1Cat}
                {$productType = $level1Cat->cName}
            {/if}
        {/if}
    {/if}
    {if !$headerDesc}
        {$headerDesc={lang key="lsDDSearchProductNummerText"}}
    {/if}
    {* ype->{$type} *}
    {row class="cat-header default drucker listing level-1 center"}
        {col class="col-12"}
            <div class="wrapper">
                <div class="container default-cat">
                    {assign 'druckerMarkeImg' ''} 
                    {row class="content-row"}
                        <div class="col-4 img-col">
                            {sanitize_string dataString=$catTitle assign='toId'}
                            {$cCatTitle = $catTitle}
                            <div class="brand-sec">
                                {if !$img}
                                    {if $type == "p"}
                                        {if $lsItems && isset($lsItems[0]) && isset($lsItems[0]->FunktionsAttribute['stamm_artikelnummer'])} 
                                            {assign "oemN" value=$lsItems[0]->FunktionsAttribute['stamm_artikelnummer']|trim}
                                            {ls_get_passende_modell_img stammArt=$oemN|trim assign="imgUrl" idType="oem"} 
                                             {if $imgUrl}
                                                {$img = $imgUrl[0]}
                                            {/if} 
                                        {/if}    
                                    {else}
                                        {ls_get_category_image catIds=$catIds assign="imgUrl" idType="stamm"}
                                        {if $imgUrl}
                                            {$img = $imgUrl[0]}
                                        {/if}
                                    {/if}
                                {/if}

                                {if !$img}
                                     {image fluid=true lazy=true webp=true class="default-img"
                                                src="{$ShopURL}/media/image/icons/sparsando-icon.svg"  
                                                alt="{$Einstellungen.global.global_shopname}"}   
                                    <h2 toid="{$toId}" class="brand-name default">{$catTitle}</h2>
                                {else}
                                    <div class="img-wrap">            
                                    {assign "imgsize" value= getimagesize($img)}
                                    {assign "w" value=auto}
                                    {assign "h" value="175"}
                                    {if is_array($imgsize) && isset($imgsize[0]) && isset($imgsize[1])}
                                        {$w=$imgsize[0]/($imgsize[1]/$h)}
                                    {/if}  
                                    {image fluid=true lazy=true webp=true class="main-img"
                                        src=$img 
                                        alt=$key
                                        width="{$w}px"
                                        height="{$h}px"
                                        }
                                        {if  $AktuelleKategorie->categoryFunctionAttributes["ls_drucker_marke_img"]}
                                            {$druckerMarkeImg= $AktuelleKategorie->categoryFunctionAttributes["ls_drucker_marke_img"]}
                                            {image fluid=true lazy=true webp=true class="brand-img"
                                            src=$AktuelleKategorie->categoryFunctionAttributes["ls_drucker_marke_img"]
                                            alt="{if empty($imgAlt->cWert)}{$AktuelleKategorie->getName()}{else}{$imgAlt->cWert}{/if}"}
                                        {else}
                                            <h2 toid="{$toId}" class="brand-name">{$catTitle} </h2>
                                        {/if}
                                    </div>
                                {/if}
                            </div>
                        </div>

                        <div class="col text-col arrow-left col-8">
                            <div class="left">
                                {if !empty($headerDesc)}
                                    <div class="title-sec">
                                        {if $type == "p"}
                                            {$headerDesc|trim|replace:"[[PRODUCT_NAME]]":$catTitle|replace:"[[PRODUCT_MARKE]] ":"{$productMarke} "|replace:"[[PRODUCT_TYPE]]":"{$productType}"} 
                                        {else}
                                             {$headerDesc|trim|replace:"[[PRODUCT_NAME]]":$catTitle|replace:"[[PRODUCT_MARKE]] ":"{$productMarke} "|replace:"[[PRODUCT_TYPE]]":"{$productType}"}
                                        {/if}
                                    </div>
                                {/if}
                                {if !empty($hinweis)}
                                    <div class="hinweis-sec">
                                        <span class="hinweis-title">Hinweis</span>
                                        {assign "passendeText" value={lang key="lsDDSearchCatResultPassendeText"}}
                                        {$hinweis|trim|replace:"[[PASSENDE_TEXT]]":$passendeText} 
                                    </div>
                                {/if}

                            </div>
                            <div class="right">
                                <div class="service-sec">
                                    <h4>Ihre Sicherheit</h4>
                                    {lang key="druckerCategoryHeaderText"}
                                </div>
                                <div class="contact-sec">
                                    <h4>Haben Sie Fragen?</h4>
                                    {lang key="contactData"}
                                </div>
                            </div>
                        </div>
                    {/row}
                    

                    {* <div class="fixtext">
                        {lang key="druckerCategoryHeaderText"}
                    </div> *}
                </div>
            </div>
        {/col}
    {/row}

    {* {row class="cat-header level-1 center"}
        {col class="col-12"}
            <div class="wrapper">
                <div class="container">
                    {if $smarty.session.cat_ids}
                        <h2 class="search-key">
                            <span class="label">
                                 {if $type == "p"}
                                    Produkt: 
                                 {else}
                                    Druckermodelle: 
                                 {/if}
                            </span> {$key}
                        </h2> 
                    {/if}
                    <div class="dropdown-search-sec p-t1">
                        {include file='snippets/ls/ls_dropdown_search.tpl' align="center"}
                    </div>
                    <div class="fixtext">
                        {lang key="druckerCategoryHeaderText"}
                    </div>
                </div>
            </div>
        {/col}
    {/row} *}

    <div class="container opc-container">
        {row}
            {col class="has-ctrl-toggle ls-relocate-parent"}
            <div class="sl-filters-wrap ctrl-toggle" parent-wrap=".has-ctrl-toggle"></div>
                {if $lsItems && $lsItems|count > 0}
                    {assign 'style' 'list'}
                    {row class="product-list search-list drucker-list layout-{$style}" id="product-list" itemprop="mainEntity" itemscope=true itemtype="http://schema.org/ItemList"}
                    {assign var="filtersA" value=''}
                    {if $type == "p"}
                        {foreach $lsItems as $Artikel}
                            {assign "grid" value="12"}
                            {assign "gridmd" value="12"}
                            {assign "gridsm" value="12"}
                            {assign "gridxl" value="12"}
                            {assign "flCls" value="ctrl-elem"}

                            <div class="">
                                {ls_get_custom_drucker_filters funcAttr=$Artikel->FunktionsAttribute pushTo=$filtersA assign="lsFilters" }
                                {if isset($lsFilters['oPushto'])}
                                    {$filtersA = "{$filtersA}{$lsFilters['oPushto']}"}
                                    {if isset($lsFilters['cls'])}
                                        {$flCls = $lsFilters['cls']}
                                    {/if}
                                {/if}
                            </div> 

                            {col cols={$grid} md="{if isset($gridmd)}{$gridmd}{/if}"
                            sm="{if isset($gridsm)}{$gridsm}{/if}" xl="{if isset($gridxl)}{$gridxl}{/if}"
                            class="product-wrapper mehr-sparen-{$Artikel->kArtikel} {$flCls}"
                            itemprop="itemListElement" itemscope=true itemtype="http://schema.org/Product"}
                                {if $style === 'list' && !$isMobile}
                                    {block name='productlist-index-include-item-list'}
                                        {include file='productlist/ls_drucker_item_list.tpl' tplscope=$style}
                                    {/block}
                                    <div class="save-more-content">
                                        {* {include file="snippets/ls/ls_buy_more.tpl" artNo=$Artikel->kArtikel} *}
                                    </div>
                                {else}
                                    {block name='productlist-index-include-item-list'}
                                        {include file='productlist/ls_drucker_item_list.tpl' tplscope=$style}
                                    {/block}
                                    <div class="save-more-content">
                                        {* {include file="snippets/ls/ls_buy_more.tpl" artNo=$Artikel->kArtikel} *}
                                    </div>

                                {/if}
                            {/col}
                        {/foreach}
                    {else}

                        
                        {foreach $lsItems as $Artikel}
                            {* {foreach $ArtikelList as $Artikel} *}
                                {assign "grid" value="12"}
                                {assign "gridmd" value="12"}
                                {assign "gridsm" value="12"}
                                {assign "gridxl" value="12"}
                                {assign "flCls" value="ctrl-elem"}

                                <div class="shiv hide">

                                    {ls_get_custom_drucker_filters funcAttr=$Artikel->FunktionsAttribute pushTo=$filtersA assign="lsFilters" } 
                                        {$lsFilters|print_r}
                                    {if is_array($lsFilters) && $lsFilters|count > 0 && isset($lsFilters['oPushto'])}
                                        {$filtersA = "{$filtersA}{$lsFilters['oPushto']}"}
                                        {if isset($lsFilters['cls'])}
                                            {$flCls = $lsFilters['cls']}
                                        {/if} 
                                    {/if}
                                   
                                </div>


                                {col cols={$grid} md="{if isset($gridmd)}{$gridmd}{/if}"
                                sm="{if isset($gridsm)}{$gridsm}{/if}" xl="{if isset($gridxl)}{$gridxl}{/if}"
                                class="product-wrapper mehr-sparen-{$Artikel->kArtikel} {$flCls}"
                                itemprop="itemListElement" itemscope=true itemtype="http://schema.org/Product"}

                                    {if $style === 'list' && !$isMobile}
                                        {block name='productlist-index-include-item-list'}
                                            {include file='productlist/ls_drucker_item_list.tpl' tplscope=$style}
                                        {/block}
                                        <div class="save-more-content">
                                            {include file="snippets/ls/ls_buy_more.tpl" artNo=$Artikel->kArtikel}
                                        </div>
                                    {else}
                                        {block name='productlist-index-include-item-list'}
                                            {include file='productlist/ls_drucker_item_list.tpl' tplscope=$style}
                                        {/block}
                                        <div class="save-more-content">
                                            {include file="snippets/ls/ls_buy_more.tpl" artNo=$Artikel->kArtikel}
                                        </div>
                                    {/if}
                                {/col}
                            {* {/foreach} *}
                        {/foreach}
                    {/if}

                    <div class="shiv hide pushtodata">
                        {if $filtersA }
                            {ls_string_to_custom_filter dataString=$filtersA assign="filtersDA"}
                            {if isset($filtersDA['filters']) && $filtersDA['filters']|count > 0} 
                                <div class="relocate-content" parentwrap=".ls-relocate-parent" containerwrap=".ctrl-toggle">
                                    <h2 class="ctrl-toggle-title sec-title md" >
                                        <span class="">{lang key="lsSearchBy"} <i class="fas fa-arrow-right"></i></span>
                                    </h2>
                                    <div class="sl-filter-btn ctrl-btn active" ctrl="all"><span class="btn-text">{lang key="lsFilterLabelAll"}</span></div>
                                    {foreach $filtersDA['filters'] as $key=>$val}
                                        {if !empty($key)}
                                            <div class="sl-filter-btn ctrl-btn" ctrl="ftype-{$key}"><span class="btn-text">
                                                {assign var="flabel" value={lang key="lsFilterLabel{$val|trim}"}} 
                                                {if $flabel|strstr:"#"}
                                                    {$val}
                                                {else}
                                                    {$flabel}
                                                {/if}
                                            </span></div>
                                        {/if}
                                    {/foreach}
                                </div>
                            {/if}
                        {/if}
                    </div>

                    {col class="col-12 nl-col pt-3"}
                        <div class="ls-add-newsletter"></div> 
                    {/col} 

                {/row}
                {else}
                    {col class="col-12 container anfrage-form-container pt-3"}
                        <div class="anfrage-form-wrapper drucker-anfrage">
                            <div class="form-wrap">
                                {assign "formTitle" value="Kein Produkt gefunden? Frag uns."} 
                                {assign "successText" value="Danke für anfrage"} 
                                {assign "failtext" value="Danke für anfrage"} 
                                {if {lang key="lsProdAnfrageTitle"} }
                                    {$formTitle ={lang key="lsProdAnfrageTitle"}} 
                                {/if}

                                {if {lang key="lsProdAnfrageSuccess"} }
                                    {$successText ={lang key="lsProdAnfrageSuccess"}} 
                                {/if}
                                {if {lang key="lsProdAnfrageFail"} }
                                    {$failtext ={lang key="lsProdAnfrageFail"}} 
                                {/if}

                                {$successText = $successText|base64_encode}
                                {$failtext = $failtext|base64_encode}
                                {$formTitle = $formTitle|base64_encode}
                                {$arttitle = $catTitle|base64_encode}
                                <div class="ls-add-paform" >
                                    <div class="form-details" formtype="drucker" successtext="{$successText}"  failtext="{$failtext}" formtitle="{$formTitle}" arttitle="{$arttitle}"  artno=""></div> 
                                </div>   
                            </div>  
                        </div>
                    {/col}
                {/if}
                {$data}
            {/col}
        {/row}
    </div>
    {literal}
        <style>
            #dp-search #main-wrapper>#content-wrapper>#content .opc-container {
                padding-top: 3em;
            }

        </style>
    {/literal}
{/block}
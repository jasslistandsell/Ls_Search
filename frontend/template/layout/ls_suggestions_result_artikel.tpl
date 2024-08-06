{block name="ls-suggestions-result-artikel"}
    {*------ database results *}
    {if $dbResult && is_array($dbResult) && $dbResult|count > 0}
        <div class="rcontent">
            {foreach $dbResult as $rows}
                {if $rows->kArtikel}
                    <a href="{$ShopURL}/{$rows->cSeo}" id="{$rows->kArtikel}" class="prods-link">
                        <div class="link-content">
                                {* {assign "prodImg" value="{$ShopURL}/media/image/product/{$rows->kArtikel}/xs/{$rows->artcPfad}"} 
                                {assign "imgsize" value= getimagesize($prodImg)}
                                {assign "w" value=40}
                                {assign "h" value=40}
                                {if is_array($imgsize) && isset($imgsize[0]) && isset($imgsize[1])}
                                    {$h=$imgsize[1]/($imgsize[0]/$w)}
                                {else}
                                    {$prodImg = "{$ShopURL}/gfx/keinBild.gif"}
                                {/if}

                                {assign "imgsize" value= getimagesize($prodImg)}
                                {if is_array($imgsize) && isset($imgsize[0]) && isset($imgsize[1])}
                                    {$h=$imgsize[1]/($imgsize[0]/$w)}
                                {/if}
                                {image fluid=true lazy=true webp=true 
                                    src=$prodImg
                                    alt="{$rows->cName|escape:'html'}"
                                    width="{$w}px"
                                    height="{$h}px"
                                } *}

                            {* <div class="img-wrap" 
                                style="
                                width:35px; height:35px; 
                                background-image:url({$ShopURL}/media/image/product/{$rows->kArtikel}/xs/{$rows->artcPfad});
                                background-size:100%;
                                background-position:center;
                                margin:0 auto;
                                margin-right:5px;
                            "></div> *}
                            <span class="text-clamp-2 name">{$rows->cName}</span>
                            <div class="preis-wrap">
                            {$rows->kArtikel->$preisNetto}
                            </div>
                        </div>
                    </a>
                {/if}
            {/foreach}
        </div>
    {/if}
    {*----- default result *} 
    {if $prodsList && is_array($prodsList) && $prodsList|count > 0}
        <div class="rcontent">
            {foreach $prodsList as $rows}
                {if $rows->kArtikel}
                    <a href="{$rows->getURL()}" id="{$rows->kArtikel}" class="prods-link">
                        <div class="link-content">
                            <div class="img-wrap">
                                {* {$image = $rows->Bilder[0]}
                                 {assign "imgsize" value= getimagesize($image->cURLMini)}
                                {assign "w" value=40}
                                {assign "h" value=40}
                                {if is_array($imgsize) && isset($imgsize[0]) && isset($imgsize[1])}
                                    {$h=$imgsize[1]/($imgsize[0]/$w)}
                                {/if}
                                {image fluid=true lazy=true webp=true 
                                    src=$image->cURLMini
                                    alt="{$rows->cName|escape:'html'}"
                                    width="{$w}px"
                                    height="{$h}px"
                                } *}
                            </div>
                            {* <div class="img-wrap" 
                                style="
                                width:35px; height:35px; 
                                background-image:url({$image->cURLMini});
                                background-size:100%;
                                background-position:center;
                                margin:0 auto;
                                margin-right:5px;
                            "></div> *}
                            <span class="text-clamp-2 name">{$rows->cName}</span>
                            <div class="preis-wrap">
                            {$rows->kArtikel->$preisNetto}
                            </div>
                        </div>
                    </a>
                {/if}
            {/foreach}
        </div>
    {/if} 
{/block}
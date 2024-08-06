{block name="ls-suggestions-result-categories"}
    {*----- database results ------*}
    {if $dbResult && is_array($dbResult) && $dbResult|count > 0}
        {assign "katsA" value=array()}
        {foreach $dbResult as $rows}
            {if $rows->kKategorie && !$rows->kKategorie|in_array:$katsA}
                {$katsA[] = $rows->kKategorie}
                <a href="{$ShopURL}/{$rows->katcSeo}" id="{$rows->kKategorie}" class="cats-link" title="{$rows->katName|escape:'html'}">
                    <div class="link-content">
                            {* {assign "prodImg" value="{$ShopURL}/media/image/category/{$rows->kArtikel}/sm/{$rows->katcPfad}"} 
                            {assign "imgsize" value= getimagesize($prodImg)}
                            {assign "w" value=90}
                            {assign "h" value=40}
                            {if is_array($imgsize) && isset($imgsize[0]) && isset($imgsize[1])}
                            {else}
                                {$prodImg="{$ShopURL}/media/image/icons/sparsando-icon-light.svg"}
                            {/if} *}
                        <div class="img-wrap" 
                            style="
                            width:90px; height:90px; 
                            background-image:url({$ShopURL}/media/image/category/{$rows->kKategorie}/sm/{$rows->katcPfad});
                            background-size:100%;
                            background-position:center;
                            margin:0 auto;
                        "></div>
                        <span class="text-clamp-2 name">{$rows->katName}</span> 
                    </div>
                </a>
                {* {if $rows->katType && strtolower($rows->katType) == "drucker"}
                    <a href="{$ShopURL}/{$rows->katcSeo}" id="{$rows->kKategorie}" class="cats-link" title="{$rows->katName|escape:'html'}">
                        <div class="link-content">
                            <div class="img-wrap">
                            </div>
                            <span class="text-clamp-2 name">{$rows->katName}</span> 
                        </div>
                    </a>
                {else}
                    <a href="{$ShopURL}/{$rows->katcSeo}" id="{$rows->kKategorie}" class="cats-link" title="{$rows->katName|escape:'html'}">
                    <div class="link-content">
                            <div class="img-wrap">
                            </div>
                         <span class="text-clamp-2 name">{$rows->katName}</span> 
                        </div>
                    </a>    
                {/if} *}
            {/if}
        {/foreach}
    {/if}

    {*----- default results ------*}
    {if $catList && $catList|count > 0}
        {foreach $catList as $category}
            {if $category->getID()}
                <a href="{$category->getURL()}" id="{$category->getID()}" class="cats-link" title="{$category->getName()|escape:'html'}">
                    <div class="link-content">
                        <div class="img-wrap">
                            {* {assign "imgsize" value= getimagesize($category->getImageURL(\JTL\Media\Image::SIZE_SM))}
                            {assign "w" value=90}
                            {assign "h" value=90}
                            {if is_array($imgsize) && isset($imgsize[0]) && isset($imgsize[1])}
                                {$h=$imgsize[1]/($imgsize[0]/$w)}
                            {/if}
                            {image fluid=true lazy=true webp=true 
                                src=$category->getImageURL(\JTL\Media\Image::SIZE_SM)
                                alt="{$category->getName()|escape:'html'}"
                                width="{$w}px"
                                height="{$h}px"
                            } *}
                            <div class="img-wrap" 
                                style="
                                width:90px; height:90px; 
                                background-image:url({$category->getImageURL(\JTL\Media\Image::SIZE_SM)});
                                background-size:100%;
                                background-position:center;
                                margin:0 auto;
                            "></div>
                            
                        </div>
                        <span class="text-clamp-2 name">{$category->getShortName()}</span>
                    </div>
                </a>
            {/if}
        {/foreach}
    {/if}
{/block}
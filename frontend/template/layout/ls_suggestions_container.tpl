{block name="ls-suggestions-container"}
    <link rel="stylesheet" href="{$ShopUrl}/plugins/ls_search/frontend/css/suggest.css"> 
    <div class="ls-suggestion">
        <div class="ls-suggestion-wrap">
            <div class="sg-content-wrap">

                <div class="sg-sec-wrap" >
                    <div class="prod-sg sg-sec">
                        <div class="prod-sg-title sg-title"><span class="label">{lang key="lsSearchProductsResultLabel"}</span></div>
                        <div class="prod-sg-content sg-content custom-scroll">
                            <div class="content-wrap">
                            {$prodTpl}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="sg-sec-wrap" >
                    <div class="cat-sg sg-sec"> 
                        <div class="cat-sg-title sg-title"><span class="label">{lang key="lsSearchCatResultLabel"}</span></div>
                        <div class="cat-sg-content sg-content custom-scroll">
                            <div class="content-wrap">
                            {$catsTpl}
                                {if $catList && $catList|count > 0}
                                    {foreach $catList as $category}
                                        {if $category->getID()}
                                            <a href="{$category->getURL()}" id="{$category->getID()}" class="cats-link" title="{$category->getName()|escape:'html'}">
                                                <div class="link-content">
                                                    <div class="img-wrap">
                                                        {assign "imgsize" value= getimagesize($category->getImageURL(\JTL\Media\Image::SIZE_SM))}
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
                                                        }
                                                    </div>
                                                    <span class="text-clamp-2 name">{$category->getShortName()}</span>
                                                </div>
                                            </a>
                                        {/if}
                                    {/foreach}
                                {/if}
                            </div>
                        </div> 
                    </div>
                </div>

            </div>
            {* <div class="sg-links-wrap">
                <div class="sg-links-sec">
                    <span class="sec-title sm">Nichts gefunden?</span> <br>
                </div>
                <div class="sg-links-sec">
                    <span class="sec-title sm">Anrufen:</span><br> <a href="tel:+4930123456789">+49 30 1234 5678</a><br>
                    <span class="sec-title sm">Email:</span><br> <a href="mailto:info@enesse-deutschland.de">info@enesse-deutschland.de</a><br>  
                </div>
            </div> *}
        </div>
    </div>
{/block}
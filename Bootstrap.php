<?php declare(strict_types=1);

namespace Plugin\ls_search;

use JTL\Alert\Alert;
use JTL\Catalog\Category\Kategorie;
use JTL\Helpers\Category;
use JTL\Catalog\Category\KategorieListe;
use JTL\Session\Frontend;
use JTL\Catalog\Product\Artikel;
use JTL\Catalog\Product\ArtikelListe;
use JTL\Cart\CartHelper;
use JTL\Filter\ProductFilter;
use JTL\Filter\Config;
use JTL\Consent\Item;
use JTL\Events\Dispatcher;
use JTL\Events\Event;
use JTL\Helpers\Form; 
use JTL\Helpers\Request;
use JTL\Link\LinkInterface;
use JTL\Plugin\Bootstrapper;
use JTL\Shop;
use JTL\Helpers\Text;
use JTL\Mail\Mail\Mail;
use JTL\Mail\Mailer;
use JTL\Language\LanguageHelper; 
use JTL\Smarty\JTLSmarty;
use Smarty;

/**
 * Class Bootstrap
 * @package Plugin\ls_search
 */
class Bootstrap extends Bootstrapper  
{
/**
     * @inheritdoc
     */
    public function boot(Dispatcher $dispatcher)
    {
        parent::boot($dispatcher);

        if (Shop::isFrontend() === false) {
            return;
        }

        $plugin       = $this->getPlugin();
        
        /**
        * 
        * AJAX REQUESTS
        * 
        */
        $dispatcher->listen(
            'shop.hook.' . \HOOK_IO_HANDLE_REQUEST , function (array &$args){
                $args['io']->register('lsGetSuggestionContainer', [$this, 'lsGetSuggestionContainer']);
                $args['io']->register('lsGetSearchSuggestions', [$this, 'lsGetSearchSuggestions']);
        });

        /**
         * 
         * PQ QUERY
         * 
         */
        // $listenTo = [
        //     \HOOK_SMARTY_OUTPUTFILTER,
        // ];
        // foreach ($listenTo as $hook) {
        //     $dispatcher->listen('shop.hook.' . $hook, [$hooks, 'lsexec' . $hook]);
        // }
    }


    public function getPluginConfig($params = null){
        return $plugin->getConfig();
    }

    /**
     * ======================================================================
     * AJAX FUNCTIONS
     * ======================================================================
     */

    /**
     * 
     *  GET SEARCH SUGGESTIONS
     * 
     * 
     */ 
    public function lsGetSearchSuggestions($params):array 
    {
        $result = [
            'success' => true,
            'data' => 'some return data', 
            'id' => '', 
        ];
        $tempR = [];
        $rC = 0;
        $htmlData = ""; 
        $isDefaultdata = false;

        $path = $this->getPlugin()->getPaths()->getFrontendPath(); 
        $shopURL = Shop::getURL();
        $db = Shop::Container()->getDB();
        if($params['type'] == 'suggestion' && !empty($params['key']) && strlen($params['key']) > 2){
            $key = trim($params['key']);
            $keyV = strtolower(trim($params['key']));

            $pattern = '/[^a-zA-Z0-9äÄöÖüÜ]/ig';
            $keyVC = preg_replace($pattern, '%', $keyV);
            $keyVE = $this->trimSpecialCharacters($keyV);
            if(strpos($key, " ") != false){
                $keyA = explode(" ", $key);
            } else{
                $keyA[] = $key; 
            }

            $qName = "";
            $qNameOr = "";
            $qKeywords = "";
            $qKeywordsOr = "";
            
            $wrdsA = [];
            if(is_array($keyA) && count($keyA) > 0){
                foreach($keyA as $sKey){
                    $scA = str_split($sKey);
                    if(count($scA) > 3){ 
                        $sKeyQ = $sKey;
                        for($i=1; $i<(count($scA) - 1); $i++){
                            $wrdsA[] = substr_replace($sKeyQ, '%', $i, 1);
                        }
                        for($i=1; $i<(count($scA) - 2); $i++){
                            $str = substr_replace($sKeyQ, '%', $i, 1);
                            $wrdsA[] = substr_replace($str, '%', $i +1, 1);    
                        }
                    }


                    if(!empty($qName)){
                        $qName .= " AND "; 
                        $qKeywords .= " AND "; 
                        $qNameOr .= " OR "; 
                        $qKeywordsOr .= " OR "; 
                    }
                    
                    $sKeyVE = $this->trimSpecialCharacters($sKey);
                    $qName .= ' art.cName LIKE "%'.$sKeyVE.'%"'; 
                    $qKeywords .= ' art.cSuchbegriffe LIKE "%'.$sKeyVE.'%"';    
                    $qNameOr .= ' art.cName LIKE "%'.$sKeyVE.'%"'; 
                    $qKeywordsOr .= ' art.cSuchbegriffe LIKE "%'.$sKeyVE.'%"'; 
                }
            }

            $extraQ = "";
            $extraKQ = "";
            if(count($wrdsA) > 0){
                foreach($wrdsA as $wrds){
                    if(!empty($extraQ)){
                        $extraQ .= ' OR ';
                        $extraKQ .= ' OR ';
                    }
                    $extraQ .= ' art.cName LIKE "%'.$wrds.'%"'; 
                    $extraKQ .= ' art.cSuchbegriffe LIKE "%'.$wrds.'%"';     
                }
            }

            // search query
            $results  = $db->getObjects(
                'SELECT art.kArtikel, art.fStandardpreisNetto as preisNetto, art.fMwSt as mwst, kat.cName as katName, kat.nLevel as katLevel, katAtr.cWert as katType, artPic.cpfad as artcPfad, artAttr.cWert as stamm, katPic.cPfad AS katcPfad, kat.cSeo as katcSeo,  katArt.kKategorie, art.cName, art.cSeo, art.cSuchbegriffe 
                FROM `tartikel` AS art 
                LEFT JOIN tartikelattribut AS artAttr ON artAttr.kArtikel = art.kArtikel AND artAttr.cName = "stamm_artikelnummer"
                LEFT JOIN tkategorieartikel AS katArt ON katArt.kArtikel = art.kArtikel 
                LEFT JOIN tkategorie AS kat ON katArt.kKategorie = kat.kKategorie 
                LEFT JOIN tkategorieattribut AS katAtr ON katAtr.kKategorie = kat.kKategorie AND katAtr.cName = "ls_products_type"
                LEFT JOIN tkategoriepict AS katPic ON katPic.kKategorie = kat.kKategorie 
                LEFT JOIN tartikelpict AS artPic ON artPic.kArtikel = art.kArtikel  AND artPic.nNr = 1 
                WHERE 
                art.cName LIKE  "%'.$key.'%" OR '.$qName.' 
                OR art.cSuchbegriffe LIKE  "%'.$key.'%" OR '.$qKeywords.'
                ORDER BY FIELD(art.cName, artAttr.cWert) DESC LIMIT 9;    
                ',    
            );

            $results = \array_merge($results); 
            $katA = [];
            $katADL2Html = "";

            $prodTpl = "no data";
            if($results){
                $prodTpl = Shop::Smarty()->assign("dbResult", $results)
                ->fetch($path . 'template/layout/ls_suggestions_result_artikel.tpl');  
                $result["prodsJSON"] = json_encode($prodTpl);

                $catsTpl = Shop::Smarty()->assign("dbResult", $results)
                ->fetch($path . 'template/layout/ls_suggestions_result_category.tpl');  
                $result["catsJSON"] = json_encode($catsTpl); 

                $result["rs"] = $results;
            }

        }else{
            $defaultData = $this->getDefautlSearchData();
            if($defaultData){
                $catsTpl    = $defaultData["defaultCatsTpl"];   
                $result["catsJSON"] = $catsTpl;
        
                $prodsTpl   = $defaultData["defaultProdsTpl"];
                $result["prodsJSON"] = $prodsTpl; 
            }
        }

        return $result;

    }    

    /**
     * 
     *  GET SEARCH SUGGESTIONS CONTAINER
     * 
     * 
     */
    public function lsGetSuggestionContainer($params):array{
        $result["status"] = "ok";
        $path = $this->getPlugin()->getPaths()->getFrontendPath(); 

        $searchHereText = Shop::Lang()->get('lsSearchHereText');

        // Get Random products
        $db = Shop::Container()->getDB();
        $randproduct  = $db->getObjects(
            'SELECT art.kArtikel, art.fStandardpreisNetto as preisNetto, art.fMwSt as mwst, kat.cName as katName, kat.nLevel as katLevel, katAtr.cWert as katType, artPic.cpfad as artcPfad, artAttr.cWert as stamm, katPic.cPfad AS katcPfad, kat.cSeo as katcSeo,  katArt.kKategorie, art.cName, art.cSeo, art.cSuchbegriffe 
            FROM `tartikel` AS art 
            LEFT JOIN tartikelattribut AS artAttr ON artAttr.kArtikel = art.kArtikel AND artAttr.cName = "stamm_artikelnummer"
            LEFT JOIN tkategorieartikel AS katArt ON katArt.kArtikel = art.kArtikel 
            LEFT JOIN tkategorie AS kat ON katArt.kKategorie = kat.kKategorie 
            LEFT JOIN tkategorieattribut AS katAtr ON katAtr.kKategorie = kat.kKategorie AND katAtr.cName = "ls_products_type"
            LEFT JOIN tkategoriepict AS katPic ON katPic.kKategorie = kat.kKategorie 
            LEFT JOIN tartikelpict AS artPic ON artPic.kArtikel = art.kArtikel  AND artPic.nNr = 1
            WHERE art.fStandardpreisNetto > 0 
            ORDER BY RAND()
            LIMIT 9; 
            ',    
        );

        $randproduct = \array_merge($randproduct); 

        $prodTpl = Shop::Smarty()->assign("dbResult", $randproduct)
        ->fetch($path . 'template/layout/ls_suggestions_result_artikel.tpl');  

        $lsSugTpl = Shop::Smarty()->assign("pluginFrontendPath", $path) 
        ->assign("searchHereText", $searchHereText)
        // ->assign("prodTpl", $prodTpl)
        ->fetch($path . 'template/layout/ls_suggestions_container.tpl');

        $result['path']= json_encode($path); 

        $result['jsonData']= json_encode($lsSugTpl); 
        $result['content']= base64_encode($lsSugTpl); 
        $result['htmlencode']= htmlentities($lsSugTpl); 
        $result['searchHereText']= json_encode($searchHereText); 

        return $result;
    }

    /**
     * ============================================================
     *  HELPER FUNCTIONS
     * ============================================================
     */

    // GET DEFAULT CATEGORIES
    function getDefautlSearchData(){
        $path                   = $this->getPlugin()->getPaths()->getFrontendPath(); 
        $lsDefaultSearchData    = Shop::Container()->getCache()->get("lsDefaultSearchData");
        $defaultCatsTpl       = $lsDefaultSearchData && isset($lsDefaultSearchData["defaultCatsTpl"]) ? $lsDefaultSearchData["defaultCatsTpl"] : false;
        $defaultProdsTpl       = $lsDefaultSearchData && isset($lsDefaultSearchData["defaultProdsTpl"]) ? $lsDefaultSearchData["defaultProdsTpl"] : false;

        if(!$defaultCatsTpl){
            $categories = Category::getInstance();
            $catList       = $categories->combinedGetAll();

            if(!$defaultProdsTpl){
                $params = [
                    'kKategorie' => 0,
                    'nSortierung' => 1, 
                    'nLimit' => 10,
                    'kSuchspecial'=> 4,
                    'bReturn' => true
                ];
                $prodsList = $this->ls_getProductList($params);
            }

            // default cats tpl
            $defaultCatsTpl = Shop::Smarty()->assign("pluginFrontendPath", $path)
            ->assign("catList", $catList)
            ->fetch($path . 'template/layout/ls_suggestions_result_category.tpl');

            // default prods tpl
            $defaultProdsTpl = Shop::Smarty()->assign("pluginFrontendPath", $path)
            ->assign("prodsList", $prodsList)
            ->fetch($path . 'template/layout/ls_suggestions_result_artikel.tpl');
            
            $lsDefaultSearchData["defaultProdsTpl"]     = json_encode($defaultProdsTpl);
            $lsDefaultSearchData["defaultProdsList"]    = $prodsList;
            $lsDefaultSearchData["defaultCatsTpl"]      = json_encode($defaultCatsTpl);
            $lsDefaultSearchData["defaultCatsList"]     = $catList;
            Shop::Container()->getCache()->set("lsDefaultSearchData", null, $lsDefaultSearchData);
        }


        return $lsDefaultSearchData;
    } 

    // trim special characters 
    function trimSpecialCharacters($string){
        $result = strtolower($string);
        if($string){
            $regex = "/[^a-zA-Z0-9äÄöÖüÜ]/ig";
            $chars = array(",", ".", "+", "-", "*", "|", " ", "\\", "*", "?", "§", "!", "<", ">", "@", "€", "#", "%", "^", "&");
            foreach($chars as $sChar){
                $result = str_replace($sChar, "",$result);
            }
        }

        return $result;
    }

    /*
     TRIM STRING TO LENGTH 
    */
    function trimString($dataString, $length = 60){
        $res = $dataString;
        if($dataString && strlen($dataString) >= $length){
            $res = substr($dataString, 0, $length).'...'; 
        }

        return $res;
    }

     /**
     * @param array                $params
     * @param Smarty_Internal_Data $smarty
     * @return array|void
     */
    public function ls_getProductList($params)
    {
        $limit                 = (int)($params['nLimit'] ?? 10);
        $sort                  = (int)($params['nSortierung'] ?? 0);
        $assignTo              = (isset($params['cAssign']) && \strlen($params['cAssign']) > 0)
            ? $params['cAssign']
            : 'oCustomArtikel_arr';
        $characteristicFilters = isset($params['cMerkmalFilter'])
            ? ProductFilter::initCharacteristicFilter(\explode(';', $params['cMerkmalFilter']))
            : [];
        $searchFilters         = isset($params['cSuchFilter'])
            ? ProductFilter::initSearchFilter(\explode(';', $params['cSuchFilter']))
            : [];
        $params                = [
            'kKategorie'             => $params['kKategorie'] ?? null,
            'kHersteller'            => $params['kHersteller'] ?? null,
            'kArtikel'               => $params['kArtikel'] ?? null,
            'kVariKindArtikel'       => $params['kVariKindArtikel'] ?? null,
            'kSeite'                 => $params['kSeite'] ?? null,
            'kSuchanfrage'           => $params['kSuchanfrage'] ?? null,
            'kMerkmalWert'           => $params['kMerkmalWert'] ?? null,
            'kSuchspecial'           => $params['kSuchspecial'] ?? null,
            'kKategorieFilter'       => $params['kKategorieFilter'] ?? null,
            'kHerstellerFilter'      => $params['kHerstellerFilter'] ?? null,
            'nBewertungSterneFilter' => $params['nBewertungSterneFilter'] ?? null,
            'cPreisspannenFilter'    => $params['cPreisspannenFilter'] ?? '',
            'kSuchspecialFilter'     => $params['kSuchspecialFilter'] ?? null,
            'nSortierung'            => $sort,
            'MerkmalFilter_arr'      => $characteristicFilters,
            'SuchFilter_arr'         => $searchFilters,
            'nArtikelProSeite'       => $params['nArtikelProSeite'] ?? null,
            'cSuche'                 => $params['cSuche'] ?? null,
            'seite'                  => $params['seite'] ?? null,
        ];
        if ($params['kArtikel'] !== null) {
            $products = [];
            if (!\is_array($params['kArtikel'])) {
                $params['kArtikel'] = [$params['kArtikel']];
            }
            foreach ($params['kArtikel'] as $productID) {
                $product    = new Artikel();
                $products[] = $product->fuelleArtikel($productID, Artikel::getDefaultOptions());  
            }
        } else {
            $products = (new ProductFilter(
                Config::getDefault(),
                $this->getDB(),
                $this->getCache()
            ))
                ->initStates($params)
                ->generateSearchResults(null, true, $limit)
                ->getProducts()
                ->all();
        }

        //$smarty->assign($assignTo, $products);
        return $products;
        // if (isset($params['bReturn'])) {
        //     return $products;
        // }
    }
}
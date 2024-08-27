<?php
/**
 * @var string $scriptURL url to index.php
 */
namespace WebApp;

/**
 * intentionally lean class that comprises useful methods
 */
class Entry
{
    function __construct()
    {
        error_log('Entry controller constructed');
    }
    /**
     * @param mixed $res
     * @return void
     */
    function Unknown($res): void
    {
        error_log('no handler for path_info ' . $res);
        $this->Page('');
    }
    /**
     * @param int $pageId id of the page
     * @return void shows the page on output
     */
    function Page($pageId): void
    {
        $sc = Config::GetConfig();
        $view = new ShowView();
        if (is_int($pageId)) {
            foreach ($sc->pages as $page) {
                if ($page->PageId === $pageId) {
                    $view->ShowPage($page);
                    return;
                }
            }
        } 
        $pages=$sc->pages ?? [];
        if(count($pages)>0)
        {
            $showPage=$pages[$pageId];
            if(is_null($showPage)){
                $firstPos=\min(\array_keys($pages));
                $showPage=$pages[$firstPos];
            }
            $view->ShowPage($showPage);
            return;
        }
        $view->ShowPage(null);
    }
    /**
     * @return void
     */
    function UpgradeDb(): void
    {
        global $scriptURL;
        $db = Db\DbCtx::GetInstance();
        $db->Upgrade();
        error_log(__FILE__.':'.__LINE__. ' upgrade completed');
        header("Refresh: 10; URL=".$scriptURL);
        echo ('db upgrade completed');
    }
}

error_log(__FILE__ . ' read');

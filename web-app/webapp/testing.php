<?php
/**
 * 
 */

 namespace WebApp;

 class Testing{
    public function Test(){
        $db = Db\DbCtx::GetInstance();
        $pageId=1;
        $created='2024-08-27 19:01:00';
        $pc = $db->FindRow('Page', ['Position'=> 1 ]);
        $pc = $db->FindRow('Page', ['Created' => $created ]);
        $pc = $db->FindRow('Page', ['PageId'=> $pageId ]);
        $pc = $db->FindRow('Page', ['PageId'=> $pageId, 'Created' => $created ]);
    }
 }
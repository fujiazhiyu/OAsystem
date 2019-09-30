<?php
class myBlock extends control
{
    /**
     * Get content when block is status 
     * 
     * @param  object    $block 
     * @access public
     * @return string
     */
    public function printStatusBlock($blockID)
    {
        $block = $this->block->getByID($blockID);
        if(empty($block)) return false;

        $this->app->loadLang('client');

        $userList     = count($this->loadModel('chat')->getUserList('online'));
        $setServerBtn = html::a(helper::createLink('setting', 'xuanxuan', 'type=edit'), $this->lang->client->set, 'class="btn"');
        $xxdStartDate = zget($this->config->xxd, 'start', $this->lang->client->noData);

        $html  = '<div class="table-row statusBlock">';
        $html .= "<div class='col date'><p>{$this->lang->client->xxdStartDate}</p><h2>{$xxdStartDate}</h2></div>";
        $html .= "<div class='col'><p>{$this->lang->client->countUsers}</p><h2>{$userList}</h2></div>";
        $html .= "<div class='col server'><p>{$this->lang->client->setServer}</p><h2>{$setServerBtn}</h2></div>";
        $html .= '</div>';

        die($html);
    }
}

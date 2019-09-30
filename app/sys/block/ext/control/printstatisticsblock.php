<?php
class block extends control
{
    /**
     * Get content when block is statistics 
     * 
     * @param  object    $block 
     * @access public
     * @return string
     */
    public function printStatisticsBlock($blockID)
    {
        $block = $this->block->getByID($blockID);
        if(empty($block)) return false;

        $this->loadModel('chat');
        $this->app->loadLang('client');

        $now      = helper::now(); 
        $users    = count($this->chat->getUserList());
        $groups   = count($this->chat->getChatGroupPairs()); 
        $messages = $this->chat->getMessageNumByTimeFrame();
        $fileSize = $this->loadModel('file')->getXxcAllFileSize();

        if($fileSize == 0)
        {
            $fileSize .= '<small> KB</small>';
        }
        else if($fileSize > $this->lang->client->sizeType['G'])
        {
            $fileSize = round($fileSize / $this->lang->client->sizeType['G'], 2) . '<small> GB</small>';
        }
        else if($fileSize > $this->lang->client->sizeType['M'])
        {
            $fileSize = round($fileSize / $this->lang->client->sizeType['M'], 2) . '<small> MB</small>';
        }
        else if($fileSize > $this->lang->client->sizeType['K'])
        {
            $fileSize = round($fileSize / $this->lang->client->sizeType['K'], 2) . '<small> KB</small>';
        }

        $html  = '<div class="table-row statisticsBlock">';
        $html .= "<div class='col'><p>{$this->lang->client->totalUsers}</p><h2>{$users}</h2></div>";
        $html .= "<div class='col'><p>{$this->lang->client->totalGroups}</p><h2>{$groups}</h2></div>";
        $html .= "<div class='col'><p>{$this->lang->client->fileSize}</p><h2>{$fileSize}</h2></div>";
        $html .= '</div><div class="table-row statisticsBlock">';
        $html .= "<div class='col'><p>{$this->lang->client->message['total']}</p><h2>{$messages->total}</h2></div>";
        $html .= "<div class='col'><p>{$this->lang->client->message['day']}</p><h2>{$messages->day}</h2></div>";
        $html .= "<div class='col'><p>{$this->lang->client->message['hour']}</p><h2>{$messages->hour}</h2></div>";
        $html .= '</div></tbody></table>';

        die($html);
    }
}

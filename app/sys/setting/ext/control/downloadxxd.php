<?php
class setting extends control
{
    /**
     * Download xxd. 
     *
     * @param  string $type
     * @param  string $os
     * @access public
     * @return void
     */
    public function downloadXXD($type = '', $os = '')
    {
        if(in_array($type, array('config', 'package')))
        {
            $server = $this->loadModel('chat')->getServer();
            if(strpos($server, '127.0.0.1') !== false) die(js::alert($this->lang->chat->xxdServerError));

            $setting     = $this->config->xuanxuan;
            $setting->os = $os;
            $this->chat->downloadXXD($setting, $type);
        }
        die("Params error.");
    }
}

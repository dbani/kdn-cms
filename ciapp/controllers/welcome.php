<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Welcome extends CI_Controller {

	public function index()
    {
        $this->load->database();
        $this->load->library('kdnAuth');

        $this->load->driver('longonya');
        $this->longonya->conf->setTitle('KOTUL.NET');
        $this->longonya->conf->setOpt('skinTheme','kdn');

        $this->load->model('events');
        $listEvts = $this->events->getEventsToday();
        $listComments = array();
        foreach( $listEvts as $eventItem)
        {
            $listComments[] = $this->events->getTitleMent($eventItem['evt_group']);
        }

        $mapInput = array();
        $mapInput['userInfo'] = $this->kdnauth->getCurrentUserInfo();
        $mapInput['strTComment'] = implode(", ", $listComments);
        $mapInput['rssFeed'] = $this->readRSS("http://dbani.blog.me/rss");

        $this->longonya->conf->setOpt('skinType','');
        $this->longonya->conf->setOpt('skinTheme','');

        $strHTML = $this->longonya->loadDocPaget('frontpage/frontpage_mt', $mapInput);

        $strHTML = $this->longonya->wikiparser->compile($strHTML, 'FrontPage');

        $this->longonya->conf->setOpt('skinType','default');
        $this->longonya->conf->setOpt('skinTheme','kdn');

        print( $this->longonya->coatSkin(
            $strHTML, 'kdn', 'default', $mapInput
        ) );
    }


    protected function readRSS( $strURL )
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $strURL);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
        /*ob_start();
        $result = curl_exec($ch);
        $buffer = ob_get_contents();
        ob_end_clean();*/
        $buffer = curl_exec($ch);
        //print( $buffer );

        //$xml = simplexml_load_string($buffer);
        $xml = simplexml_load_string( $buffer , null
    , LIBXML_NOCDATA);
        return $xml;
    }
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */

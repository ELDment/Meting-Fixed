<?php
namespace Metowolf;
use DOMDocument;

class Meting
{
	const VERSION = '1.5.20';

	public $raw;
	public $data;
	public $info;
	public $error;
	public $status;

	public $server;
	public $proxy = null;
	public $format = false;
	public $header;

	public function __construct($value = 'netease')
	{
		$this->site($value);
	}

	public function site($value)
	{
		$suppose = array('netease', 'tencent', 'kugou', 'kuwo');
		$this->server = in_array($value, $suppose) ? $value : 'netease';
		$this->header = $this->curlset();

		return $this;
	}

	public function cookie($value)
	{
		$this->header['Cookie'] = $value;

		return $this;
	}

	public function format($value = true)
	{
		$this->format = $value;

		return $this;
	}

	public function proxy($value)
	{
		$this->proxy = $value;

		return $this;
	}

	protected function exec($api, $clear = false)
	{
		if (isset($api['encode'])) {
			$api = call_user_func_array(array($this, $api['encode']), array($api));
		}
		if ($api['method'] == 'GET') {
			if (isset($api['body'])) {
				$api['url'] .= '?'.http_build_query($api['body']);
				$api['body'] = null;
			}
		}

		$this->curl($api['url'], $api['body'], 0, $clear);

		if (!$this->format) {
			return $this->raw;
		}

		$this->data = $this->raw;
		if (isset($api['decode'])) {
			$this->data = call_user_func_array(array($this, $api['decode']), array($this->data));
		}
		if (isset($api['format'])) {
			$this->data = $this->clean($this->data, $api['format']);
		}
		
		return $this->data;
		
	}

	protected function curl($url, $payload = null, $headerOnly = 0, $clear = false)
	{
		$header = array_map(function ($k, $v) {
			return $k.': '.$v;
		}, array_keys($this->header), $this->header);
		
		$curl = curl_init();
		if (!is_null($payload)) {
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, is_array($payload) ? http_build_query($payload) : $payload);
		}
		curl_setopt($curl, CURLOPT_HEADER, $headerOnly);
		curl_setopt($curl, CURLOPT_TIMEOUT, 15);
		curl_setopt($curl, CURLOPT_ENCODING, 'gzip');
		curl_setopt($curl, CURLOPT_IPRESOLVE, 1);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
		if ($clear) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, array('Accept: application/json','User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36 Edg/117.0.2045.47'));
		}
		if ($this->proxy) {
			curl_setopt($curl, CURLOPT_PROXY, $this->proxy);
		}
		for ($i = 0; $i < 3; $i++) {
			$this->raw = curl_exec($curl);
			$this->info = curl_getinfo($curl);
			$this->error = curl_errno($curl);
			$this->status = $this->error ? curl_error($curl) : '';
			if (!$this->error) {
				break;
			}
		}
		curl_close($curl);

		return $this;
	}

	protected function pickup($array, $rule)
	{
		$t = explode('.', $rule);
		foreach ($t as $vo) {
			if (!isset($array[$vo])) {
				return array();
			}
			$array = $array[$vo];
		}

		return $array;
	}

	protected function clean($raw, $rule)
	{
		$rawBuffer = $raw;
		$raw = json_decode($raw, true);
		if (!empty($rule)) {
			$raw = $this->pickup($raw, $rule);
		}
		if (!isset($raw[0]) && count($raw)) {
			$raw = array($raw);
		}

		if ($rule == 'song_kuwo') {
			$result = array_map(array($this, 'format_'.$rule), json_decode($rawBuffer, true));
		}
		elseif ($rule == 'album_kuwo') {
			$result = $this->format_album_kuwo($rawBuffer);
		} else {
			$result = array_map(array($this, 'format_'.$this->server), $raw);
		}

		return json_encode($result);
	}

	//AllFixed
	public function search($keyword, $option = null)
	{
		switch ($this->server) {
			case 'netease':
				$api = array(
					'method'	=> 'POST',
					'url'		=> 'http://music.163.com/api/cloudsearch/pc',
					'body'		=> array(
						's'			=> $keyword,
						'type'		=> isset($option['type']) ? $option['type'] : 1,
						'limit'		=> isset($option['limit']) ? $option['limit'] : 30,
						'total'		=> 'true',
						'offset'	=> isset($option['page']) && isset($option['limit']) ? ($option['page'] - 1) * $option['limit'] : 0,
					),
					'encode'	=> 'netease_AESCBC',
					'format'	=> 'result.songs',
				);
			break;

			case 'tencent':
				$api = array(
					'method'	=> 'GET',
					'url'		=> 'https://c.y.qq.com/soso/fcgi-bin/client_search_cp',
					'body'		=> array(
						'format'	=> 'json',
						'p'			=> isset($option['page']) ? $option['page'] : 1,
						'n'			=> isset($option['limit']) ? $option['limit'] : 30,
						'w'			=> $keyword,
						'aggr'		=> 1,
						'lossless'	=> 1,
						'cr'		=> 1,
						'new_json'	=> 1,
					),
					'format'	=> 'data.song.list',
				);
			break;

			case 'kugou':
				$api = array(
					'method'	=> 'GET',
					'url'		=> 'http://mobilecdn.kugou.com/api/v3/search/song',
					'body'		=> array(
						'api_ver'	=> 1,
						'area_code'	=> 1,
						'correct'	=> 1,
						'pagesize'	=> isset($option['limit']) ? $option['limit'] : 30,
						'plat'		=> 2,
						'tag'		=> 1,
						'sver'		=> 5,
						'showtype'	=> 10,
						'page'		=> isset($option['page']) ? $option['page'] : 1,
						'keyword'	=> $keyword,
						'version'	=> 8990,
					),
					'format'	=> 'data.info',
				);
			break;

			case 'kuwo':
				$api = array(
					'method'	=> 'GET',
					'url'		=> 'http://www.kuwo.cn/search/searchMusicBykeyWord',
					'body'		=> array(
						'all'		=> $keyword,
						'pn'		=> isset($option['page']) ? $option['page'] : 1,
						'rn'		=> isset($option['limit']) ? $option['limit'] : 30,
						'vipver'	=> 1,
						'client'	=> 'kt',
						'ft'		=> 'music',
						'cluster'	=> 0,
						'strategy'	=> 2012,
						'encoding'	=> 'utf8',
						'rformat'	=> 'json',
						'mobi'		=> 1,
						'issubtitle'=> 1,
						'show_copyright_off'=> 1,
					),
					'format'	=> 'abslist',
				);
			break;
		}

		return $this->exec($api);
	}

	//AllFixed
	public function song($id)
	{
		switch ($this->server) {
			case 'netease':
				$api = array(
					'method'=> 'POST',
					'url'	=> 'http://music.163.com/api/v3/song/detail/',
					'body'	=> array(
						'c'		=> '[{"id":'.$id.',"v":0}]',
					),
					'encode'=> 'netease_AESCBC',
					'format'=> 'songs',
				);
			break;

			case 'tencent':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'https://c.y.qq.com/v8/fcg-bin/fcg_play_single_song.fcg',
					'body'	=> array(
						'songmid'	=> $id,
						'platform'	=> 'yqq',
						'format'	=> 'json',
					),
					'format'=> 'data',
				);
			break;

			case 'kugou':
				$api = array(
					'method'=> 'POST',
					'url'	=> 'http://m.kugou.com/app/i/getSongInfo.php',
					'body'	=> array(
						'cmd'	=> 'playInfo',
						'hash'	=> $id,
						'from'	=> 'mkugou',
					),
					'format'=> '',
				);
			break;

			case 'kuwo':
				$clear = true;
				$api = array(
					'method'=> 'GET',
					//http://datacenter.kuwo.cn/d.c?cmd=query&ft=music&force=no&resenc=utf8&cmkey=plist_pl2012&nation=1&isdownload=1&fpay=1&ids=321164775
					//'url'	=> 'http://sartist.kuwo.cn/qi.s',
					'url'	=> 'http://datacenter.kuwo.cn/d.c',
					'body'	=> array(
						'ids'	=> $id,
						'fpay'	=> 1,
						'isdownload'=> 1,
						'nation'=> 1,
						'cmkey'	=> 'plist_pl2012',
						'resenc'=> 'utf8',
						'force'	=> 'no',
						'ft'	=> 'music',
						'cmd'	=> 'query',
					),
					/*
					'body'	=> array(
						'rid'			=> $id,
						'isMultiArtists'=> 1,
						'encoding'		=> 'utf8',
					),
					*/
					'format'=> 'song_kuwo',
				);
			break;
		}
		return $this->exec($api, $clear);
	}

	//KuwoNeedsToBeImproved
	public function album($id)
	{
		switch ($this->server) {
			case 'netease':
				$api = array(
					'method'=> 'POST',
					'url'	=> 'http://music.163.com/api/v1/album/'.$id,
					'body'	=> array(
						'total'			=> 'true',
						'offset'		=> '0',
						'id'			=> $id,
						'limit'			=> '1000',
						'ext'			=> 'true',
						'private_cloud'	=> 'true',
					),
					'encode'=> 'netease_AESCBC',
					'format'=> 'songs',
				);
			break;

			case 'tencent':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'https://c.y.qq.com/v8/fcg-bin/fcg_v8_album_detail_cp.fcg',
					'body'	=> array(
						'albummid'	=> $id,
						'platform'	=> 'mac',
						'format'	=> 'json',
						'newsong'	=> 1,
					),
					'format'=> 'data.getSongInfo',
				);
			break;

			case 'kugou':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'http://mobilecdn.kugou.com/api/v3/album/song',
					'body'	=> array(
						'albumid'	=> $id,
						'area_code'	=> 1,
						'plat'		=> 2,
						'page'		=> 1,
						'pagesize'	=> -1,
						'version'	=> 8990,
					),
					'format' => 'data.info',
				);
			break;

			case 'kuwo':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'https://www.kuwo.cn/album_detail/'.$id,
					'body'	=> array(),
					'format'=> 'album_kuwo',
				);
			break;
		}
		return $this->exec($api);
	}

	//Only "netease" is available
	//Abandoned fixing other engines, due to laziness. XDDD
	public function artist($id, $limit = 50)
	{
		switch ($this->server) {
			case 'netease':
				$api = array(
					'method'=> 'POST',
					'url'	=> 'http://music.163.com/api/v1/artist/'.$id,
					'body'	=> array(
						'ext'			=> 'true',
						'private_cloud'	=> 'true',
						'ext'			=> 'true',
						'top'			=> $limit,
						'id'			=> $id,
					),
					'encode'=> 'netease_AESCBC',
					'format'=> 'hotSongs',
				);
			break;

			case 'tencent':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'http://mobilecdn.kugou.com/api/v3/singer/song',
					'body'	=> array(
						'singerid'	=> $id,
						'area_code'	=> 1,
						'page'		=> 1,
						'plat'		=> 0,
						'pagesize'	=> $limit,
						'version'	=> 8990,
					),
					'format' => 'data.info',
				);
			break;

			case 'kugou':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'http://mobilecdn.kugou.com/api/v3/singer/song',
					'body'	=> array(
						'singerid'	=> $id,
						'area_code'	=> 1,
						'page'		=> 1,
						'plat'		=> 0,
						'pagesize'	=> $limit,
						'version'	=> 8990,
					),
					'format'=> 'data.info',
				);
			break;

			case 'kuwo':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'http://www.kuwo.cn/api/www/artist/artistMusic',
					'body'	=> array(
						'artistid'		=> $id,
						'pn'			=> 1,
						'rn'			=> $limit,
						'httpsStatus'	=> 1,
					),
					'format' => 'data.list',
				);
			break;
		}

		return $this->exec($api);
	}

	//Only "netease", "tencent" are available
	//Abandoned fixing other engines, due to laziness. XDDD
	public function playlist($id)
	{
		switch ($this->server) {
			case 'netease':
				$api = array(
					'method'=> 'POST',
					'url'	=> 'http://music.163.com/api/v6/playlist/detail',
					'body'	=> array(
						's'		=> '0',
						'id'	=> $id,
						'n'		=> '1000',
						't'		=> '0',
					),
					'encode'=> 'netease_AESCBC',
					'format'=> 'playlist.tracks',
				);
			break;

			case 'tencent':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'https://c.y.qq.com/v8/fcg-bin/fcg_v8_playlist_cp.fcg',
					'body'	=> array(
						'id'		=> $id,
						'format'	=> 'json',
						'newsong'	=> 1,
						'platform'	=> 'jqspaframe.json',
					),
					'format'=> 'data.cdlist.0.songlist',
				);
			break;

			case 'kugou':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'http://mobilecdn.kugou.com/api/v3/special/song',
					'body'	=> array(
						'specialid'	=> $id,
						'area_code'	=> 1,
						'page'		=> 1,
						'plat'		=> 2,
						'pagesize'	=> -1,
						'version'	=> 8990,
					),
					'format'=> 'data.info',
				);
			break;

			case 'kuwo':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'http://www.kuwo.cn/api/www/playlist/playListInfo',
					'body'	=> array(
						'pid'			=> $id,
						'pn'			=> 1,
						'rn'			=> 1000,
						'httpsStatus'	=> 1,
					),
					'format' => 'data.musicList',
				);
			break;
		}

		return $this->exec($api);
	}

	//AllFixed
	public function url($id, $br = 320)
	{
		switch ($this->server) {
			case 'netease':
				$api = array(
					'method'=> 'POST',
					'url'	=> 'http://music.163.com/api/song/enhance/player/url',
					'body'	=> array(
						'ids'	=> array($id),
						'br'	=> $br * 1000,
					),
					'encode'=> 'netease_AESCBC',
					'decode'=> 'netease_url',
				);
			break;

			case 'tencent':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'https://c.y.qq.com/v8/fcg-bin/fcg_play_single_song.fcg',
					'body'	=> array(
						'songmid'	=> $id,
						'platform'	=> 'yqq',
						'format'	=> 'json',
					),
					'decode'=> 'tencent_url',
				);
			break;

			case 'kugou':
				$api = array(
					'method'=> 'POST',
					'url'	=> 'http://media.store.kugou.com/v1/get_res_privilege',
					'body'	=> json_encode(array(
						'relate'	=> 1,
						'userid'	=> '0',
						'vip'		=> 0,
						'appid'		=> 1000,
						'token'		=> '',
						'behavior'	=> 'download',
						'area_code'	=> '1',
						'clientver'	=> '8990',
						'resource'	=> array(array(
							'id'	=> 0,
							'type'	=> 'audio',
							'hash'	=> $id,
						)), 
					)),
					'decode'=> 'kugou_url',
				);
			break;

			case 'kuwo':
				$clear = true;
				//http://antiserver.kuwo.cn/anti.s?format=mp3&rid=388614055&response=url&type=convert_url3&br=320kmp3
				$api = array(
					'method'=> 'GET',
					//'url'	=> "http://antiserver.kuwo.cn/anti.s?format=mp3&rid=$id&type=convert_url3",
					'url'	=> 'http://antiserver.kuwo.cn/anti.s',
					'body'	=> array(
						//'mid'		=> $id,
						//'type'	=> 'music',
						//'httpsStatus'=> 1,
						'format'	=> 'mp3',
						'rid'		=> $id,
						'response'	=> 'url',
						'type'		=> 'convert_url3',
					),
					'decode'=> 'kuwo_url',
				);
			break;
		}
		$this->temp['br'] = $br;
		
		return $this->exec($api, $clear);
	}

	//NoNeedToFix
	public function lyric($id)
	{
		switch ($this->server) {
			case 'netease':
				$api = array(
					'method'=> 'POST',
					'url'	=> 'http://music.163.com/api/song/lyric',
					'body'	=> array(
						'id'	=> $id,
						'os'	=> 'linux',
						'lv'	=> -1,
						'kv'	=> -1,
						'tv'	=> -1,
					),
					'encode'=> 'netease_AESCBC',
					'decode'=> 'netease_lyric',
				);
			break;

			case 'tencent':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'https://c.y.qq.com/lyric/fcgi-bin/fcg_query_lyric_new.fcg',
					'body'	=> array(
						'songmid'	=> $id,
						'g_tk'		=> '5381',
					),
					'decode'=> 'tencent_lyric',
				);
			break;

			case 'kugou':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'http://krcs.kugou.com/search',
					'body'	=> array(
						'keyword'	=> '%20-%20',
						'ver'		=> 1,
						'hash'		=> $id,
						'client'	=> 'mobi',
						'man'		=> 'yes',
					),
					'decode'=> 'kugou_lyric',
				);
			break;

			case 'kuwo':
				$api = array(
					'method'=> 'GET',
					'url'	=> 'http://m.kuwo.cn/newh5/singles/songinfoandlrc',
					'body'	=> array(
						'musicId'		=> $id,
						'httpsStatus'	=> 1,
					),
					'decode'=> 'kuwo_lyric',
				);
			break;
		}

		return $this->exec($api);
	}

	//AllFixed
	public function pic($id, $size = 400)
	{
		switch ($this->server) {
			case 'netease':
				$format = $this->format;
				$data = $this->format(false)->song($id);
				$this->format = $format;
				$data = json_decode($data, true);
				$url = $data['songs'][0]['al']['picUrl'].'?param='.$size.'y'.$size;
				//$url = 'https://p3.music.126.net/'.$this->netease_encryptId($data['pic_id']).'/'.$data['pic_id'].'.jpg?param='.$size.'y'.$size;
			break;

			case 'tencent':
				$format = $this->format;
				$data = $this->format(false)->song($id);
				$this->format = $format;
				$data = json_decode($data, true);
				$url = 'https://y.gtimg.cn/music/photo_new/T002R'.$size.'x'.$size.'M000'.$data['data'][0]['album']['mid'].'.jpg?max_age=2592000';
			break;

			case 'kugou':
				$format = $this->format;
				$data = $this->format(false)->song($id);
				$this->format = $format;
				$data = json_decode($data, true);
				$url = $data['imgUrl'];
				$url = str_replace('{size}', $size, $url);
			break;

			case 'kuwo':
				$format = $this->format;
				$data = $this->format(false)->song($id);
				$this->format = $format;
				$data = json_decode($data, true)[0];
				if ($data['albumid'] == 0 || $data['album'] == '') {
					$url = 'https://img1.kuwo.cn/star/starheads/'.$size.'/s4s0/7/2832251303.jpg';
				} else {
					$api = array(
						'method'=> 'GET',
						'url'	=> 'http://mobilebasedata.kuwo.cn/basedata.s',
						'body'	=> array(
							'type'		=> 'get_album_info',
							'id'		=> $data['albumid'],
							'aapiver'	=> 1,
							'tmeapp'	=> 1,
							'spPrivilege'=> 0,
							'prod'		=> 'kwplayer_ip_11.1.0.0',
							'source'	=> 'kwplayer_ip_11.1.0.0_TJ.ipa',
							'corp'		=> 'kuwo',
							'plat'		=> 'ip',
							'newver'	=> 3,
							'province'	=> '',
							'city'		=> '',
							'notrace'	=> 0,
							'allpay'	=> 0,
						),
					);
					//http://mobilebasedata.kuwo.cn/basedata.s?type=get_album_info&id=16891087&aapiver=1&tmeapp=1&spPrivilege=0&prod=kwplayer_ip_11.1.0.0&source=kwplayer_ip_11.1.0.0_TJ.ipa&corp=kuwo&plat=ip&tmeapp=1&newver=3&province&city&notrace=0&allpay=0
					$res = $this->exec($api, true);
					//$url = json_decode($res, true)['big_pic'];
					$url = json_decode($res, true)['pic'];
					$url = str_replace('albumcover/300', 'albumcover/'.$size, $url);
					//$url = isset($data['data']['pic']) ? $data['data']['pic'] : $data['data']['albumpic'];
				}
			break;
		}

		return json_encode(array('url' => $url));
	}

	//NoNeedToFix
	protected function curlset()
	{
		switch ($this->server) {
			case 'netease':
				return array(
					'Referer'			=> 'https://music.163.com/',
					'Cookie'			=> 'appver=8.2.30; os=iPhone OS; osver=15.0; EVNSM=1.0.0; buildver=2206; channel=distribution; machineid=iPhone13.3',
					'User-Agent'		=> 'Mozilla/5.0 (iPhone; CPU iPhone OS 15_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Mobile/15E148 CloudMusic/0.1.1 NeteaseMusic/8.2.30',
					'X-Real-IP'			=> long2ip(mt_rand(1884815360, 1884890111)),
					'Accept'			=> '*/*',
					'Accept-Language'	=> 'zh-CN,zh;q=0.8,gl;q=0.6,zh-TW;q=0.4',
					'Connection'		=> 'keep-alive',
					'Content-Type'		=> 'application/x-www-form-urlencoded',
				);
			break;

			case 'tencent':
				return array(
					'Referer'			=> 'http://y.qq.com',
					'Cookie'			=> 'pgv_pvi=22038528; pgv_si=s3156287488; pgv_pvid=5535248600; yplayer_open=1; ts_last=y.qq.com/portal/player.html; ts_uid=4847550686; yq_index=0; qqmusic_fromtag=66; player_exist=1',
					'User-Agent'		=> 'QQ%E9%9F%B3%E4%B9%90/54409 CFNetwork/901.1 Darwin/17.6.0 (x86_64)',
					'Accept'			=> '*/*',
					'Accept-Language'	=> 'zh-CN,zh;q=0.8,gl;q=0.6,zh-TW;q=0.4',
					'Connection'		=> 'keep-alive',
					'Content-Type'		=> 'application/x-www-form-urlencoded',
				);
			break;

			case 'kugou':
				return array(
					'User-Agent'		=> 'IPhone-8990-searchSong',
					'UNI-UserAgent'		=> 'iOS11.4-Phone8990-1009-0-WiFi',
				);
			break;

			case 'kuwo':
				return array(
					'Cookie'			=> 'Hm_lvt_cdb524f42f0ce19b169a8071123a4797=1623339177,1623339183; _ga=GA1.2.1195980605.1579367081; Hm_lpvt_cdb524f42f0ce19b169a8071123a4797=1623339982; kw_token=3E7JFQ7MRPL; _gid=GA1.2.747985028.1623339179; _gat=1',
					'csrf'				=> '3E7JFQ7MRPL',
					'Host'				=> 'www.kuwo.cn',
					'Referer'			=> 'http://www.kuwo.cn/',
					'User-Agent'		=> 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36 Edg/117.0.2045.47',
					//'Accept'			=> 'application/json',
					//'Accept-Encoding'	=> 'gzip, deflate, br',
					//'Connection'		=> 'keep-alive',
					//'Content-Type'	=> 'application/json'
				);
			break;
		}
	}

	protected function getRandomHex($length)
	{
		if (function_exists('random_bytes')) {
			return bin2hex(random_bytes($length / 2));
		}
		if (function_exists('mcrypt_create_iv')) {
			return bin2hex(mcrypt_create_iv($length / 2, MCRYPT_DEV_URANDOM));
		}
		if (function_exists('openssl_random_pseudo_bytes')) {
			return bin2hex(openssl_random_pseudo_bytes($length / 2));
		}
	}

	protected function bchexdec($hex)
	{
		$dec = 0;
		$len = strlen($hex);
		for ($i = 1; $i <= $len; $i++) {
			$dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
		}
		return $dec;
	}

	protected function bcdechex($dec)
	{
		$hex = '';
		do {
			$last = bcmod($dec, 16);
			$hex = dechex($last).$hex;
			$dec = bcdiv(bcsub($dec, $last), 16);
		} while ($dec > 0);
		return $hex;
	}

	protected function str2hex($string)
	{
		$hex = '';
		for ($i = 0; $i < strlen($string); $i++) {
			$ord = ord($string[$i]);
			$hexCode = dechex($ord);
			$hex .= substr('0'.$hexCode, -2);
		}

		return $hex;
	}

	protected function netease_AESCBC($api)
	{
		$modulus = '157794750267131502212476817800345498121872783333389747424011531025366277535262539913701806290766479189477533597854989606803194253978660329941980786072432806427833685472618792592200595694346872951301770580765135349259590167490536138082469680638514416594216629258349130257685001248172188325316586707301643237607';
		$pubkey = '65537';
		$nonce = '0CoJUm6Qyw8W8jud';
		$vi = '0102030405060708';

		if (extension_loaded('bcmath')) {
			$skey = $this->getRandomHex(16);
		} else {
			$skey = 'B3v3kH4vRPWRJFfH';
		}

		$body = json_encode($api['body']);

		if (function_exists('openssl_encrypt')) {
			$body = openssl_encrypt($body, 'aes-128-cbc', $nonce, false, $vi);
			$body = openssl_encrypt($body, 'aes-128-cbc', $skey, false, $vi);
		} else {
			$pad = 16 - (strlen($body) % 16);
			$body = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $nonce, $body.str_repeat(chr($pad), $pad), MCRYPT_MODE_CBC, $vi));
			$pad = 16 - (strlen($body) % 16);
			$body = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $skey, $body.str_repeat(chr($pad), $pad), MCRYPT_MODE_CBC, $vi));
		}

		if (extension_loaded('bcmath')) {
			$skey = strrev(utf8_encode($skey));
			$skey = $this->bchexdec($this->str2hex($skey));
			$skey = bcpowmod($skey, $pubkey, $modulus);
			$skey = $this->bcdechex($skey);
			$skey = str_pad($skey, 256, '0', STR_PAD_LEFT);
		} else {
			$skey = '85302b818aea19b68db899c25dac229412d9bba9b3fcfe4f714dc016bc1686fc446a08844b1f8327fd9cb623cc189be00c5a365ac835e93d4858ee66f43fdc59e32aaed3ef24f0675d70172ef688d376a4807228c55583fe5bac647d10ecef15220feef61477c28cae8406f6f9896ed329d6db9f88757e31848a6c2ce2f94308';
		}

		$api['url'] = str_replace('/api/', '/weapi/', $api['url']);
		$api['body'] = array(
			'params'	=> $body,
			'encSecKey'	=> $skey,
		);

		return $api;
	}

	protected function netease_encryptId($id)
	{
		$magic = str_split('3go8&$8*3*3h0k(2)2');
		$song_id = str_split($id);
		for ($i = 0; $i < count($song_id); $i++) {
			$song_id[$i] = chr(ord($song_id[$i]) ^ ord($magic[$i % count($magic)]));
		}
		$result = base64_encode(md5(implode('', $song_id), 1));
		$result = str_replace(array('/', '+'), array('_', '-'), $result);

		return $result;
	}

	protected function netease_url($result)
	{
		$data = json_decode($result, true);
		if (isset($data['data'][0]['uf']['url'])) {
			$data['data'][0]['url'] = $data['data'][0]['uf']['url'];
		}
		if (isset($data['data'][0]['url'])) {
			$url = array(
				'url'	=> $data['data'][0]['url'],
				'size'	=> $data['data'][0]['size'],
				'br'	=> $data['data'][0]['br'] / 1000,
			);
		} else {
			$url = array(
				'url'	=> '',
				'size'	=> 0,
				'br'	=> -1,
			);
		}

		return json_encode($url);
	}

	protected function tencent_url($result)
	{
		$data = json_decode($result, true);
		$guid = mt_rand() % 10000000000;

		$type = array(
			array('size_flac',	999,'F000',	'flac'),
			array('size_320mp3',320,'M800',	'mp3'),
			array('size_192aac',192,'C600',	'm4a'),
			array('size_128mp3',128,'M500',	'mp3'),
			array('size_96aac',	96,	'C400',	'm4a'),
			array('size_48aac',	48,	'C200',	'm4a'),
			array('size_24aac',	24,	'C100',	'm4a'),
		);

		$uin = '0';
		preg_match('/uin=(\d+)/', $this->header['Cookie'], $uin_match);
		if (count($uin_match)) {
			$uin = $uin_match[1];
		}

		$payload = array(
			'req_0'	=> array(
				'module'=> 'vkey.GetVkeyServer',
				'method'=> 'CgiGetVkey',
				'param'	=> array(
					'guid'		=> (string) $guid,
					'songmid'	=> array(),
					'filename'	=> array(),
					'songtype'	=> array(),
					'uin'		=> $uin,
					'loginflag'	=> 1,
					'platform'	=> '20',
				),
			),
		);

		foreach ($type as $vo) {
			$payload['req_0']['param']['songmid'][]	= $data['data'][0]['mid'];
			$payload['req_0']['param']['filename'][]= $vo[2].$data['data'][0]['file']['media_mid'].'.'.$vo[3];
			$payload['req_0']['param']['songtype'][]= $data['data'][0]['type'];
		}

		$api = array(
			'method'=> 'GET',
			'url'	=> 'https://u.y.qq.com/cgi-bin/musicu.fcg',
			'body'	=> array(
				'format'		=> 'json',
				'platform'		=> 'yqq.json',
				'needNewCode'	=> 0,
				'data'			=> json_encode($payload),
			),
		);
		$response = json_decode($this->exec($api), true);
		$vkeys = $response['req_0']['data']['midurlinfo'];

		foreach ($type as $index => $vo) {
			if ($data['data'][0]['file'][$vo[0]] && $vo[1] <= $this->temp['br']) {
				if (!empty($vkeys[$index]['vkey'])) {
					$url = array(
						'url'	=> $response['req_0']['data']['sip'][0].$vkeys[$index]['purl'],
						'size'	=> $data['data'][0]['file'][$vo[0]],
						'br'	=> $vo[1],
					);
					break;
				}
			}
		}
		if (!isset($url['url'])) {
			$url = array(
				'url'	=> '',
				'size'	=> 0,
				'br'	=> -1,
			);
		}

		return json_encode($url);
	}

	protected function kugou_url($result)
	{
		$data = json_decode($result, true);
		$max = 0;
		$url = array();
		foreach ($data['data'][0]['relate_goods'] as $vo) {
			if ($vo['info']['bitrate'] <= $this->temp['br'] && $vo['info']['bitrate'] > $max) {
				$api = array(
					'method'=> 'GET',
					//http://trackercdn.kugou.com/i/v2/?cmd=25&key=49f701e8b36115d84c13d5d7decf2603&hash=d9887aecacd56ddc63786ea0028015b4&behavior=download&pid=3
					'url'	=> 'http://trackercdn.kugou.com/i/v2/',
					'body'	=> array(
						//'id'		=> $vo['id'],
						//'album_id'=> $vo['album_id'],
						//'album_audio_id'=> $vo['album_audio_id'],
						'hash'		=> $vo['hash'],
						'key'		=> md5($vo['hash'].'kgcloudv2'),
						'pid'		=> 3,
						//'behavior'=> 'play',
						'behavior'	=> 'download',
						'cmd'		=> '25',
						//'version'	=> 8990,
					),
				);
				$t = json_decode($this->exec($api), true);
				if (isset($t['url'])) {
					$max = $t['bitRate'] / 1000;
					$url = array(
						'url'	=> reset($t['url']),
						'size'	=> $t['fileSize'],
						'br'	=> $t['bitRate'] / 1000,
					);
				}
			}
		}
		if (!isset($url['url'])) {
			$url = array(
				'url'	=> '',
				'size'	=> 0,
				'br'	=> -1,
			);
		}

		return json_encode($url);
	}

	protected function kuwo_url($result)
	{
		$result = json_decode($result, true);
		$url = array();
		if ($result['code'] == 200 && $result['msg'] == 'success' && isset($result['url'])) {
			$url = array(
				'url'	=> $result['url'],
				'br'	=> 128,
			);
		} else {
			$url = array(
				'url'	=> '',
				'br'	=> -1,
			);
		}

		return json_encode($url);
	}

	protected function netease_lyric($result)
	{
		$result = json_decode($result, true);
		$data = array(
			'lyric'	=> isset($result['lrc']['lyric']) ? $result['lrc']['lyric'] : '',
			'tlyric'=> isset($result['tlyric']['lyric']) ? $result['tlyric']['lyric'] : '',
		);

		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	protected function tencent_lyric($result)
	{
		$result = substr($result, 18, -1);
		$result = json_decode($result, true);
		$data = array(
			'lyric'	=> isset($result['lyric']) ? base64_decode($result['lyric']) : '',
			'tlyric'=> isset($result['trans']) ? base64_decode($result['trans']) : '',
		);

		return json_encode($data, JSON_UNESCAPED_UNICODE);
	}

	protected function kugou_lyric($result)
	{
		$result = json_decode($result, true);
		$api = array(
			'method'=> 'GET',
			'url'	=> 'http://lyrics.kugou.com/download',
			'body'	=> array(
				'charset'	=> 'utf8',
				'accesskey'	=> $result['candidates'][0]['accesskey'],
				'id'		=> $result['candidates'][0]['id'],
				'client'	=> 'mobi',
				'fmt'		=> 'lrc',
				'ver'		=> 1,
			),
		);
		$data = json_decode($this->exec($api), true);
		$arr = array(
			'lyric'	=> base64_decode($data['content']),
			'tlyric'=> '',
		);

		return json_encode($arr, JSON_UNESCAPED_UNICODE);
	}

	protected function kuwo_lyric($result)
	{
		$result = json_decode($result, true);
		if (count($result['data']['lrclist'])) {
			$kuwolrc = '';
			for ($i = 0; $i < count($result['data']['lrclist']); $i++) {
				$otime = $result['data']['lrclist'][$i]['time'];
				$osec = explode('.', $otime)[0];
				$min = str_pad(floor($osec / 60), 2, "0", STR_PAD_LEFT);
				$sec = str_pad($osec - $min * 60, 2, "0", STR_PAD_LEFT);
				$msec = explode('.', $otime)[1];
				$olyric = $result['data']['lrclist'][$i]['lineLyric'];
				$kuwolrc = $kuwolrc.'['.$min.':'.$sec.'.'.$msec.']'.$olyric."\n";
			}
			$arr = array(
				'lyric'	=> $kuwolrc,
				'tlyric'=> '',
			);
		} else {
			$arr = array(
				'lyric'	=> '',
				'tlyric'=> '',
			);
		}
		return json_encode($arr, JSON_UNESCAPED_UNICODE);
	}

	protected function format_netease($data)
	{
		$result = array(
			'id'		=> $data['id'],
			'name'		=> $data['name'],
			'artist'	=> array(),
			'album'		=> $data['al']['name'],
			'pic_id'	=> isset($data['al']['pic_str']) ? $data['al']['pic_str'] : $data['al']['pic'],
			'url_id'	=> $data['id'],
			'lyric_id'	=> $data['id'],
			'source'	=> 'netease',
		);
		if (isset($data['al']['picUrl'])) {
			preg_match('/\/(\d+)\./', $data['al']['picUrl'], $match);
			$result['pic_id'] = $match[1];
		}
		foreach ($data['ar'] as $vo) {
			$result['artist'][] = $vo['name'];
		}

		return $result;
	}

	protected function format_tencent($data)
	{
		if (isset($data['musicData'])) {
			$data = $data['musicData'];
		}
		$result = array(
			'id'		=> $data['mid'],
			'name'		=> $data['name'],
			'artist'	=> array(),
			'album'		=> trim($data['album']['title']),
			'pic_id'	=> $data['album']['mid'],
			'url_id'	=> $data['mid'],
			'lyric_id'	=> $data['mid'],
			'source'	=> 'tencent',
		);
		foreach ($data['singer'] as $vo) {
			$result['artist'][] = $vo['name'];
		}

		return $result;
	}

	protected function format_kugou($data)
	{
		$result = array(
			'id'		=> $data['hash'],
			'name'		=> isset($data['filename']) ? $data['filename'] : $data['fileName'],
			'artist'	=> array(),
			'album'		=> isset($data['album_name']) ? $data['album_name'] : '',
			'url_id'	=> $data['hash'],
			'pic_id'	=> $data['hash'],
			'lyric_id'	=> $data['hash'],
			'source'	=> 'kugou',
		);
		list($result['artist'], $result['name']) = explode(' - ', $result['name'], 2);
		$result['artist'] = explode('、', $result['artist']);

		return $result;
	}

	protected function format_kuwo($data)
	{
		$result = array(
			'id'		=> str_replace("MUSIC_", "", $data['MUSICRID']),
			'name'		=> $data['NAME'],
			'artist'	=> explode('&', $data['ARTIST']),
			'album'		=> $data['ALBUM'],
			'pic_id'	=> str_replace("MUSIC_", "", $data['MUSICRID']),
			'url_id'	=> str_replace("MUSIC_", "", $data['MUSICRID']),
			'lyric_id'	=> str_replace("MUSIC_", "", $data['MUSICRID']),
			'source'	=> 'kuwo',
		);

		return $result;
	}

	protected function format_song_kuwo($data)
	{
		//$data = $data[0];
		$result = array(
			'id'		=> $data['id'],
			'name'		=> $data['name'],
			'artist'	=> explode('&', $data['artist']),
			'album'		=> $data['album'],
			'pic_id'	=> $data['id'],
			'url_id'	=> $data['id'],
			'lyric_id'	=> $data['id'],
			'source'	=> 'kuwo',
		);

		return $result;
	}

	protected function format_album_kuwo($data)
	{
		$dom = new DOMDocument();
		libxml_use_internal_errors(true);
		$dom->loadHTML($data);
		libxml_clear_errors();
		$albumList = $dom->getElementsByTagName('ul');
		foreach ($albumList as $ul) {
			if ($ul->getAttribute('class') != 'album_list') {
				continue;
			}
			$liList = $ul->getElementsByTagName('li');
			foreach ($liList as $li) {
				if ($li->getAttribute('class') != 'song_item flex_c') {
					continue;
				}
					
				$divList = $li->getElementsByTagName('div');
				//$dom->loadHTML($div);
				//$xpath = new DOMXPath($div);
				foreach ($divList as $div) {
					if ($div->getAttribute('class') == 'song_name flex_c') {
						$song_name = $div;
						continue;
					}
					if ($div->getAttribute('class') == 'song_artist') {
						$song_artist = $div;
						continue;
					}
					if ($div->getAttribute('class') == 'song_album') {
						$song_album = $div;
						continue;
					}
				}
				
				if ($song_name->length <= 0 || $song_artist->length <= 0 || $song_album->length <= 0) {
					//continue;
				}
				
				$name = $song_name->getElementsByTagName('a')->item(0)->getAttribute('title');
				$id = $song_name->getElementsByTagName('a')->item(0)->getAttribute('href');
				$id = str_replace('/play_detail/', '', $id);
				$artist = $song_artist->getElementsByTagName('span')->item(0)->getAttribute('title');
				$album = $song_album->getElementsByTagName('span')->item(0)->getAttribute('title');
				$data = array(
					'id'		=> $id,
					'name'		=> $name,
					'artist'	=> explode('&', $artist),
					'album'		=> $album,
					'url_id'	=> $id,
					'pic_id'	=> $id,
					'lyric_id'	=> $id,
					'source'	=> 'kuwo',
				);
				$songList[] = json_decode(json_encode($data, JSON_UNESCAPED_UNICODE));
			}
		}
		return $songList;
	}	
}

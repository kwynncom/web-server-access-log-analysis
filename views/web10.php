<?php

require_once(__DIR__ . '/../load/dao_wsal.php');
require_once(__DIR__ . '/agentView.php');
require_once(__DIR__ . '/../bots/bots.php');
require_once(__DIR__ . '/../myips/i10.php');

class wsal_web_view_10 extends dao_wsal {
	
	const defaultSBack = 110000;
	
	public function __construct() {
		parent::__construct();
		$this->db10();
		$this->do10();

	}
	
	private function db10() {
		$res = $this->lcoll->createIndex(['ts'  => 1]);
		$res = $this->lcoll->createIndex(['ts' => -1]);
		$res = $this->lcoll->createIndex(['n'  =>  1]);
		return;
	}
	
	private function getUq($a, $k) {
		if (!isset($this->ouq[$k][$a]))
				   $this->ouq[$k][$a] = true;
		else return $k . '-' . $this->ouq[$k][$a];
		
		$n = count($this->ouq[$k]);
		$this->ouq[$k][$a] = $n;
		return $k . '-' . $n;
	}
	
	private function combo($a) {
		static $k10  = '/t/9/12/sync/';
		$k10l = false;
		
		if (!$k10l) $k10l = strlen($k10);
		
		
		$u = $a['url'];

		if (substr($u, 0, $k10l) === $k10) $u = '/sync/';
				
		$k = $a['ip'] . $a['agent'] . $u;
		if (!isset($this->ouqc[$k])) {
				   $this->ouqc[$k] = true;
				   return FALSE;
		}
		return true;
		
	}
	
	private function do10() {
		
		static $bots = [];
		
		$mya = myips::get();
			
		$res = $this->lcoll->find(['ts' => ['$gte' => strtotime('2022-01-03 18:00')]], ['sort' => ['n' => 1]]);
		$h = '';
		foreach($res as $a) {
			
			if ($a['httpCode'] >= 400) continue;
			$ag = $a['agent'];		
			
			if (isset($bots[$ag])) continue;
			if (wsal_bots::isBot($ag)) {
				$bots[$ag] = true;
				continue;
			}
			
			if (agent_view_10::isBot($ag, $a['url'])) continue;
			
			if (isset($mya[$a['ip']][$ag])) continue;
			
			$url = $a['url'];
			if ($url === '/') continue;
			if (substr($url, 0, 2) === '/?') continue;
			
			if (strpos($url, 'qjshu2021-1')) continue;
			
			if (preg_match('/html5_valid\.jpg$/', $url)) continue;
			if ($url === '/robots.txt') continue;
			if ($url === '/favicon.ico') continue;
			
			if ($this->combo($a)) continue;
			
			$af10 = $this->getUq($ag, 'ag');
			$ipd  = $this->getUq($a['ip'], 'ip');
			
			$af = $af10;
			// $af = agent_view_10::filter($ag, $a['url']);
			
			$h .= '<tr>';
			// $h .= '<td>' . $a['n'] . '</td>';
			$h .= '<td class="hud">' . date('m/d H:i:s', $a['ts']) . '</td>';
			$h .= '<td>' . $ipd . '</td>';			
			$h .= '<td>' . $af . '</td>';	
			$h .= '<td>' . $a['url'] . '</td>';
			$h .= '</tr>' . "\n";
			
		}

		echo($h);
	}
}

if (didCLICallMe(__FILE__)) {
	new wsal_view();
}

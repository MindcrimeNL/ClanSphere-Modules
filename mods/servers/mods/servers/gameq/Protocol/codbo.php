<?php
/**
 * This file is part of GameQ.
 *
 * GameQ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GameQ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * $Id$  
 */


require_once GAMEQ_BASE . 'Protocol.php';


/**
 * Quake3 Protocol
 *
 * @author         Remy Wetzels   <mindcrime@gab-clan.org>
 * @version        $Revision: 1.0 $
 */
class GameQ_Protocol_codbo extends GameQ_Protocol
{
	private $rcon = 'RCONPASS';

	public function setRcon($pass)
	{
		$this->rcon = $pass;
	} // function setRcon

	public function status()
	{
		$data = $this->p->getData();
		if ($this->p->read(11) == "print\x0Amap: ")
		{
			/* first we get the map */
			$this->r->add('map', $this->p->readString("\x0A"));
			/* then we get the list headers: num, score, ping, guid, name, team, lastmsg, address, qport, rate */
			$header = $this->p->readString("\x0A");
			
			//echo 'header: '.$header.'<br>';
			/* header separator */
			$this->p->readString("\x0A");
			/* now we should get players */
			$pattern = "/^\s*(\d+)\s+(\d+)\s+(\d+)\s+(\w+)\s+(.+)\^7\s+(\d+)\s+(\d+)\s+([0-9-.:]+)\s+([0-9-]+)\s+(\d+)(.*)$/";
			$line = $this->p->readString("\x0A");
			$numplayers = 0;
			while (!empty($line))
			{
				// echo 'player: '.$line.'<br>';
      	if (preg_match($pattern, $line, $match))
				{
					if (intval($match[1]) > 0)
					{
						$this->r->addPlayer('score', intval($match[2]));
						$this->r->addPlayer('ping', intval($match[3]));
						$this->r->addPlayer('name', $match[5]);
						$teamid = intval($match[6]);
						switch ($teamid)
						{
						default: $team = 'Spec'; break;
						case 1: $team = 'Speznaz'; break; // Tropas
						case 2: $team = 'BlackOps'; break; // OP 40
						}
						$this->r->addPlayer('team', $team);
						$this->r->addPlayer('rate', intval($match[10]));
						$numplayers++;
					}
				}
				$line = $this->p->readString("\x0A");
			}
			$this->r->add('numplayers', $numplayers);
		}
		else
			throw new GameQ_ParsingException($this->p);

	} // function status

	public function serverinfo()
	{
		$data = $this->p->getData();
		$info = array();
		if ($this->p->read(28) == "print\x0AServer info settings:\x0A")
		{
			$pattern = "/^([a-zA-Z_.]+)(.*)$/";
			$line = $this->p->readString("\x0A");
			while (!empty($line))
      {
      	if (preg_match($pattern, $line, $match))
      	{
      		$info[$match[1]] = trim($match[2]);
      	}
				$line = $this->p->readString("\x0A");
      }

			foreach ($info as $key => $value)
			{
				$this->r->add($key, $value);
				switch ($key)
				{
				default: break;
				case 'g_gametype': $this->r->add('gametype', $value); break;
				case 'sv_hostname': $this->r->add('servername', $value); break;
				case 'sv_maxclients': $this->r->add('maxplayers', $value); break;
				}
			}
			$this->r->add('gamename', 'Call of Duty: Black Ops ('.$info['g_gametype'].')'); 
			// echo '<pre>';
			// var_dump($info);
			// echo '</pre>';
		}
		else
			throw new GameQ_ParsingException($this->p);
	} // functions serverinfo

	public function preprocess($packets)
	{
		$cmd = '';
		foreach ($packets as $packet)
		{
			$rcmd = '';
			if (strlen($packet) > 5)
			{
				if (substr($packet, 0, 5) == "\xFF\xFF\xFF\xFF\x01")
					$packet = substr($packet, 5);
			}
			$len = strlen($packet);
			for ($i = 0; $i < $len; $i++)
			{
				$c = substr($packet, $i, 1);
				if (ord($c) == 0)
				{
					if ($i < $len-3)
					{
						$np = substr($packet, $i+1, 3);
						if ($np == "nt\n")
						{
							$i += 3;
							continue;
						}
					}
					break;
				}
				$rcmd .= $c;
			}
			$cmd .= $rcmd;
		}

		return $cmd;
	} // function preprocess

	public function modifyPacket($packet_conf)
	{
		// Add rcon password to query strings
		$packet_conf['data'] = sprintf($packet_conf['data'], $this->rcon);
		return $packet_conf;
	} // function modifyPacket
}
?>

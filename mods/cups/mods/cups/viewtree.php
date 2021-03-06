<?php
// ClanSphere 2010 - www.clansphere.net
// $Id$

/* Cup tree html generation code:
 * 
 * Copyright (c) 2010, Wetzels Holding BV, Remy Wetzels <mindcrime@gab-clan.org>
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *
 * - Redistributions of source code must retain the above copyright notice,
 *   this list of conditions and the following disclaimer.
 * - Redistributions in binary form must reproduce the above copyright notice,
 *   this list of conditions and the following disclaimer in the documentation
 *   and/or other materials provided with the distribution.
 * - Neither the name of Wetzels Holding BV nor the names of its
 *   contributors may be used to endorse or promote products derived from this
 *   software without specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 *
 * Special notice by Remy Wetzels <mindcrime@gab-clan.org>, September 14, 2010:
 * Permission is hereby granted by Wetzels Holding BV to the ClanSphere Project
 * to omit the above disclaimer in their general documentation and/or
 * ClanSphere about section of the code.
 */

  
$cs_lang = cs_translate('cups');
$cs_option = cs_sql_option(__FILE__, 'cups');
$datahtml = array();
$datahtml['tree'] = array();

include_once 'mods/cups/functions.php';

$cups_id = (int) $_GET['id'];
/* tree.php might set the variable below */
$gridonly = (isset($gridonly) && $gridonly == true) || !empty($_GET['gridonly']) ? true : false;

$cup = cs_sql_select(__FILE__, 'cups', '*', 'cups_id = ' . $cups_id);
$rounds = cs_cups_log($cup['cups_teams']);
$rounds_1 = $rounds;

if ($account['access_cups'] < $cup['cups_access'] || $cup['cups_access'] == 0)
{
	echo $cs_lang['access_denied'];
	return;
}

if (defined('IN_TREE'))
	$tree = true;
else
	$tree = false;
	
$width = empty($cs_option['width']) ? (empty($_GET['width']) ? 600 : (int) $_GET['width']) : $cs_option['width'];
$key = 'lang='.$account['users_lang'].'&cup='.$cups_id.'&lb=0&gridonly='.($gridonly ? 1 : 0).'&tree='.($tree ? 1 : 0).'&width='.$width.'&access='.$account['access_cups'];
if (function_exists('cs_datacache_load'))
	$cachedata = cs_datacache_load('cups', 'viewtree', $key, false);
else
	$cachedata = false;
if ($cachedata !== false)
{
	echo $cachedata;
	return;
}

$tables = 'cupmatches cm LEFT JOIN ';
$tables .= $cup['cups_system'] == CS_CUPS_TYPE_USERS ? '{pre}_users u1 ON u1.users_id = cm.squad1_id LEFT JOIN {pre}_users u2 ON u2.users_id = cm.squad2_id' :
  '{pre}_squads sq1 ON sq1.squads_id = cm.squad1_id LEFT JOIN {pre}_squads sq2 ON sq2.squads_id = cm.squad2_id';
$tables .= ' LEFT JOIN {pre}_cupsquads cs1 ON cm.squad1_id = cs1.squads_id AND cs1.cups_id = cm.cups_id LEFT JOIN {pre}_cupsquads cs2 ON cm.squad2_id = cs2.squads_id AND cs2.cups_id = cm.cups_id';
$cells = $cup['cups_system'] == CS_CUPS_TYPE_USERS
  ? 'u1.users_nick AS team1_name, cm.squad1_id AS team1_id, u2.users_nick AS team2_name, cm.squad2_id AS team2_id'
  : 'sq1.squads_name AS team1_name, cm.squad1_id AS team1_id, sq2.squads_name AS team2_name, cm.squad2_id AS team2_id';
$cells .= ', cm.cupmatches_winner AS cupmatches_winner, cm.cupmatches_accepted1 AS cupmatches_accepted1';
$cells .= ', cm.cupmatches_accepted2 AS cupmatches_accepted2, cm.cupmatches_tree_order AS cupmatches_tree_order';
$cells .= ', cs1.cupsquads_seed AS seed1, cs1.cupsquads_autoseed AS autoseed1';
$cells .= ', cs2.cupsquads_seed AS seed2, cs2.cupsquads_autoseed AS autoseed2';
$cells .= ', cm.cupmatches_seed1 AS cupmatches_seed1, cm.cupmatches_seed2 AS cupmatches_seed2';
$cells .= ', cm.cupmatches_score1 AS cupmatches_score1, cm.cupmatches_score2 AS cupmatches_score2';
$cells .= ', cm.cupmatches_match AS cupmatches_match, cm.cupmatches_nextmatch AS cupmatches_nextmatch';
$cells .= ', cm.cupmatches_id AS cupmatches_id';

$where = 'cm.cups_id = ' . $cups_id . ' AND cm.cupmatches_loserbracket = 0 AND cm.cupmatches_round = ';

$cupmatches = array();
for ($i=1; $i <= $rounds; $i++) {
  $temp = cs_sql_select(__FILE__, $tables, $cells, $where . $i, 'cm.cupmatches_tree_order ASC',0,0);
	$run = 0;
	if (count($temp))
  foreach ($temp as $match)
  {
  	$match['team1_name'] = cs_cups_team($cs_lang, $cup['cups_system'], $match['team1_id'], $match['team1_name'], $match['autoseed1'], $match['seed1'], true, true);
  	$match['team2_name'] = cs_cups_team($cs_lang, $cup['cups_system'], $match['team2_id'], $match['team2_name'], $match['autoseed2'], $match['seed2'], true, true);
    $cupmatches[$i][$run++] = $match;
  }
}

if (count($cupmatches) == 0)
{
	echo $cs_lang['no_matches'];
	return;
}

// Calc-Defs
$count_matches = $cup['cups_teams'];
$lb = '';
include 'mods/cups/viewtree_head.php';


$round = 0;
$run = 0;
$match_run = 0;

$prevmatches = array();
/* the matrix (column,row) */
$matrix = array();
$maxrows = 0;
for ($i = 1; $i <= $rounds; $i++)
{
	/* the column in the matrix */
	$column = ($c * $i) - ($c-1);
	/* init matrix */
	for ($j = $column; $j < $column + $c; $j++)
	{
		$matrix[$j] = array();
	}

	$count = 0;
	if (isset($cupmatches[$i]) && count($cupmatches[$i]))
	foreach($cupmatches[$i] as $match)
	{
		if ($i == 1)
		{
			/* calculate height */
			$currow = $count*4 + 1;
		}
		else
		{
			/* calculate height */
			$currow = floor(($prevmatches[$match['cupmatches_match']][0][1] + $prevmatches[$match['cupmatches_match']][0][2]) / 2);

			/* draw from first prev match */
			$matrix[$column-1][$prevmatches[$match['cupmatches_match']][0][1]] = array('class' => 'cup-grid-angle-down');
			/* draw from second prev match */
			$matrix[$column-1][$prevmatches[$match['cupmatches_match']][0][2]] = array('class' => 'cup-grid-angle-up');
			/* draw vertical line between */
			for ($j = $prevmatches[$match['cupmatches_match']][0][1] + 1; $j < $prevmatches[$match['cupmatches_match']][0][2]; $j++)
			{
				if ($j == $currow)
					/* draw to this one */
					$matrix[$column-1][$j] = array('class' => 'cup-grid-vertical-split-right');
				else
					/* draw vertical line */
					$matrix[$column-1][$j] = array('class' => 'cup-grid-vertical');
			}
		}
		/* keep track of this height */
		$prevrow = $currow;
		$matrix[$column][$currow] = array('class' => 'cup-grid-team');

		$string = '';
		if (!empty($match['team1_name']))
			$string = $match['team1_name'];
//		else
//			$string = '#'.$match['cupmatches_seed1'];
		if ($cs_option['scores'] == 1)
			$matrix[$column+1][$currow] = array('class' => 'cup-grid-score');
		if (!empty($string))
		{
			$matrix[$column][$currow]['content'] = $string; // TODO: extend with more info
			if ($match['cupmatches_winner'] != CS_CUPS_TEAM_UNKNOWN)
			{
				if ($cs_option['scores'] == 1)
					$matrix[$column+1][$currow]['content'] = cs_html_link(cs_url('cups','match','id='.$match['cupmatches_id']),$match['cupmatches_score1']);
        if ($match['cupmatches_winner'] == $match['team1_id'])
        {
          $matrix[$column][$currow]['class'] .= '-win';
					if ($cs_option['scores'] == 1)
  	        $matrix[$column+1][$currow]['class'] .= '-win';
        }
        else
        {
          $matrix[$column][$currow]['class'] .= '-loss';
					if ($cs_option['scores'] == 1)
	          $matrix[$column+1][$currow]['class'] .= '-loss';
        }
			}
			else
				$matrix[$column+1][$currow]['content'] = cs_html_link(cs_url('cups','match','id='.$match['cupmatches_id']), 'x');
		}

		if ($i == 1)
		{
			/* calculate height */
			$currow += 2;
		}
		else
		{
			/* draw new position */
			$currow = floor(($prevmatches[$match['cupmatches_match']][1][1] + $prevmatches[$match['cupmatches_match']][1][2]) / 2);

			/* draw from first prev match */
			$matrix[$column-1][$prevmatches[$match['cupmatches_match']][1][1]] = array('class' => 'cup-grid-angle-down');
			/* draw from second prev match */
			$matrix[$column-1][$prevmatches[$match['cupmatches_match']][1][2]] = array('class' => 'cup-grid-angle-up');
			/* draw vertical line between */
			for ($j = $prevmatches[$match['cupmatches_match']][1][1] + 1; $j < $prevmatches[$match['cupmatches_match']][1][2]; $j++)
			{
				if ($j == $currow)
					/* draw to this one */
					$matrix[$column-1][$j] = array('class' => 'cup-grid-vertical-split-right');
				else
					/* draw vertical line */
					$matrix[$column-1][$j] = array('class' => 'cup-grid-vertical');
			}
		}
		$matrix[$column][$currow] = array('class' => 'cup-grid-team');

    if (!isset($prevmatches[$match['cupmatches_nextmatch']]))
    {
      $prevmatches[$match['cupmatches_nextmatch']] = array();
			$prevmatches[$match['cupmatches_nextmatch']][0] = array($match['cupmatches_match'],
																															$prevrow,
																															$currow);
    }
    else if ($prevmatches[$match['cupmatches_nextmatch']][0][1] > $currow)
    {
      $prevmatches[$match['cupmatches_nextmatch']][1] = $prevmatches[$match['cupmatches_nextmatch']][0];
      $prevmatches[$match['cupmatches_nextmatch']][0] = array($match['cupmatches_match'],
																																 $prevrow,
																																 $currow);
    }
    else
      $prevmatches[$match['cupmatches_nextmatch']][1] = array($match['cupmatches_match'], 
																															 $prevrow,
																															 $currow);

		$string = '';
		if (!empty($match['team2_name']))
			$string = $match['team2_name'];
//		else
//			$string = '#'.$match['cupmatches_seed1'];
		if ($cs_option['scores'] == 1)
			$matrix[$column+1][$currow] = array('class' => 'cup-grid-score');
		if (!empty($string))
		{
			$matrix[$column][$currow]['content'] = $string; // TODO: extend with more info
			if ($match['cupmatches_winner'] != CS_CUPS_TEAM_UNKNOWN)
			{
				if ($cs_option['scores'] == 1)
					$matrix[$column+1][$currow]['content'] = cs_html_link(cs_url('cups','match','id='.$match['cupmatches_id']),$match['cupmatches_score2']);
        if ($match['cupmatches_winner'] == $match['team2_id'] && $match['team2_id'] != $match['team1_id'])
        {
          $matrix[$column][$currow]['class'] .= '-win';
					if ($cs_option['scores'] == 1)
	          $matrix[$column+1][$currow]['class'] .= '-win';
        }
        else
        {
          $matrix[$column][$currow]['class'] .= '-loss';
					if ($cs_option['scores'] == 1)
	          $matrix[$column+1][$currow]['class'] .= '-loss';
        }
			}
			else
				$matrix[$column+1][$currow]['content'] = cs_html_link(cs_url('cups','match','id='.$match['cupmatches_id']), 'x');
		}		

		if ($prevrow > $maxrows)
			$maxrows = $prevrow;
		if ($currow > $maxrows)
			$maxrows = $currow;
		$count++;
	}
}

// TODO
$finalmatchnr = -1;
if ($cup['cups_brackets'] == CS_CUPS_SYSTEM_LB)
	$finalmatchnr = 0;
if (isset($prevmatches[$finalmatchnr]))
{
	$column = ($c * ($rounds+1)) - ($c-1);

	/* draw winner position */
	$final = $cupmatches[$rounds][0];

	/* calculate height */
 	$currow = floor(($prevmatches[$finalmatchnr][0][1] + $prevmatches[$finalmatchnr][0][2]) / 2);

	/* draw from first prev match */
	$matrix[$column-1][$prevmatches[$finalmatchnr][0][1]] = array('class' => 'cup-grid-angle-down');
	/* draw from second prev match */
	$matrix[$column-1][$prevmatches[$finalmatchnr][0][2]] = array('class' => 'cup-grid-angle-up');
	/* draw vertical line between */
	for ($j = $prevmatches[$finalmatchnr][0][1] + 1; $j < $prevmatches[$finalmatchnr][0][2]; $j++)
	{
		if ($j == $currow)
			/* draw to this one */
			$matrix[$column-1][$j] = array('class' => 'cup-grid-vertical-split-right');
		else
			/* draw vertical line */
			$matrix[$column-1][$j] = array('class' => 'cup-grid-vertical');
	}
	$matrix[$column][$currow] = array('class' => 'cup-grid-winner');

	if ($final['cupmatches_winner'] != CS_CUPS_TEAM_UNKNOWN)
	{
		$string = '';
		if ($final['cupmatches_winner'] == $final['team1_id'])
		{
			if (!empty($final['team1_name']))
				$string = $final['team1_name'];
//			else
//				$string = '#'.$match['cupmatches_seed1'];
		}
		else
		{
			if (!empty($final['team2_name']))
				$string = $final['team2_name'];
//			else
//				$string = '#'.$match['cupmatches_seed2'];
		}
		if (!empty($string))
			$matrix[$column][$currow]['content'] = $string; // TODO: extend with more info
	}
}

$colwidth = 0;
$linewidth = CS_CUPS_GRID_IMAGE_WIDTH;
$xwidth = CS_CUPS_GRID_IMAGE_WIDTH;
if ($cs_option['scores'] == 1)
	$xwidth += 20;
if ($width > 0)
{
	$colwidth = floor(ceil($width - ($rounds + 1)*$xwidth) / ($rounds + 1));
}

$start = !empty($_GET['start']) ? (int) $_GET['start'] : 0;
$max = !empty($_GET['max']) ? (int) $_GET['max'] : 0;  

$startcolumn = 1;
if ($start >= 1 && $start <= $rounds)
	$startcolumn = $start * $c;
$stoprounds = $rounds + 1;
if ($max >= 1 && $start + $max <= $rounds + 1)
	$stoprounds = $start + $max;

$grid = '';
$grid .= '<table class="cup-grid">'."\n";

for ($j = 1; $j <= $maxrows; $j++)
{
	$grid .= '<tr>'."\n";
	for ($i = $startcolumn; $i <= ($stoprounds * $c) - 1; $i++)
	{
		if (isset($matrix[$i][$j]))
		{
		  switch($matrix[$i][$j]['class'])
			{
			default: 
				$grid .= '<td class="'.$matrix[$i][$j]['class'].'">?'.$matrix[$i][$j]['class'].'</td>';
				break;
			case 'cup-grid-angle-down':
			case 'cup-grid-angle-up':
			case 'cup-grid-vertical-split-right':
			case 'cup-grid-vertical':
				$grid .= '<td class="'.$matrix[$i][$j]['class'].'">&nbsp;</td>';
				break;
			case 'cup-grid-winner':
			case 'cup-grid-team':
			case 'cup-grid-team-win':
			case 'cup-grid-team-loss':
				$grid .= '<td class="'.$matrix[$i][$j]['class'].'" style="width: '.$colwidth.'px;">';
				if (isset($matrix[$i][$j]['content']))
					$grid .= $matrix[$i][$j]['content'];
				else
					$grid .= '&nbsp;';
				$grid .= '</td>';
				break;
      case 'cup-grid-score':
			case 'cup-grid-score-win':
			case 'cup-grid-score-loss':
				$grid .= '<td align="right" class="'.$matrix[$i][$j]['class'].'">';
				if (isset($matrix[$i][$j]['content']))
					$grid .= $matrix[$i][$j]['content'];
				$grid .= '</td>';
				break;
			}
		}
		else
			$grid .= '<td>'.($i == 1 ? '&nbsp;' : '').'</td>';
	}
	$grid .= "\n".'</tr>'."\n";
}
$grid .= '</table>'."\n";

$datahtml['tree']['grid'] = $grid;
$datahtml['if']['standalone'] = false;
if (!$tree)
	$datahtml['if']['standalone'] = true;

if ($gridonly)
	$cache = $grid;
else
	$cache = cs_subtemplate(__FILE__, $datahtml, 'cups', 'viewtree');

if (function_exists('cs_datacache_load'))
	cs_datacache_create('cups', 'viewtree', $key, $cache, 0);

echo $cache;

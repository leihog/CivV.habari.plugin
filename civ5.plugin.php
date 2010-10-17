<?php

class Civ5 extends Plugin
{
    protected $steamId;
    
	/**
	 * function info
	 * Returns information about this plugin
	 * @return array Plugin info array
	 **/
	public function info()
	{
		return array (
			'name' => 'Civ5',
			'url' => 'http://www.gomitech.com',
			'author' => 'Leif Högberg',
			'authorurl' => 'http://www.gomitech.com',
			'version' => '1.0',
			'description' => 'Displays how much time you\'ve <strike>wasted</strike> spent playing Civilization V.',
			'license' => 'Apache License 2.0',
		);
	}

	public function action_init()
	{
		$this->add_template( 'civ5', dirname(__FILE__) . '/civ5.template.php' );
	}

	public function theme_civ5($theme)
	{
	    $options = Options::get( 'civ5__steamid', 'civ5__cache_expiry' );
        
	    if (!isset($options['civ5__steamid']) || empty($options['civ5__steamid']))
	    {
	        return;
	    }
	    
	    $this->steamId = $options['civ5__steamid'];
        $cacheName = "civ5_{$this->steamId}";

        if ( Cache::has($cacheName) )
        {
            $theme->civ5 = Cache::get($cacheName);
        }
        else
        {
    	    $data = array(
    	        'userid' => $this->steamId,
    	    	'time' => $this->getGameTime(),
    	        'achievements' => $this->getGameAchievements()
    	    );
    	    $theme->civ5 = $data;

    	    Cache::set($cacheName, $data, (60 * $options['civ5__cache_expiry']));
        }

	    return $theme->fetch( 'civ5' );
	}
	
	private function getGameStatsUrl($page)
	{
	    if (is_numeric($this->steamId))
	    {
	        $url = "http://steamcommunity.com/profiles/{$this->steamId}/{$page}?xml=1";
	    }
	    else
	    {
	        $url = "http://steamcommunity.com/id/{$this->steamId}/{$page}?xml=1";
	    }

	    return $url;
	}

	private function getGameAchievements()
	{
	    $data = new SimpleXMLElement(file_get_contents( $this->getGameStatsUrl('stats/CIvV') ));
	    if(!empty($data->error))
	    {
	        return false;
	    }

	    $cTotalAchievements = 0;
	    $cUnlockedAchievements = 0;
	    foreach($data->achievements->achievement as $achievement)
	    {
	        $attribs = $achievement->attributes();
	        if ($attribs['closed'] == 1)
	        {
	            $cUnlockedAchievements++;
	        }
	        
	        $cTotalAchievements++;
	    }

	    return array(
	        "total" => $cTotalAchievements,
	        "unlocked" => $cUnlockedAchievements
        );
	}

	private function getGameTime()
	{
	    $data = new SimpleXMLElement(file_get_contents( $this->getGameStatsUrl('games') ));
	    if(!empty($data->error))
	    {
	        return false;
	    }

	    foreach($data->games->game as $game)
	    {
	        if ($game->appID == 8930)
	        {
                return array(
                    "hoursLast2Weeks" => (string) $game->hoursLast2Weeks,
                    "hoursOnRecord" => (string )$game->hoursOnRecord
                );
	        }
	    }

	    return false;
	}

	/**
	 * Add our menu to the FormUI for plugins.
	 *
	 * @param array $actions Array of menu items for this plugin.
	 * @param string $plugin_id A unique plugin ID, it needs to match ours.
	 * @return array Original array with our added menu.
	 */
	public function filter_plugin_config( $actions, $plugin_id ) {
		if ( $plugin_id == $this->plugin_id ) { 
			$actions[]= 'Configure';
		}

		return $actions;
	}

	/**
	 * Handle calls from FormUI actions.
	 * Show the form to manage the plugin's options.
	 *
	 * @param string $plugin_id A unique plugin ID, it needs to match ours.
	 * @param string $action The menu item the user clicked.
	 */
	public function action_plugin_ui( $plugin_id, $action ) {
		if ( $plugin_id == $this->plugin_id && $action == 'Configure' )
		{
            $ui= new FormUI( 'civ5' );
            $elm = $ui->append( 'text', 'steamid', 'civ5__steamid', '<dl><dt>Steam ID</dt><dd>A 64bit numeric id or a string id (Same as id in your custom steam url).</dd></dl>' );
            $elm->add_validator( 'validate_required' );
            
            $elm = $ui->append( 'text', 'expiry', 'civ5__cache_expiry', '<dl><dt>Cache expiry time</dt><dd>The amount of minutes to cache the data collected from steam. 60 should be a good default.</dd></dl>' );
            $elm->add_validator( 'validate_required' );
            $elm->add_validator( 'validate_regex', '/^[0-9]*$/', 'Only numbers may be entered for quantity.' );

            $ui->append( 'submit', 'save', _t('Save') );
            $ui->out();
		}
	}
	
	
}
?>
      <div class="dialog">
        <div class="dialog_window" id="configuration">
             <ul id="configTabs">
              <li id="tabInt" class="toggleConfigTab left selTab">
                <a onclick='javascript:$.toggleConfigTab("#config_interface", "#tabInt")'>Interface</a>
	      </li>
              <li id="tabClient" class="toggleConfigTab">
                <a onclick='javascript:$.toggleConfigTab("#config_torClient", "#tabClient")'>Client</a>
	      </li>
              <li id="tabTor" class="toggleConfigTab">
                <a onclick='javascript:$.toggleConfigTab("#config_tor", "#tabTor")'>Torrent</a>
	      </li>
              <li id="tabFavs" class="toggleConfigTab">
                <a onclick='javascript:$.toggleConfigTab("#config_favorites", "#tabFavs")'>Favorites</a>
	      </li>
              <li id="tabFeeds" class="toggleConfigTab" 
                <a onclick='javascript:$.toggleConfigTab("#config_feeds", "#tabFeeds")'>Feeds</a>
	      </li>
              <li id="tabHideList" class="toggleConfigTab" 
                <a onclick='javascript:$.toggleConfigTab("#config_hideList", "#tabHideList")'>Hide List</a>                    
	      </li>
              <li id="tabOthers" class="toggleConfigTab right" 
                <a onclick='javascript:$.toggleConfigTab("#config_other", "#tabOthers")'>Other</a>
	      </li>
	     </ul>
            
            <form action="torrentwatch.php?setGlobals=1" id="config_form" name="config_form">
                <div class="config_form">
                    <div id="config_interface" class="configTab">
                        <div class="int_settings">
                            <div id="config_webui">
                                <label class="item select">Font Size:</label> 
                                <select name="webui" id="config_webui" onchange="changeFontSize(this.options[this.selectedIndex].value)">
                                    <option value="Small">
                                        Small
                                    </option>
                                    <option value="Medium" selected>
                                        Medium
                                    </option>
                                    <option value="Large">
                                        Large
                                    </option>
                                </select>
                            </div>
                            <div id="config_combinefeeds">
                                <label class="item checkbox" title="Combine all feeds into 1 list">
                                    Combine Feeds:
                                    <input type="checkbox" name="combinefeeds" value="1" <?php echo $combinefeeds; ?>/>
                                </label>
                            </div>
                            <div id="config_disable_hidelist">
                                <label class="item checkbox" title="Disable the hide list.">
                                    <input type="checkbox" name="dishidelist" value="1" <?php echo $dishidelist; ?>/>
                                Disable hide list:</label>
                            </div>
                        </div>
                    </div>
                    <div id="config_torClient" class="configTab hidden">
                        <div class="tor_client_settings">
                            <div id="config_torrentclient">
                                <label class="item select" title="Which torrent client to use">Client:</label> 
                                <select name="client" id="client" onchange="changeClient(this.options[this.selectedIndex].value)">
                                    <option value="Transmission" <?php echo $transmission; ?>>
                                        Transmission
                                    </option>
                                    <option value="folder" <?php echo $folderclient; ?>>
                                        Save torrent in folder
                                    </option>
                                </select>
                            </div>
                            <div id="config_folderclient">
                                <label class="item">File Extension</label> <input type="text" class="text" name="extension" 
                                 value="<?php echo $config_values['Settings']['Extension']; ?>"/>
                            </div>
                            <div id="config_downloaddir" title="Default directory to start items in">
                                <label class="item textinput">Download Dir:</label> <input type="text" class="text" name="downdir"
                                 value="<?php echo $config_values['Settings']['Download Dir']; ?>"/>
                            </div>
                            <div id="config_tr_host" title="Hostname">
                                <label class="item textinput">Hostname:</label> <input type="text" class="text" name="trhost"
                                 value="<?php echo $config_values['Settings']['Transmission Host']; ?>"/>
                            </div>
                            <div id="config_tr_port" title="Port">
                                <label class="item textinput">Port:</label> <input type="text" class="text" name="trport" 
                                 value="<?php echo $config_values['Settings']['Transmission Port']; ?>"/>
                            </div>
                            <div id="config_tr_user" title="Username">
                                <label class="item textinput">Username:</label> <input type="text" class="text" name="truser"
                                 value="<?php echo $config_values['Settings']['Transmission Login']; ?>"/>
                            </div>
                            <div id="config_tr_password" title="Password">
                                <label class="item textinput">Password:</label> <input type="password" class="password" name="trpass"
                                 value="<?php echo $config_values['Settings']['Transmission Password']; ?>"/>
                            </div>
                        </div>
                    </div>
                    <div id="config_tor" class="configTab hidden">
                        <div class="tor_settings">
                            <div id="config_deepdir">
                                <label class="item select" title="Save downloads in multi-directory structure">Deep Directories:</label>
                                 <select name="deepdir">
                                    <option value="Full" <?php echo $deepfull; ?>>Full Name</option>
                                    <option value="Title" <?php echo $deeptitle; ?>>Show Title</option>
                                    <option value="Title_Season" <?php echo $deepTitleSeason; ?>>Show Title + Season</option>
                                    <option value="0" <?php echo $deepoff; ?>>Off</option>
                                </select>
                            </div>
                            <div id="default_ratio">
                                <label class="item textinput" title="Set default Seed Ratio">Default Seed Ratio:</label>
                                <input type="text" class="text" name="defaultratio"
                                 value="<?php echo $config_values['Settings']['Default Seed Ratio']; ?>"/>
                            </div>
                            <div id="config_watchdir">
                                <label class="item textinput" title="Directory to look for new .torrents">Watch Dir:</label>
                                <input type="text" class="text" name="watchdir"
                                 value="<?php echo $config_values['Settings']['Watch Dir']; ?>"/>
                            </div>
                            <div id="config_savetorrent">
                                <label class="item checkbox" title="Save torrent to download directory">
                                    <input type="checkbox" name="savetorrents" value="1" <?php echo $savetorrent; ?>/> 
                                Save torrent files:</label>
                            </div>
                        </div>
                    </div>
                    <div id="config_favorites" class="configTab hidden">
                        <div class="fav_settings">
                            <div id="config_matchstyle">
                                <label class="item select" title="Type of filter to use">Matching Style:</label>
                                <select name="matchstyle">
                                    <option value="regexp" <?php echo $matchregexp; ?>>
                                        RegExp
                                    </option>
                                    <option value="glob" <?php echo $matchglob; ?>>
                                        Glob
                                    </option>
                                    <option value="simple" <?php echo $matchsimple; ?>>
                                        Simple
                                    </option>
                                </select>
                            </div>
                            <div>
                                <label class="item checkbox">
                                    <input type="checkbox" name="favdefaultall" value="1" <?php echo $favdefaultall; ?>
                                    title="Set feed to all when adding favorite. (This doesn't affect existing favorites)"/>
                                Set default feed to "All":</label>
                            </div>
                            <div id="config_require_epi_info">
                                <label class="item checkbox" 
                                    title="When enabled only shows with episode information (S01E12, 1x12, etc... ) wil be matched.">
                                    <input type="checkbox" name="require_epi_info" value="1" <?php echo $require_epi_info; ?>/>
                                Require episode info:</label>
                            </div>
                            <div id="config_verifyepisodes" title="Try not to download duplicate episodes">
                                <label class="item checkbox">
                                    <input type="checkbox" name="verifyepisodes" value="1" <?php echo $verifyepisode; ?>/>
                                Verify Episodes:</label>
                            </div>
                            <div>
                                <label class="item checkbox">
                                    <input type="checkbox" name="onlynewer" value="1" <?php echo $onlynewer; ?>/> 
                                Newer Episodes Only:</label>
                            </div>
                        </div>
                    </div>
                    <div id="config_other" class="configTab hidden">
                        <div class="other_settings">
                            <div>
                                <label class="item checkbox">
                                    <input type="checkbox" name="mailonhit" value="1" <?php echo $mailonhit; ?>/> 
                                Send mail on hit:</label>
                            </div>
                            <div id="email_address">
                                <label class="item">Email Address:</label>
                                <input type="text" name="emailAddress" class="text" 
                                title="Enter an email address here to send warnings and errors to."
                                value="<?php echo $config_values['Settings']['Email Address']; ?>"/>
                            </div>
                            <div id="smtp_server">
                                <label class="item">SMTP Server:</label>
                                <input type="text" name="smtpServer" class="text" 
                                value="<?php echo $config_values['Settings']['SMTP Server']; ?>"/>
                            </div>
                            <div id="script">
                                <label class="item">Script:</label>
                                <input type="text" class="text" readonly="readonly"
                                title="Configured script to run on certain events. (Read doc/script.txt for more info)."
                                value="<?php echo $config_values['Settings']['Script']; ?>"/>
                            </div>
                            <div id="tz">
                                <label class="item">TimeZone:</label>
                                <input type="text" name="TZ" class="text"
                                title="Set your TimeZone (Default UTC). See http://nl2.php.net/manual/en/timezones.php for a list of supported timezones."
                                value="<?php echo $config_values['Settings']['TimeZone']; ?>"/>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="buttonContainer">
                    <a class="submitForm button" id="Save" href="#" name="Save">Save</a> 
                </div>
            </form>
            <div id="config_feeds" class="configTab hidden">
                <div id="addFeed">
                    <form action="torrentwatch.php?updateFeed=1" class="feedform">
                    <a class="submitForm button" id="Add" href="#">Add</a>
                    <label class="item">Add feed:</label>
                    <input type="text" class="feed_link" name="link">
                    </form>
                </div>
                <div id="feedItemTitles">
                    <div id="feedNameUrl">
                        <label class="item">Name</label>
                        <label class="item hidden">Link</label>
                    </div>
                    <label class="item">Ratio</label>
                </div>
                <?php if(isset($config_values['Feeds'])): ?>
                    <?php foreach($config_values['Feeds'] as $key => $feed): ?>
                        <div id="feedItem_<?php echo $key; ?>"class="feeditem">
                              <form action="torrentwatch.php?updateFeed=1" class="feedform">  
                              <input type="hidden" name="idx" value="<?php echo $key; ?>">
                              <input class="feed_name" type="text" name="feed_name"
                                    title="<?php echo $feed['Link']; ?>" value="<?php echo $feed['Name']; ?>"</input>
                              <input class="feed_url hidden" type="text" name="feed_link"
                                    title="<?php echo $feed['Name']; ?>" value="<?php echo $feed['Link']; ?>"</input>
                              <input class="seed_ratio" type="text" name="seed_ratio" title="Set default seed ratio for this feed."
                                    value="<?php echo $feed['seedRatio']; ?>"</input>
                              <a class="submitForm button" id="Delete" href="#feedItem_<?php echo $key; ?>">Del</a>
                              <a class="submitForm button" id="Update" href="#">Upd</a>
                              </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                <div id="showURL">
                    <label id="showURL" class="item">Show feed link</label>
                    <input id="showURL" type="checkbox" onClick="$.toggleFeedNameUrl(<?php echo $key; ?>)"
                        title="Toggle between name and link input fields">
                    </input>
                </div>
            </div>
	    <form action="torrentwatch.php?delHidden=1" id="hidelist_form" name="hidelist_form">
		    <div id="config_hideList" class="configTab hidden">
                        <ul class="hidelist">
                        <?php if($config_values['Hidden']): ?>
                        <?php ksort($config_values['Hidden'], SORT_STRING); ?>
                        <?php foreach($config_values['Hidden'] as $key => $item): ?>
                            <li>
                                <label class="item checkbox">
                                <input type="checkbox" name="unhide[]" value="<?=$key?>"/>
                                <?php echo $key; ?></label>
                            </li>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <li><h2 style='color: red; text-align: center'>You did not hide any shows.</h2></li>
                        <?php endif; ?>
                        </ul>
		</div>
                        <div class="buttonContainer">
                            <a class="submitForm button" id="Unhide" href="#">Unhide</a>
                        </div>
	    </form>
	    <div id='linkButtons' class="buttonContainer">
		<a class='toggleDialog button close' href='#'>Close</a> 
	    </div>
        </div>
      </div>

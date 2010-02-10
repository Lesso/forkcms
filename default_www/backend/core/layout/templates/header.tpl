{include:file='{$BACKEND_CORE_PATH}/layout/templates/head.tpl'}
<body id="{$MODULE}" class="{$ACTION}">
	{option:debug}<div id="debugnotify">Debug mode</div>{/option:debug}
	<table border="0" cellspacing="0" cellpadding="0" id="encloser">
		<tr>
			<td>
				<div id="headerHolder">
					<table cellspacing="0" cellpadding="0" id="header">
						<tr>
							<td id="siteTitle" width="266">

								<table border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td>
											<h1>
												<a href="/">{$SITE_TITLE|truncate:20}</a>
											</h1>
										</td>
										{option:workingLanguages}
										<td>
											<select id="workingLanguage">
												{iteration:workingLanguages}
													<option{option:workingLanguages.selected} selected="selected"{/option:workingLanguages.selected} value="{$workingLanguages.abbr}">{$workingLanguages.label|ucfirst}</option>
												{/iteration:workingLanguages}
											</select>
										</td>
										{/option:workingLanguages}
									</tr>
								</table>
							</td>
							<td id="navigation">
								{$var|getmainnavigation}
							</td>
							<td id="user">
								<ul>
									<li class="settings">
										<a href="{$var|geturl:'index':'settings'}" class="button linkButton icon iconSettings">
											<span><span><span>{$lblSettings|ucfirst}</span></span></span>
										</a>
									</li>
									<li>
										<table border="0" cellspacing="0" cellpadding="0">
											<tr>
												<td><a class="user" href="{$authenticatedUserEditUrl}">{$authenticatedUserNickname}</a></td>
												<td><a href="{$authenticatedUserEditUrl}"><img src="{$FRONTEND_FILES_URL}/backend_users/avatars/32x32/{$authenticatedUserAvatar}" width="24" height="24" alt="{$authenticatedUserNickname}" class="avatar"></a></td>
												<td><a href="{$var|geturl:'logout':'authentication'}">{$lblSignOut|ucfirst}</a>
											</tr>
										</table>
									</li>
								</ul>
							</td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
		<tr>
			<td id="container">
				<div id="main">
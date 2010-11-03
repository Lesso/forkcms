<?php require_once '_head.php'; ?>
<body>
	<div id="container">

		<?php require_once '_header.php'; ?>
		<?php require_once '_toc.php'; ?>

		<div class="id1">
			<div class="content">

				<p class="intro">Welcome to the documentation of Fork CMS. You'll find everything you need here to get your website up and running quickly. Have fun!</p>

				<hr />

				<ol>
					<li><a href="index.php#whatis">What is Fork CMS?</a></li>
					<li><a href="index.php#download">Download</a></li>
					<li><a href="index.php#why">Why?</a></li>
					<li><a href="index.php#sysreq">System requirements</a></li>
					<li><a href="index.php#installation">Installation</a></li>
					<li><a href="index.php#bugReports">Bug reports</a></li>
					<li><a href="index.php#discussion">Discussion</a></li>
					<li><a href="index.php#thanks">Thanks!</a></li>
				</ol>

				<h3 id="whatis">What is Fork CMS?</h3>

 				<p>Fork CMS in a nutshell: Fork enables developers to deliver kick-ass websites. Marketing and communication managers to edit every part of their website using an easy interface. It comes with good defaults and various pre-built functionalities including a blog module and Google Analytics integration.</p>

				<h3 id="download">Download</h3>

				<p>Download Fork CMS from the <a href="http://fork-cms.be">website</a>.</p>

				<h3 id="why">Why should I use Fork CMS?</h3>

				<ul>
					<li><strong>Killer feature</strong>: finally a CMS that's easy to understand for your clients: they'll get the hang of using Fork quickly, since the interface is so easy and intuitive. You won't have to spend your days providing support.</li>
					<li>Fork is built using the <a href="http://www.spoon-library.com/">Spoon</a> PHP5 library. Using Spoon is easy: it's like taking candy from a baby. Spoon stands for speed, both in page execution and coding agility.</li>
					<li>All javascript has been written using jQuery, the best Javascript library in town</li>
					<li>Fork CMS has been built up from the ground to deliver speedy, performant websites. Who doesn't like speed?</li>
				</ul>

				<h3 id="sysreq">System requirements</h3>

				<p>After downloading Fork CMS, you'll have to install it. Typically you'll develop on a local copy before putting the website online. First, make sure your server matches the system requirements:</p>

				<ul>
					<li>PHP 5.2 or higher</li>
					<li>The following PHP extensions should be installed and enabled: cURL, SimpleXML, SPL, PDO, PDO MySQL driver, mb_string, iconv, GD2</li>
					<li>MySQL 5.0</li>
					<li>Apache 2.0 with mod_rewrite enabled</li>
				</ul>

				<p>Please consult the <a href="sysreq.php">detailed system requirements</a> for more information.</p>

				<h3 id="installation">Installation</h3>

				<p>Point your localhost (e.g. <code>myforksite.local</code>) to the default_www path e.g. if your website lives in <code>/Users/accountname/Sites/mywebsite</code>, point your server to <code>/Users/accountname/Sites/mywebsite/default_www</code>.</p>

				<p>Visit <code>&lt;your-domain&gt;/install</code> (e.g. http://myforksite.local/install) to start the installation.</p>

				<p>Have fun with your project!</p>

				<h3 id="bugReports">Bug reports</h3>

				<p>If you encounter any bugs, please create a new issue using the <a href="http://forkcms.lighthouseapp.com/projects/61890-fork-cms/overview">bugtracker</a>.</p>

				<h3 id="discussion">Discussion</h3>

				<p>If you encounter a problem, or want to discuss Fork CMS, visit the <a href="https://forkcms.tenderapp.com/discussions/general">Fork CMS support forum</a>.</p>

			</div>
		</div>

		<div class="hr"><hr /></div>

		<p class="secondaryContent">&copy; Fork CMS by <a href="http://www.netlash.com">Netlash</a> and contributors. Read the Fork CMS license (See file named LICENSE distributed with every Fork) for usage details.</p>

	</div>

	<script type="text/javascript">
		SyntaxHighlighter.config.clipboardSwf = 'js/syntax/scripts/clipboard.swf';
		SyntaxHighlighter.all();
	</script>

</body>
</html>
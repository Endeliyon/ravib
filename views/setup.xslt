<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  PHP extensions template
//
//-->
<xsl:template match="php_extensions">
<p>De volgende PHP extensies ontbreken:</p>
<ul>
<xsl:for-each select="extension"><li><xsl:value-of select="." /></li></xsl:for-each>
</ul>
<p>Installeer en/of activeer deze en ververs deze pagina.</p>

<div class="btn-group">
<a href="/{/output/page}" class="btn btn-default">Ververs</a>
</div>
</xsl:template>

<!--
//
//  MySQL client template
//
//-->
<xsl:template match="mysql_client">
<p>The MySQL command line client is missing. Install it and refresh this page.</p>

<div class="btn-group">
<a href="/{/output/page}" class="btn btn-default">Refresh</a>
</div>
</xsl:template>

<!--
//
//  Database settings template
//
//-->
<xsl:template match="db_settings">
<p>Voer de database-instellingen in settings/website.conf in en ververs deze pagina.</p>

<div class="btn-group">
<a href="/{/output/page}" class="btn btn-default">Ververs</a>
</div>
</xsl:template>

<!--
//
//  Create database template
//
//-->
<xsl:template match="create_db">
<xsl:call-template name="show_messages" />

<p>Voer de MySQL root credentials in om een database en een gebruiker voor deze website aan te maken.</p>
<form action="/{/output/page}" method="post">
<label for="username">Gebruikersnaam:</label>
<input type="text" id="username" name="username" value="{username}" class="form-control" />
<label for="password">Wachtwoord:</label>
<input type="password" id="password" name="password" class="form-control" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Create database" class="btn btn-default" />
</div>
</form>
</xsl:template>

<!--
//
//  Import SQL template
//
//-->
<xsl:template match="import_sql">
<xsl:call-template name="show_messages" />

<p>De volgende stap is het importeren van het bestand database/mysql.sql in de database.</p>
<form action="/{/output/page}" method="post">
<input type="submit" name="submit_button" value="Importeer SQL" class="btn btn-default" />
</form>
</xsl:template>

<!--
//
//  Update database template
//
//-->
<xsl:template match="update_db">
<p>De huidige database is verouderd en dient te worden bijgewerkt.</p>
<form action="/{/output/page}" method="post">
<input type="submit" name="submit_button" value="Database bijwerken" class="btn btn-default" />
</form>
</xsl:template>

<!--
//
//  Update PIA template
//
//-->
<xsl:template match="update_pia">
<xsl:call-template name="show_messages" />
<p>De huidige PIA regels zijn verouderd en dienen te worden bijgewerkt.</p>
<form action="/{/output/page}" method="post">
<input type="submit" name="submit_button" value="PIA bijwerken" class="btn btn-default" />
</form>
</xsl:template>

<!--
//
//  Credentials template
//
//-->
<xsl:template match="credentials">
<xsl:call-template name="show_messages" />

<form action="/{/output/page}" method="post" onSubmit="javascript:hash_passwords(); return true;">
<label for="username">Kies een gebruikersnaam:</label>
<input type="username" id="username" name="username" value="{username}" class="form-control" />
<label for="password">Kies een wachtwoord voor deze gebruiker:</label>
<input type="password" id="password" name="password" class="form-control" autofocus="autofocus" />
<label for="repeat">Herhaal het gekozen wachtwoord:</label>
<input type="password" id="repeat" name="repeat" class="form-control" />

<input type="hidden" id="password_hashed" name="password_hashed" value="no" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Set password" class="btn btn-default" />
</div>
</form>
</xsl:template>

<!--
//
//  Done template
//
//-->
<xsl:template match="done">
<p>Klaar! U kunt nu inloggen met de zojuist ingestelde logingegevens.</p>
<p>Vergeet niet de setup-module te deactiveren door deze te verwijderen uit settings/public_modules.conf.</p>

<div class="btn-group">
<a href="/" class="btn btn-default">Doorgaan</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>RAVIB setup</h1>
<xsl:apply-templates />
</xsl:template>

</xsl:stylesheet>

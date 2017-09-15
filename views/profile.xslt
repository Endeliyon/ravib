<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post" onSubmit="javascript:hash_passwords(); return true;">
<label for="fullname">Naam:</label>
<input type="text" id="fullname" name="fullname" value="{fullname}" class="form-control" />
<label for="email">E-mailadres:</label>
<input type="text" id="email" name="email" value="{email}" class="form-control" />
<label for="current">Huidige wachtwoord:</label>
<input type="password" id="current" name="current" class="form-control" />
<label for="password">Nieuw wachtwoord:</label> <span class="blank" style="font-size:10px">(wordt niet gewijzigd indien leeg)</span>
<input type="password" id="password" name="password" class="form-control" onKeyUp="javascript:password_strength(this, 'username')" />
<label for="repeat">Herhaal wachtwoord:</label>
<input type="password" id="repeat" name="repeat" class="form-control" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Profiel bijwerken" class="btn btn-default" />
<xsl:if test="cancel">
<a href="/{cancel/@page}" class="btn btn-default"><xsl:value-of select="cancel" /></a>
</xsl:if>
</div>

<input type="hidden" id="username" value="{username}" />
<input type="hidden" id="password_hashed" name="password_hashed" value="no" />
</form>

<h2>Recentelijke accountactiviteit</h2>
<table class="table table-striped table-xs">
<thead>
<tr>
<th>IP-adres</th>
<th>Tijdstip</th>
<th>Activiteit</th>
</tr>
</thead>
<tbody>
<xsl:for-each select="actionlog/log">
<tr>
<td><xsl:value-of select="ip" /></td>
<td><xsl:value-of select="timestamp" /></td>
<td><xsl:value-of select="message" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Gebruikersprofiel</h1>
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>

<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="sessions">
<table class="table table-striped table-hover table-condensed">
<thead>
<tr><th>IP-adres</th><th>Verloopdatum</th><th>Naam</th></tr>
</thead>
<tbody>
<xsl:for-each select="session">
<tr class="click {owner}" onClick="javascript:document.location='/session/{@id}'">
<td><xsl:value-of select="ip_address" /></td>
<td><xsl:value-of select="expire" /></td>
<td><xsl:value-of select="name" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/session" method="post">
<input type="hidden" name="id" value="{session/@id}" />

<label for="name">Naam:</label>
<input type="text" id="name" name="name" value="{session/name}" class="form-control" />
<label for="ipaddr">IP-adres:</label>
<input id="ipaddr" class="form-control" disabled="disabled" value="{session/ip_address}" />
<label for="expire">Verloopdatum:</label>
<input id="expire" class="form-control" disabled="disabled" value="{session/expire}" />

<div class="btn-group">
<input type="submit" name="submit_button" value="Sessie bijwerken" class="btn btn-default" />
<a href="/session" class="btn btn-default">Afbreken</a>
<input type="submit" name="submit_button" value="Sessie verwijderen" class="btn btn-default" onClick="javascript:return confirm('VERWIJDEREN: Weet u het zeker?')" />
</div>
</form>
</xsl:template>

<!--
//
//  Result template
//
//-->
<xsl:template match="result">
<p><xsl:value-of select="." /></p>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Sessiebeheer</h1>
<xsl:apply-templates select="sessions" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>

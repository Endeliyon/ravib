<?xml version="1.0" ?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:include href="../banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<p>Deze Privacy Impact Assessment (PIA) tool is een implementatie van de PIA v1.2 zoals <a href="http://www.norea.nl/download/?id=522">uitgegeven door NOREA</a>.</p>
<table class="table table-condensed table-striped">
<thead>
<tr>
<th>Naam</th>
<th>Datum</th>
<th class="start"></th>
</tr>
</thead>
<tbody>
<xsl:for-each select="case">
<tr>
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="date" /></td>
<td><a href="/pia/pia/{@id}" class="btn btn-xs btn-primary">Start</a> <a href="/{/output/page}/{@id}" class="btn btn-xs btn-default">Bewerken</a></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<a href="/{/output/page}/new" class="btn btn-default">Nieuwe casus</a>
</div>
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="case/@id">
<input type="hidden" name="id" value="{case/@id}" />
</xsl:if>

<label for="name">Naam:</label>
<input type="text" id="name" name="name" value="{case/name}" class="form-control" />
<label for="date">Datum:</label>
<input type="text" id="date" name="date" value="{case/date}" class="form-control datepicker" />
<label for="description">Omschrijving:</label>
<textarea id="description" name="description" class="form-control"><xsl:value-of select="case/description" /></textarea>

<div class="btn-group">
<input type="submit" name="submit_button" value="Casus opslaan" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Afbreken</a>
<xsl:if test="case/@id">
<input type="submit" name="submit_button" value="Casus verwijderen" class="btn btn-default" onClick="javascript:return confirm('VERWIJDEREN: Weet je het zeker?')" />
</xsl:if>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>PIA casussen</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
